<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barber extends Model
{
    use HasFactory;

    public function photos()
    {
        return $this->hasMany("App\Models\BarberPhoto");
    }

    public function testimonials()
    {
        return $this->hasMany("App\Models\BarberTestimonial");
    }

    public function reviews()
    {
        return $this->hasMany("App\Models\BarberReview");
    }

    public function appointments()
    {
        return $this->hasMany("App\Models\UserAppointment");
    }

    public function availability()
    {
        return $this->hasMany("App\Models\BarberAvailability");
    }

    public function services()
    {
        return $this->belongsToMany("App\Models\Service")
            ->as("relation")
            ->withPivot("price");
    }
}
