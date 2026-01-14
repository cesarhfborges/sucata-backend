<?php

namespace Database\Seeders;

use App\Models\NotaFiscal;
use Faker\Factory;
use Illuminate\Database\Seeder;

class NotaFiscalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('local', 'development')) {
            NotaFiscal::factory()
                ->count(3000)
                ->comItens(Factory::create()->numberBetween(5, 15))
                ->create();
        }
    }
}
