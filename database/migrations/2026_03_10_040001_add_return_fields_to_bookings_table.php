<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->datetime('return_deadline')->nullable()->after('approved_at');
            $table->datetime('returned_at')->nullable()->after('return_deadline');
            $table->datetime('return_requested_at')->nullable()->after('returned_at');
            $table->decimal('fine_amount', 10, 2)->default(0)->after('return_requested_at');
            $table->string('fine_status')->default('none')->after('fine_amount'); // none, unpaid, paid
        });

        // Update status enum to include return_requested
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','approved','rejected','finished','cancelled','return_requested') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','approved','rejected','finished','cancelled') DEFAULT 'pending'");

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['return_deadline', 'returned_at', 'return_requested_at', 'fine_amount', 'fine_status']);
        });
    }
};
