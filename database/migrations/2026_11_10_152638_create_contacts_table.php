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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            
            // Relación con el usuario (FK) - Un usuario tiene muchos contactos
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Datos del contacto
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            
            // Agrega la columna deleted_at para el Soft Delete (Borrado lógico)
            $table->softDeletes();
            
            // Agrega created_at y updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
