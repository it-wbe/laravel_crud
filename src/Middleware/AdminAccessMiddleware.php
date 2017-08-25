<?php

namespace Wbe\Crud\Middleware;

use Closure;


class AdminAccessMiddleware
{
    /**
     * Closing access via role and permissions
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $request_segments = \Request::segments();
        //dd($request_segments);
        if(!empty($request_segments[0]) && !empty($request_segments[1]) && $request_segments[0] == 'admin') {
            if($request_segments[1] == 'crud' || $request_segments[1] == 'fields_descriptor') {
                if($request_segments[1] == 'fields_descriptor' && \Gate::forUser(\Auth::guard('admin')->user())->denies('access-field-descriptor')) {

                    abort(403, 'Access denie');
                }

                if (\Gate::forUser(\Auth::guard('admin')->user())->allows('edit-crud-system-content-type', $request_segments[3])) {

                    return $next($request);
                } elseif (\Gate::forUser(\Auth::guard('admin')->user())->allows('access-content-type', $request_segments[3])) {

                    return $next($request);
                } else  abort(403, 'Access denie');
            }
            if($request_segments[1]=='login'||$request_segments[1]=='logout'||$request_segments[1]=='password')
            {
                return $next($request);
            }

            if(!\Auth::guard('admin')->user()){
                abort(403, 'Access denie');
            }
        }
        elseif(!empty($request_segments[0])&& $request_segments[0]=='admin')
        {
            if(!\Auth::guard('admin')->user()){
              return  redirect(route('admin.login'));
            }
        }
        return $next($request);
    }
}
