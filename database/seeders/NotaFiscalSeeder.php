<?php

namespace Database\Seeders;

use App\Models\NotaFiscal;
use App\Models\NotaFiscalItem;
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
                ->comItens(7)
                ->create();
        }
    }
}
