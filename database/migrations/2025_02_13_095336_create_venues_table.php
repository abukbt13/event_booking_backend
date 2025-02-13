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
        Schema::create('venues', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('venue'); // Name of the venue (e.g., "Garden Hall")
            $table->string('location'); // Location of the venue (e.g., "123 Main St, City")
            $table->integer('capacity'); // Maximum capacity of the venue
            $table->text('description')->nullable(); // Description of the venue (optional)
            $table->string('amenities')->nullable(); // List of amenities (e.g., ["catering", "parking", "Wi-Fi"])
            $table->integer('price_per_hour'); // Price per hour for renting the venue
            $table->string('contact_email')->nullable(); // Contact email for the venue
            $table->string('contact_phone')->nullable(); // Contact phone for the venue
            $table->string('user_id'); // user creating it
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
