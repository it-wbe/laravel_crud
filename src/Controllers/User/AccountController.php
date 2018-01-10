<?php

namespace Wbe\Crud\Controllers\User;

use Illuminate\Http\Request;

//use App\Http\Requests;
use Illuminate\Support\Facades\Hash;
use Wbe\Crud\Models\ContentTypes\ContentType;
use Wbe\Crud\Models\ContentTypes\User;
use App\Http\Controllers\Controller;
use Auth;

class AccountController extends Controller
{
    public function index()
    {
        $user = User::get(\Auth::guard('admin','moderator')->user()->id);
        return view('crud::user.account', ['user' => $user]);
    }

     public function edit(Request $request) {
         $user = User::find(\Auth::guard('admin','moderator')->user()->id);
         if($user){
           $user->name = $request->name;
           $user->email = $request->email;
           if(isset($request->password)&&isset($request->password_confirm))
           {
                if($request->password==$request->password_confirm)
                {
                    $user->password = Hash::make($request->password);
                }
           }
           $user->save();
           return redirect()->action('\Wbe\Crud\Controllers\User\AccountController@index')->with('status','OK');
         }
     }

     public function settings(){
        $user =  User::find(\Auth::guard('admin','moderator')->user()->id);
         if(!empty($user->settings)) {
             $settings = unserialize($user->settings);
         }
         if (request()->isMethod('post')) {
             /// save only types with 1
             $a =collect(request()->get('content')['types']);
             $w = $a->filter(function ($key, $value) {
                 return $key>0;
             });
             $data = serialize($w->all());
             $user->settings = $data;
             $user->save();
             $settings = $w->all();
             request()->session()->flash('alert-success', 'settings saved!');
         }
         $types = ContentType::where('is_system', '=', '0')->get();
         return view('crud::user.settings', compact('settings', 'types'));
    }
}
