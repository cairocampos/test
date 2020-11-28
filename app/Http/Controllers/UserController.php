<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function update(Request $request)
    {   
        $request->validate([
            "name" => "string",
            "email" => "email",
            "password" => "confirmed",
            "avatar" => "nullable|image|mimes:jpeg,png,jpg"
        ]);

        $data = $request->only(["name", "email", "password", "password_confirmation"]);
        $user = User::find(auth()->user()->id);
        
        if(isset($data["name"])) {
            $user->name = $data["name"];
        }
        
        if(isset($data["password"])) {
            if($data["password"] == $data["password_confirmation"]) {
                $user->password = Hash::make($data["password"]);
            } else {
                return response()->json(["message" => "Senhas não conferem!"], 422);
            }
        }

        if(isset($data["email"])) {
            if($data["email"] != $user->email) {
                $emailAlreadyExists = User::where("email", $data["email"])->count();
                
                if(!$emailAlreadyExists) {
                    $user->email = $data["email"];
                }

                return response()->json(["message" => "Email já existe"], 422);
            }
        } 

        $user->save();

        $user->avatar = url("/media/avatars/{$user->avatar}");

        return $user;
    }
}
