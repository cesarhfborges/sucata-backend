<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Material;
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
        ];
    }

    public function comItens(int $quantidade = 7): static
    {
        return $this->afterCreating(function (NotaFiscal $nota) use ($quantidade) {

            $materiais = Material::inRandomOrder()
                ->limit($quantidade)
                ->get();

            foreach ($materiais as $material) {
                $nota->itens()->create([
                    'material_id'   => $material->codigo,
                    'faturado'      => rand(1, 30),
                    'saldo_devedor' => rand(0, 30),
                ]);
            }
        });
    }
}
