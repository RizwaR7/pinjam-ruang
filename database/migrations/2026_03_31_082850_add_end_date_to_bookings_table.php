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
        Schema::table('bookings', function (Blueprint $table) {
            // Add end_date for multi-day bookings (nullable, defaults to same as booking_date)
            $table->date('end_date')->nullable()->after('booking_date');
        });

        // Set existing bookings' end_date to match booking_date (single day)
        DB::table('bookings')->whereNull('end_date')->update([
            'end_date' => DB::raw('booking_date')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('end_date');
        });
    }
};
