<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            "full_name" => "required",
            "bio" => "required|max:100",
            "username" => "required|min:3|unique:users,username|regex:/^[a-z0-9_.]+$/i",
            "password" => "required|min:6",
            "is_private" => "boolean"
        ]);

        if($validator->fails()){
            return response([
                "message" => "Invalid fields",
                "errors" => $validator->errors()
            ], 422);
        }

        $user = User::create($validator->validated());
        Auth::login($user);
        $token = $user->createToken(uniqid())->plainTextToken;

        return response([
            "message" => "Register success",
            "token" => $token,
            "user" => $user
        ], 200);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            "username" => "required|min:3|regex:/^[a-z0-9_.]+$/i",
            "password" => "required|min:6",
        ]);

        if($validator->fails()){
            return response([
                "message" => "Invalid fields",
                "errors" => $validator->errors()
            ], 422);
        }

        if(!Auth::attempt($validator->validated())){
            return response([
                "message" => "Wrong username or password"
            ], 401);
        }
        $user = User::find(auth()->user()->id);
        $token = $user->createToken(uniqid())->plainTextToken;

        return response([
            "message" => "Login success",
            "token" => $token,
            "user" => $user
        ], 200);
    }

    public function logout(){
        auth()->user()->tokens()->delete();
        return response([
            "message" => "Logout success",
        ]);
    }
}
