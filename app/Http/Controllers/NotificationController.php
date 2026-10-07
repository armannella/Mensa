<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead(string $id){
        $notif = Auth::user()->notifications()->findOrFail($id);
        $notif->markAsRead(); 
        
        return back();
    }

    public function markAllAsRead(){
        Auth::user()->unreadNotifications->markAsRead();
        
        return back();
    }
}