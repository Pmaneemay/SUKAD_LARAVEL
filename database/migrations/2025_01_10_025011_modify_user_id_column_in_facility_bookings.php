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
        Schema::table('facility_bookings', function (Blueprint $table) {
            // Add the time_slot column to the existing table
            $table->string('time_slot')->after('booking_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('facility_bookings', function (Blueprint $table) {
            // Drop the time_slot column if rolled back
            $table->dropColumn('time_slot');
        });
    }
};