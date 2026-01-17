<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Material;
use App\Models\NotaFiscal;
use App\Models\User;
use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class NotaFiscalFactory extends Factory
{
    protected $model = NotaFiscal::class;

    public function definition(): array
    {
        $users = User::pluck('id')->all();
        $user = $this->faker->randomElement($users);
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

            'created_by' => $user,
            'updated_by' => $user,
        ];
    }

    public function comItens(int $quantidade = 7): static
    {
        return $this->afterCreating(function (NotaFiscal $nota) use ($quantidade) {

            $materiais = Material::inRandomOrder()
                ->limit($quantidade)
                ->get();

            if ($materiais->isEmpty()) {
                return;
            }

            $itens = $materiais->map(function ($material) use ($nota) {
                $agora = new DateTime();
                $faturado = rand(1, 30);

                return [
                    'nota_fiscal_id' => $nota->id,
                    'material_id'    => $material->codigo,
                    'faturado'       => $faturado,
                    'saldo_devedor'  => $this->faker->boolean() === true ? rand(0, $faturado): 0,
                    'created_at'     => $agora,
                    'updated_at'     => $agora,
                ];
            })->toArray();

            DB::table('nota_fiscal_itens')->insert($itens);
        });
    }
}
