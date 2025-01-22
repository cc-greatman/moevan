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
        Schema::create('cable_transactions', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 20, 2);
            $table->string('trx')->unique();
            $table->string('cable_trx');
            $table->string('type');
            $table->string('package');
            $table->string('billers_code');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('user_id');
            $table->string('phone');
            $table->integer('status');
            $table->timestamps();

            $table->foreign('service_id')->references('id')->on('providers');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cable_transactions');
    }
};
