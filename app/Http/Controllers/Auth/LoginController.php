<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle actions after a user is authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    protected function authenticated(Request $request, $user)
    {
        // Check if the `date_login` is null
        if (is_null($user->date_login)) {

            // Store the user ID in the session and log out the user temporarily
            session(['user_id' => $user->id]);
            // dd(session()->all());

            Auth::logout();

            // Redirect to OTP view with a message
            return redirect()->route('otp.show', ['userId' => $user->id])->with('message', 'Please verify your account using the OTP sent.');
        }


        // If `date_login` is not null, update it to the current timestamp
        $user->update(['date_login' => now()]);

        // Allow the user to proceed
        return redirect()->intended($this->redirectTo);
    }
}
