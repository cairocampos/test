<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAppointment extends Model
{
    use HasFactory;

    protected $table = "user_appointments";

    public function user() 
    {
        return $this->belongsTo("App\Models\User");
    }

    public function barber()
    {
        return $this->belongsTo("App\Models\Barber");
    }

}
