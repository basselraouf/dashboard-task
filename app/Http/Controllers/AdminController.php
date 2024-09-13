<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Exception;
use GuzzleHttp\Psr7\Message;
use Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct(){
        $this->middleware('AdminAuthGuard',['except'=>['login','register']]);
    }
    public function register(Request $request){
        try{
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:admins|max:255',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*?&#]/']
                ],[
                    'password.min' => 'The password must be at least 8 characters.',
                    'password.regex' => 'The password must contain an uppercase letter, a lowercase letter, a number, and a special character.',
            ]);

                $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            return response()->json(['message'=> 'New Admin Created Successfully','admin'=>$admin]);

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
            $credentials = $request->only('email','password');
            $token = Auth::guard('admin-api')->attempt($credentials);
            if(!$token){
                return response()->json(['error'=>'Wrong Credentials']);
            }
            $admin = Auth::guard('admin-api')->user();
            $admin->token = $token;
            return response()->json([
                'message' => 'Admin logged in successfully',
                'data' => [
                    'admin'=>$admin,
                ]
            ]);
        }catch(Exception $e){
            return response()->json([
                'message'=>$e->getMessage()
                ]);
        }
    }

    public function logout(){
        Auth::logout();
        return response()->json(['message'=>'Admin logged out successfully']);
    }

    public function refresh(){
        try {
            $refreshToken = Auth::guard('admin-api')->refresh();
            return response()->json([
                'message'=>'Token refreshed successfully',
                'refresh token' => $refreshToken
            ]);
        }catch(Exception $e){
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }
}
