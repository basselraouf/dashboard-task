<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try{
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response()->json(['message'=> 'New user Created Successfully','user'=>$user]);

        }catch(Exception $e){
            return response()->json([
            'error'=>'An error occurred while registering',
            'message'=>$e->getMessage()
            ]);
        }
    }

    public function login(Request $request){
        try{
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('API Token')->plainTextToken;

                return response()->json([
                    'message' => 'User logged in successfully',
                    'data' => [
                        'user' => $user,
                        'token' => $token,
                    ]
                ], 200);
            }
            return response()->json(['error' => 'Wrong Credentials'], 401);
        }catch(Exception $e){
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function logout(Request $request)
    {
        try{
            $request->user()->tokens()->delete();

            return response()->json(['message' => 'User logged out successfully'], 200);
        }catch(Exception $e){
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }
}
