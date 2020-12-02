<?php 
namespace App\Services;

use Illuminate\Support\Facades\Http;

class Geolocation 
{
    public static function search($address)
    {   
        $address = urlencode($address);
        return Http::get("https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyAVwZfB7D7z1vOj3J4W_cF_B5cVKP_eEck");
    }

    public static function calcDistance($lat, $lng)
    {   
        $myLat = "-18.8659167";
        $myLng = "-41.9493979";

        $distance = sqrt(
            POW(69.1 * ($myLat - $lat), 2) + 
            POW(69.1 * ($myLng - $lng) * COS($lat / 57.3), 2));
        
            return $distance;
    }
}