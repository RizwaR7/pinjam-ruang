<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('scope')->default('universitas'); // universitas, fakultas
            $table->string('faculty')->nullable();
            $table->integer('capacity')->default(0);
            $table->text('facilities')->nullable();
            $table->string('building')->nullable();      // Gedung
            $table->string('floor')->nullable();          // Lantai
            $table->string('location')->nullable();       // Lokasi lengkap
            $table->enum('status', ['tersedia', 'dipakai', 'maintenance'])->default('tersedia');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
