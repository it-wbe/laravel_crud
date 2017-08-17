<?php

namespace Wbe\Crud\Controllers\User;

use Illuminate\Http\Request;

//use App\Http\Requests;
use Illuminate\Support\Facades\Hash;
use Wbe\Crud\Models\ContentTypes\User;
use App\Http\Controllers\Controller;
use Auth;

class AccountController extends Controller
{
    public function index()
    {
        $user = User::get(\Auth::guard('admin')->user()->id);

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
}
