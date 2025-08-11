<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $r)
    {
        $data = $r->validate([
            'name'=>'required|string|max:100',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|string|min:8',
        ]);
        $u = \App\Models\User::create([
            'name'=>$data['name'], 'email'=>$data['email'], 'password'=>bcrypt($data['password'])
        ]);
        $token = $u->createToken('api')->plainTextToken;
        return \App\Support\ApiResponse::ok(['token'=>$token]);
    }

    public function login(Request $r)
    {
        $cred = $r->validate(['email'=>'required|email','password'=>'required']);
        if (!auth()->attempt($cred)) { return \App\Support\ApiResponse::error('Invalid credentials', 422); }
        $token = $r->user()->createToken('api')->plainTextToken;
        return \App\Support\ApiResponse::ok(['token'=>$token]);
    }

    public function me(Request $r){ return \App\Support\ApiResponse::ok($r->user()); }
    public function logout(Request $r){ $r->user()->currentAccessToken()->delete(); return \App\Support\ApiResponse::ok(); }
}
