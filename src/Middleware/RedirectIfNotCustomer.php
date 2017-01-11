<?php

namespace Wbe\Crud\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotCustomer
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @param  string|null  $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = 'customer')
	{
	    if (!Auth::guard($guard)->check()) {
	        return redirect('/login');
	    }

	    return $next($request);
	}
}