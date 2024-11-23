<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('admin') || !session()->has('admin_token')) {
            // If the admin session or token doesn't exist, redirect to the login page
            return redirect('/admin-login')->withErrors(['error' => 'Please log in to access the dashboard.']);
        }

        // Retrieve the stored token
        $sessionToken = session('admin_token');
        $admin = session('admin');

        // Verify if the token matches the one in the database
        if ($admin->token !== $sessionToken) {
            // Token mismatch, redirect to login
            session()->forget(['admin', 'admin_token']);
            return redirect('/admin-login')->withErrors(['error' => 'Invalid session. Please log in again.']);
        }

        return $next($request);
    }
}
