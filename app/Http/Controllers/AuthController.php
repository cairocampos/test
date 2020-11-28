<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:api", ["except" => ["login","create"]]);
    }

    public function create(Request $request) {
        $request->validate([
            "name" => "required|string",
            "email" => "required|email|unique:users",
            "password" => "required"
        ]);
        $data = $request->only(["name", "email", "password"]);
        $data["password"] = Hash::make($data["password"]);
        
        $user = User::create($data);
        $token = Auth::login($user);

        return $this->respondWithToken($token);
    }

    public function login(Request $request)
    {
        $credentials = $request->only(["email", "password"]);

        if(!$token = Auth::attempt($credentials)) {
            return response()->json(["message" => "Credenciais inválidas!"], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        Auth::logout();
        return ["message" => ""];
    }

    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    public function respondWithToken($token) {
        return [
            "type" => "Bearer",
            "token" => $token
        ];
    }

    public function me()
    {
        $user =  auth()->user();
        $user["avatar"] = url("/media/avatars/{$user['avatar']}");

        return $user;
    }
}
