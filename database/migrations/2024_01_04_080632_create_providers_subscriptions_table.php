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
        Schema::create('providers_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('providers')->nullable(); // Foreign key to TVProvider model
            $table->string('variation_code');
            $table->string('name');
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->foreign('providers')->references('id')->on('providers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providers_subscriptions');
    }
};
