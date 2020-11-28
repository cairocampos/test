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
}