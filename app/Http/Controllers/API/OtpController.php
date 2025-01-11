<?php
namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class OtpController extends Controller
{
    public function show($userId)
    {

        return view('auth.otp', compact('userId')); // OTP modal view
    }



    public function verify(Request $request, $userId)
    {

        // dd($userId);

        // Define the validation rules
        $validator = Validator::make($request->all(), [
            'otp' => 'required|numeric',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->route('otp.show', ['userId' => $userId])
                             ->with('error', 'Invalid OTP. Please try again.');
        }


        // Find the user
        $user = User::find($userId);

        // dd($user->otp);
        // dd($request->all());
        // Check if user exists and OTP is correct
        if ($user->otp !== (int) $request->otp) {
            // dd('wrong');
            return back()->withErrors(['otp' => 'The provided OTP is incorrect.'])->withInput(); // Return back with error
        }

        // dd('right');
        // Update the user's `date_login`
        $user->update(['date_login' => now()]);

        // Log in the user
        auth()->login($user);

        // dd(auth()->login($user));
        // Redirect the user
        return redirect()->intended('/home')->with('message', 'Your account has been successfully verified.');
    }




}

