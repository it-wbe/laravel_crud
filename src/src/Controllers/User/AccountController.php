<?php

namespace Wbe\Crud\Controllers\User;

use Illuminate\Http\Request;

//use App\Http\Requests;
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
        $user = User::get(\Auth::user()->id);
        $user->name = $request->name;
        $user->email = $request->email;
        \DB::table('users')
            ->where('id', \Auth::user()->id)
            ->update(
                ['name' => $request->name],
                ['email' => $request->email]
            );

        return redirect()->action(
            'Backend\Crud\User\AccountController@index'
        );
    }
}
