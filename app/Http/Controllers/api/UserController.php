<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Login api for user login to access api token

    public function login(Request $request) {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json(['status' => 0,'message' => $validation->errors()], 403);
        }
        
    
        $user = User::where('email', $request->email)->first();
    
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 0,'message' => 'invalid email and password'], 401);
        }
        return response()->json(['status' => 1,'message' => 'success', 'token' => $user->createToken($request->device_name)->plainTextToken], 200);
        
    }
}
