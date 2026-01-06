<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nome' => 'Admin',
            'sobrenome' => 'Sistema',
            'email' => 'admin@admin.com',
            'password' => Hash::make('@zyba.@'),
            'ativo' => true,
        ]);
        if (app()->environment('local', 'development')) {
            User::factory()->count(3)->create();
        }
    }
}
