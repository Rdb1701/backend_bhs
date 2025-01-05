<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // abort_if(Auth::user() && Auth::user()->role == "user" , Auth::logout());
        // return $next($request);

        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user is NOT admin/owner OR is inactive
            if (!in_array($user->role, ['owner', 'admin'])) {
                // Log out the user and clear the session
                Auth::logout();
                session()->flush();

                // Store flash message before redirect
                session()->flash('notification', [
                    'type' => 'warning',
                    'message' => 'Your account is inactive or unauthorized. Please contact the administrator.'
                ]);

                return redirect()->route('filament.admin.auth.login');
            }
        }
        
        return $next($request);
    }
}