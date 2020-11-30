<?php

namespace App\Http\Controllers;

use App\Http\Requests\GeolocationBarbersRequest;
use App\Models\Barber;
use App\Models\BarberAvailability;
use App\Models\User;
use App\Models\UserAppointment;
use App\Models\UserFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\Geolocation;

class BarberController extends Controller
{   
    private $lat = "-18.8659167";
    private $lng = "-41.9493979";
    private $city = "Governador Valadares";

    public function index(GeolocationBarbersRequest $request)
    {
        $latitude = $request->input("latitude");
        $longitude = $request->input("longitude");
        $city = $request->input("city");
        $page = $request->input("page", 0);
        $page = ceil(($page - 1) / 10);
        
        if($latitude && $longitude) {
            $location = Geolocation::search($latitude.",".$longitude);
            
            if(count($location["results"])) {
                $city = $location["results"][0]["formatted_address"];
            }            
        } else if($city) {
            $location = Geolocation::search($city);
            
            if(count($location["results"])) {
                $latitude = $location["results"][0]["geometry"]["location"]["lat"];
                $longitude = $location["results"][0]["geometry"]["location"]["lng"];
                
            }
        } else {
            $latitude = $this->lat;
            $longitude = $this->lng;
            $city = $this->city;
        }
        
        $barbers = Barber::selectRaw('*, SQRT(
            POW(69.1 * (latitude - '.$latitude.'), 2) + 
            POW(69.1 * ('.$longitude.' - longitude) * COS(latitude / 57.3), 2)) AS distance')
            //->having("distance", "<=", 10)
            ->orderBy("distance", "ASC")
            ->paginate();
            

        foreach($barbers as $barber) {
            $barber->avatar = url("/media/avatars/{$barber->avatar}");
        }

        return $barbers;
    }

    public function show($id) 
    {
        $barber = Barber::with(["photos", "reviews", "testimonials", "services"])->find($id);
        $barber["favorited"] = User::find(auth()->user()->id)->favorites()->where("barber_id", $id)->exists();
        
        $appointments = Barber::find($id)->appointments
            ->whereBetween("ap_datetime", [
                date("Y-m-d")." 00:00:00",
                date("Y-m-d", strtotime("+20 days"))." 23:59:59"
            ]);
        
        $ap_dates = [];
        foreach($appointments as $ap) {
            $ap_dates[] = $ap->ap_datetime;
        }

        $availability = Barber::find($id)->availability;
        $avails = [];
        $availables = [];

        foreach($availability as $avail) {
            $avails[$avail->weekday] = explode(",", $avail->hours);
        }

        for($i = 0; $i < 20; $i++) {
            $time = strtotime("+{$i} days");
            $day = date("w", $time);
            $data = date("Y-m-d", $time);

            if(in_array($day, array_keys($avails))) {
                $hours = [];

                foreach($avails[$day] as $hour) {
                    $dayFormatted = "{$data} {$hour}:00";

                    if(!in_array($dayFormatted, $ap_dates)) {
                        $hours[] = $hour;
                    }
                }

                if(count($hours)) {
                    $availables[] = [
                        "date" => $data,
                        "hours" => $hours
                    ];
                }
            }
        }    
        
        $barber["availability"] = $availables;

        return $barber;
    }
}
