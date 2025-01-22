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
        Schema::create('electric_transactions', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 20, 2);
            $table->string('trx')->unique();
            $table->string('electric_trx');
            $table->string('token')->nullable();
            $table->string('units')->nullable();
            $table->string('meter_no');
            $table->string('meter_type');
            $table->unsignedBigInteger('user_id');
            $table->string('phone');
            $table->integer('status');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electric_transactions');
    }
};
