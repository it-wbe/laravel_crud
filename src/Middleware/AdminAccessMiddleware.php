<?php
namespace Wbe\Crud\Middleware;
use Closure;
use Wbe\Crud\Models\ContentTypes\User;
use Wbe\Crud\Models\Roles\Permissions;
use Wbe\Crud\Models\Roles\Role;
use Wbe\Crud\Models\Log\AdminLog;

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
                if ($request_segments[1] == 'login' || $request_segments[1] == 'logout' || $request_segments[1] == 'password'|| $request_segments[1] == 'setlocale'||$request_segments[1]=="autocomplete") {
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
            if($request_segments[1]=='account'){
                return $next($request);
            }

            $rights = null;
            $log_rights = null;
            if($request->has('modify')) {
                $rights = 'w';
                $log_rights = "modification";
            }elseif($request->has('insert')){
                $rights = 'w';
                $log_rights = "insert";
            }elseif($request->has('delete')){
                $log_rights = "delete";
                $rights = 'd';
            }elseif($request->has('show')){
                $log_rights = "show";
                $rights = 'r';
            }

            $path = $request->path();
            if($request_segments[1]=='crud'){ /// crud content type
                $a =0 ;
                    $a = count($request_segments)-1;
                $temp  ='admin/crud/grid/'.$request_segments[$a];

            /// log only for crud content type
                if(!is_null($log_rights)){
                    Log::write(\Auth::guard('admin')->user()->id,$log_rights,$request_segments[$a]);
                }

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
            if(\Auth::guard('admin')->user()->role->HasPermission($temp,$rights)){
                return $next($request);
            }
            else{
                \Session::flash('alert-warning', __('crud::common.dont_have_access'));
                return redirect()->back();
            }
                return $next($request);
            }
        return $next($request);
    }
}

class Log{
    public static function write($user_id,$action,$content_type_id){
        AdminLog::insert(['user_id'=>$user_id,'action'=>$action,'content_type_id'=>$content_type_id,'action_date'=>date('Y-m-d H:i:s')]);
    }
}