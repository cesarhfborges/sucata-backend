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
        $admin = User::create([
            'nome' => 'Admin',
            'sobrenome' => 'Sistema',
            'email' => 'admin@admin.com',
            'password' => Hash::make('@zyba.@'),
            'ativo' => true,
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        if (app()->environment('local', 'development')) {
            User::factory()->count(3)->create([
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);
        }
    }
}
