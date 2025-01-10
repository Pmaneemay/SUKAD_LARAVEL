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
        Schema::create('facility_bookings', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('facility_id'); // Foreign key to sports_facilities
            $table->string('user_id'); // User ID as VARCHAR
            $table->date('booking_date'); // Date of booking
            $table->string('time_slot'); // Add time_slot field
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending'); // Booking status
            $table->timestamps(); // created_at and updated_at
            
            // Foreign key constraint for facility_id
            $table->foreign('facility_id')->references('id')->on('sports_facilities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_bookings');
    }
};