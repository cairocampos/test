<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarberServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barber_service', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("barber_id");
            $table->unsignedBigInteger("service_id");
            $table->float("price");
            $table->foreign("barber_id")->references("id")->on("barbers");
            $table->foreign("service_id")->references("id")->on("services");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barber_service');
    }
}
