<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('category')->default('elektronik'); // elektronik, furniture, audio_visual, lainnya
            $table->integer('quantity')->default(1);
            $table->boolean('is_available')->default(true);
            $table->string('condition')->default('baik'); // baik, rusak_ringan, rusak_berat
            $table->string('location')->nullable(); // lokasi alat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
