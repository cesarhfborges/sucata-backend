<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\NotaFiscal;
use App\Models\NotaFiscalItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotaFiscalItemFactory extends Factory
{
    /**
     * O nome do model correspondente ao Factory.
     *
     * @var string
     */
    protected $model = NotaFiscalItem::class;

    /**
     * Define o estado padrão do model.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'nota_fiscal_id' => NotaFiscal::factory(),
            'material_id' => function () {
                $material = Material::inRandomOrder()->first() ?? Material::factory()->create();
                return $material->codigo;
            }
        ];
    }
}
