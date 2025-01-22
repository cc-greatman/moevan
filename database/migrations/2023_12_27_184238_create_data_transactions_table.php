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
        Schema::create('data_transactions', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 20, 2);
            $table->string('trx')->unique();
            $table->string('data_trx');
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('user_id');
            $table->string('phone');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('plan_id')->references('data_id')->on('data_plans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_transactions');
    }
};
