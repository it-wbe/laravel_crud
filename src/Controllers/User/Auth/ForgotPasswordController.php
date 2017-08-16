<?php

namespace Wbe\Crud\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;

use Auth;
class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('admin.guest');
    // }
    //
    public function __construct()
    {
        $this->middleware('admin');
    }

//    send email with token
    public function sendResetLinkEmail(Request $request)
    {
        $email = Auth::guard()->user()->email;
        $response = $this->broker()->sendResetLink(['email'=>$email]);

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }


    public function showLinkRequestForm()
    {
        $email = Auth::guard()->user()->email;
        return view('crud::user.auth.passwords.email')->withEmail($email);
    }

    public function broker()
    {
        return Password::broker('admins');
    }
}
