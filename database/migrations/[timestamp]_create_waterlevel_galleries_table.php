<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('waterlevel_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained('waterlevellist')->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waterlevel_galleries');
    }
};
