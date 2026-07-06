<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Ersu
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login'); 
        }

        
        if (Auth::user()->role !== UserRole::ADMIN) {
            
            abort(403, 'You cannot access this page, you are not a ADMIN, amico!');
        }

        return $next($request);
    }
}
