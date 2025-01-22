<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('networks', function (Blueprint $table) {
            $table->string('network_id')->primary(); // Set as primary key
            $table->string('name');
            $table->timestamps();
        });
    }

    /*
     * Reverse Migrations
     */
    public function down()
    {
        Schema::dropIfExists('networks');
    }
};
