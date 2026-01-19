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
        Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('npm')->unique();
        $table->string('angkatan');        // Tambahan: Angkatan
        $table->string('email')->unique();
        $table->string('password');
        
        // FOTO
        $table->string('foto_ktm')->nullable();        // Tambahan: Path file foto KTM
        $table->string('foto_diri')->nullable(); // Tambahan: Foto diri (Boleh KOSONG di awal)

        // STATUS
        $table->timestamp('email_verified_at')->nullable();
        $table->boolean('has_voted')->default(false);
        
        $table->string('role')->default('mahasiswa');
        
        $table->rememberToken();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
