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
        Schema::create('agent_data_plan_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id'); // Foreign key referencing users.id
            $table->string('data_id'); // Foreign key referencing data_plans.data_id
            $table->decimal('price', 10, 2);
            $table->timestamps();
        
            // Foreign key constraints
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('data_id')->references('data_id')->on('data_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_data_plan_prices');
    }
};
