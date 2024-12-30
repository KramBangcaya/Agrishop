<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class MobileController extends Controller
{
    //
    public function authenticate(Request $request)
    {
        // dd($request);
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        // dd($request);
        // Attempt authentication using Laravel's built-in Auth
        if (Auth::attempt($credentials)) {
            // User authenticated, redirect or return success response
            // return redirect()->intended('dashboard'); // Example redirect
            return response()->json(['success' => 'Valid credentials'], 200);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    public function Registration(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:users,name,' . $request->id,
            'email' => 'required|email|unique:users,email,' . $request->id,
            'password' => 'required|sometimes',
            'lastname' => 'required|string',
            'middle_initial' => 'nullable|string|max:2',
            'date_of_birth' => 'required|date',
            'contact_number' => 'required|string|digits:11',
            'telephone_number' => 'nullable|string|digits:7',
            // 'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        // $photos = request()->file('photos');
        // $paths = [];

        // if (!empty($photos)) {
        //     foreach ($photos as $photo) {
        //         $filename = time() . '.' . $photo->getClientOriginalExtension();
        //         $path = $photo->storeAs('Personal_Info_Photo', $filename, 'public');
        //         $paths[] = $path; // Add path to the array
        //     }
        // }

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            // 'password' => Hash::make($data['password']),
            'password' => $request['password'],
            'lastname' => $request['lastname'],
            'middle_initial' => $request['middle_initial'],
            'date_of_birth' => $request['date_of_birth'],
            'contact_number' => $request['contact_number'],
            'telephone_number' => $request['telephone_number'],
            // 'photos' => json_encode($paths), // Store photo paths as JSON
        ]);

        if ($user->save()) {
            return response()->json(['success' => 'Valid credentials'], 200);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }
}
