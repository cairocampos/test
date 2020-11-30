<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BarberReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("barber_reviews", function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("barber_id");
            $table->string("name");
            $table->float("rate");
            $table->foreign("barber_id")->references("id")->on("barbers");
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
        Schema::dropIfExists("barber_reviews");
    }
}