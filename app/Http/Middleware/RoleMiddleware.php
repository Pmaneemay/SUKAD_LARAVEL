<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{ 
    public function handle($request, Closure $next, ...$roles)
    {
        // Check if the user is authenticated and their role is in the list of roles
        if (Auth::check()) {
            if (in_array(Auth::user()->role, $roles)) {
                return $next($request); // Proceed with the request
            }
        }

        // Redirect to Home page if the user is not authenticated or doesn't have a valid role
        return redirect('/'); 
    }
}

