<?php

namespace App\Http\Controllers;

use App\Http\Requests\GeolocationBarbersRequest;
use App\Models\Barber;
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
}
