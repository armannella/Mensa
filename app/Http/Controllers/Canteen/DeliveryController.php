<?php

namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Reserve;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DeliveryController extends Controller
{
    public function showDeliveryPage(Menu $menu , Reserve $reserve = null){

        if(!Gate::allows('isMenuForTheCanteen' , [$menu , Auth::user()->canteen])){
            abort(403 , 'this menu is not for ur Canteen');
        }

        if(!$menu->canBeServed()){
            abort(403 , 'this menu is not open for delivery');
        }
        return view('mensa.delivery',compact('menu' , 'reserve'));
    }

    public function DelieverReserve(Request $request , Menu $menu){

        
        $request->validate([
            'barcode' => 'required|string'
        ]);

        $result = Reserve::decrypteBarcode($request->barcode);

        if ($result['status'] === 'error') {
            return back()->withErrors(['barcode' => $result['message']]);
        }

        $reserve = $result['reserve'];

        if(!Gate::allows('isReserveForCanteen' , $reserve)){
            return back()->withErrors(['barcode' => 'This meal is not for this Canteen!']);
        }
        if ($reserve->status->value === 'delivered') { 
            return back()->withErrors(['barcode' => 'This meal has already been delivered!']);
        }

        $reserve->update(['status' => 'delivered']);

        return redirect()->route('mensa.delivery.show', [
            'menu' => $reserve->menu_id, 
            'reserve' => $reserve->id
        ])->with('success', 'Meal delivered successfully for student: ' . $reserve->student->user->name);
    }



    
    
}
