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
    Schema::create('timeslots', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('facility_id');
        $table->string('start_time');
        $table->string('end_time');
        $table->timestamps();
    
        $table->foreign('facility_id')->references('id')->on('sports_facilities')->onDelete('cascade');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timeslots');
    }
};
