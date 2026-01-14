<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(UsuarioSeeder::class);
        $this->call(EmpresaSeeder::class);
        $this->call(MaterialSeeder::class);
        $this->call(ClientesSeeder::class);
        $this->call(NotaFiscalSeeder::class);
    }
}
