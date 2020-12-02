<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAppointment;
use App\Models\UserFavorite;
use App\Services\Geolocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{   
    private $user_id;

    public function __construct()
    {
        $this->user_id = auth()->user()->id;
    }

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

    public function getFavoritesBarbers()
    {   
        $user_id = $this->user_id;
        $favorites = User::find($user_id)
            ->join("user_favorites", function($join) use($user_id) {
                $join->on("users.id", "=", "user_favorites.user_id")
                    ->where("users.id", $user_id);
            })
            ->join("barbers", function($join) {
                $join->on("barbers.id", "=", "user_favorites.barber_id");
            })
            ->select("barbers.*")
            ->get();
        
        foreach($favorites as $favorite) {
            $favorite["avatar"] = url("/media/avatars/{$favorite['avatar']}");
            $favorite["distance"] = Geolocation::calcDistance($favorite["latitude"], $favorite["longitude"]);
        }
        
            return $favorites;
    }

    public function setFavoriteBarber($barber_id)
    {   
        $data = [
            "user_id" => $this->user_id,
            "barber_id" => $barber_id
        ];

        if(UserFavorite::where("user_id", $this->user_id)->where("barber_id", $barber_id)->exists()) {
            return UserFavorite::where("user_id", $this->user_id)->where("barber_id", $barber_id)->delete();
        } else {
            $favorited = UserFavorite::create($data);
            return $favorited;
        }
    }

    public function getAppointments()
    {  
        return UserAppointment::with(["barber"])->where("user_id", $this->user_id)->get();
    }
}
