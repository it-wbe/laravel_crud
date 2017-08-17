<?php

namespace Wbe\Crud\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin.guest');
    }


    protected $redirectTo = 'admin';

    public function showResetForm(Request $request, $token = null)
    {
        return view('crud::user.auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
//        $email = $request->input('email');
//        if (property_exists($this, 'resetView')) {
//            return view($this->resetView)->with(compact('token', 'email'));
//        }
//        if (view()->exists('crud::user.auth.passwords.reset')) {
//            return view('crud::user.auth.passwords.reset')->with(compact('token', 'email'));
//        }
//        return view('crud::user.auth.password.reset')->with(compact('token', 'email'));
    }
}
