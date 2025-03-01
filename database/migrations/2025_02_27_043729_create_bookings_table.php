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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('venue');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('price_per_hour')->nullable();
            $table->integer('total_price')->nullable();
            $table->integer('capacity');
            $table->string('status')->default('pending');
            $table->integer('venue_id');
            $table->integer('event_id');
            $table->integer('user_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
