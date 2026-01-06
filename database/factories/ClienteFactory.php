<?php

namespace Database\Factories;

use App\Enums\Estados;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{

    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->company(),
            'cpf_cnpj' => $this->faker->numerify('########')."0001".$this->faker->numerify('##'),
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
