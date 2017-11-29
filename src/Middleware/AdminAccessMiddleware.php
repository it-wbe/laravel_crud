<?php
namespace Wbe\Crud\Middleware;
use Closure;
use Wbe\Crud\Models\ContentTypes\User;
use Wbe\Crud\Models\Roles\Permissions;
use Wbe\Crud\Models\Roles\Role;

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
        if(!empty($request_segments[0])&& $request_segments[0]=='admin') {
                if (!empty($request_segments[1])) {
                    if ($request_segments[1] == 'login' || $request_segments[1] == 'logout' || $request_segments[1] == 'password') {
                        return $next($request);
                    }
                    if($request_segments[1]=='setlocale'){
                      return $next($request);
                    }
                }elseif(empty($request_segments[1])){ // index admin
                    if(!empty(\Auth::guard('admin')->user())){
                    return $next($request);
                    }
                }
                if (!\Auth::guard('admin')->user()) {
                    return redirect(route('admin.login'));
                }

            if(empty(\Auth::guard('admin')->user())){

                return redirect()->back();
            }
            $rights = null;
            if($request->has('modify')||$request->has('insert')){
                $rights = 'w';
            }elseif($request->has('delete')){
                $rights = 'd';
            }elseif($request->has('show')){
                $rights = 'r';
            }

            $path = $request->path();
            if($request_segments[1]=='crud'){ /// crud content type
                $a =0 ;
                    $a = count($request_segments)-1;
                $temp  ='admin/crud/grid/'.$request_segments[$a];
            }elseif($request_segments[1]=='additional'){/// permissions for additional
                if($request_segments[2]=='roles'){
                    $temp = $request_segments[0].'/'.$request_segments[1].'/'.$request_segments[2];
                }elseif($request_segments[2]=='menu') {
                    $temp = $request_segments[0].'/'.$request_segments[1].'/'.$request_segments[2];
                }else{
                    $temp = $path;
                }
            }else{
                $temp = $path;
            }
            // dump($temp,$rights);
            if(\Auth::guard('admin')->user()->role->HasPermission($temp,$rights)){
                return $next($request);
            }
            else{
                \Session::flash('access', "You don't have access");
                return redirect()->back();
            }
                return $next($request);
            }
        return $next($request);
    }
}
