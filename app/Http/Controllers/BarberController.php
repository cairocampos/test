<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BarberController extends Controller
{
    public function index()
    {
        $barbers = Barber::all();

        foreach($barbers as $barber) {
            $barber->avatar = url("/media/avatars/{$barber->avatar}");
        }

        return $barbers;
    }

    private function searchGeolocation($address)
    {
        return Http::get("");
    }
}
