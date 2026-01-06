<?php

namespace Database\Factories;

use App\Enums\Estados;
use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmpresaFactory extends Factory
{

    protected $model = Empresa::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'razao_social' => $this->faker->company(),
            'nome_fantasia' => $this->faker->company(),
            'cnpj' => $this->faker->numerify('########')."0001".$this->faker->numerify('##'),
            'cep' => $this->faker->numerify('########'),
            'logradouro' => $this->faker->streetName(),
            'numero' => $this->faker->buildingNumber(),
            'complemento' => $this->faker->streetSuffix,
            'bairro' => $this->faker->citySuffix(),
            'cidade' => $this->faker->city(),
            'uf' => $this->faker->randomElement(Estados::values()),
            'telefone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->companyEmail(),
            'observacoes' => $this->faker->sentence(),
        ];
    }
}
