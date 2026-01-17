<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\NotaFiscal;
use App\Models\NotaFiscalItem;
use App\Models\User;
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
        $users = User::pluck('id')->all();
        $user = $this->faker->randomElement($users);
        $faturado = $this->faker->numberBetween(1, 30);
        return [
            'nota_fiscal_id' => NotaFiscal::factory(),
            'material_id' => function () {
                $material = Material::inRandomOrder()->first() ?? Material::factory()->create();
                return $material->codigo;
            },
            'faturado' => $faturado,
            'saldo_devedor' => $this->faker->numberBetween(0, $faturado),
            'created_by' => $user,
            'updated_by' => $user,
        ];
    }
}
