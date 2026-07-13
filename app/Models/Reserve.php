<?php

namespace App\Models;

use App\Enums\ReserveStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reserve extends Model
{
    protected $fillable = ['menu_id' , 'student_id' , 'price','status' , 'secret_barcode'];

    protected function casts(): array
    {
        return [
            'status' => ReserveStatus::class,
            'price' => 'decimal:2'
        ];
    }

    public function menu(){
        return $this->belongsTo(Menu::class);
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function foods(){
        return $this->belongsToMany(Food::class,'reserve_detail')->withTimestamps();
    }
    public function feedback(){
        return $this->hasOne(Feedback::class);
    }

    ////////////////////////////////////

    public function canBeCancelled(){
        if (env('APP_DEMO_MODE', false)) {
            return true;
        }

        $menu = $this->menu ;
        $configs = Config::getAll();
        $deadline = $menu->getStartDateTime()->copy()->subHours($configs['cancel_time']);
        return now()->isBefore($deadline);

    }

    public function canBeReviewed(){
        if (env('APP_DEMO_MODE', false)) {
            return true;
        }
        
        $menu = $this->menu ;
        $start = Carbon::parse($this->updated_at);
        $end = $menu->getEndDateTime()->copy()->addHours(3);
        return now()->isBetween($start , $end);
    }

    public function getFinePercent(){
        $menu = $this->menu ;
        $configs = Config::getAll();
        if($menu->canBeReserved()){
            return 0 ;
        }
        $fine = (int) $configs['cancel_fine'];
        return $fine ;
    }

    public function encrypteBarcode()
    {
        $secret = $this->secret_barcode;
        $timeWindow = floor(time() / 60);
        
        $hash = hash_hmac('sha256', $timeWindow, $secret);
        
        $dynamicCode = str_pad(hexdec(substr($hash, 0, 6)) % 1000000, 6, '0', STR_PAD_LEFT);
        return $this->id . '-' . $dynamicCode;
    }

    public static function decrypteBarcode(String $scanned_barcode)
    {
        $parts = explode('-', $scanned_barcode);
        
        if (count($parts) !== 2) {
            return ['status' => 'error', 'message' => 'Wrong format of Barcode!'];
        }
        
        $reserveId = $parts[0];
        $scannedCode = $parts[1];
        
        $reserve = self::find($reserveId);
        
        if (!$reserve) {
            return ['status' => 'error', 'message' => 'There is no reserve with this barcode!'];
        }
        
        $secret = $reserve->secret_barcode;
        $currentTimeWindow = floor(time() / 60);
        
        $validCodes = [
            str_pad(hexdec(substr(hash_hmac('sha256', $currentTimeWindow, $secret), 0, 6)) % 1000000, 6, '0', STR_PAD_LEFT),
            str_pad(hexdec(substr(hash_hmac('sha256', $currentTimeWindow - 1, $secret), 0, 6)) % 1000000, 6, '0', STR_PAD_LEFT)
        ];
        
        if (in_array($scannedCode, $validCodes)) {
            return ['status' => 'success', 'reserve' => $reserve];
        }
        
        return ['status' => 'error', 'message' => 'Barcode is old/expired, please refresh it!'];
    }
}