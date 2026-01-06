<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\NotaFiscal;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotaFiscalFactory extends Factory
{
    protected $model = NotaFiscal::class;

    public function definition(): array
    {
        return [
            'empresa_id' => function () {
                $empresa = Empresa::inRandomOrder()->first() ?? Empresa::factory()->create();
                return $empresa->id;
            },

            'cliente_id' => function () {
                $cliente = Cliente::inRandomOrder()->first() ?? Cliente::factory()->create();
                return $cliente->id;
            },

            'nota_fiscal' => $this->faker->unique()->numberBetween(1000, 999999),
            'serie' => $this->faker->numberBetween(1, 10),
            'emissao' => $this->faker->dateTimeBetween('-1 year', 'now'),

            'status' => $this->faker->randomElement(['PENDENTE', 'DEVOLVIDA']),
        ];
    }

    public function pendente(): self
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'PENDENTE',
        ]);
    }

    public function devolvida(): self
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'DEVOLVIDA',
        ]);
    }
}
