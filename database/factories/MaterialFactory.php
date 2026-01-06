<?php

namespace Database\Factories;

use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    /**
     * O nome do model correspondente ao Factory.
     *
     * @var string
     */
    protected $model = Material::class;

    /**
     * Define o estado padrão do model.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'codigo'    => $this->randomCodigo(),
            'descricao' => $this->faker->words(3, true),
            'un'        => $this->faker->randomElement(['UN', 'KG', 'LT', 'M2', 'CX']),
        ];
    }

    private function randomCodigo(): string {
        $prefix = $this->faker->randomElement(['SC', 'KS', 'MB', 'NG']);
        $code = $this->faker->unique()->numberBetween(100000, 999999);
        return "$code-$prefix";
    }
}
