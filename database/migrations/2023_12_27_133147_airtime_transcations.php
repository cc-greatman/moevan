<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('top_up_transactions', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 20, 2);
            $table->string('trx')->unique();
            $table->string('airtime_trx');
            $table->unsignedBigInteger('network_id');
            $table->unsignedBigInteger('user_id');
            $table->string('phone');
            $table->string('type');
            $table->integer('status');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('network_id')->references('network_id')->on('networks');
        });
    }

    public function down()
    {
        Schema::dropIfExists('top_up_transactions');
    }
};
