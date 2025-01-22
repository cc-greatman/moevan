<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_plans', function (Blueprint $table) {
            $table->string('data_id')->primary(); // Set as primary key
            $table->unsignedBigInteger('network_id');
            $table->decimal('price', 20, 2);
            $table->string('size');
            $table->integer('validity');
            $table->unsignedBigInteger('type');
            $table->timestamps();

            // Foreign key constraint for network_id
            $table->foreign('network_id')->references('network_id')->on('networks');
            $table->foreign('type')->references('id')->on('data_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_plans');
    }
};
