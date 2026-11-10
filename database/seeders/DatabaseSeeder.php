<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear un usuario fijo para pruebas manuales
        $testUser = User::factory()->create([
            'name' => 'Usuario Prueba',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Crear entre 3 y 8 contactos para el usuario de prueba
        Contact::factory(rand(3, 8))->create([
            'user_id' => $testUser->id,
        ]);

        // Crear 4 usuarios adicionales con sus propios contactos
        User::factory(4)->create()->each(function ($user) {
            Contact::factory(rand(3, 8))->create([
                'user_id' => $user->id,
            ]);
        });
    }
}