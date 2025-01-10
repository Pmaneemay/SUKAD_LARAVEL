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
            $table->string('time_slot')->after('booking_date'); // Add time_slot column after booking_date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('facility_bookings', function (Blueprint $table) {
            $table->dropColumn('time_slot'); // Remove time_slot column
        });
    }
};