<?php

namespace Database\Factories;

use App\Enums\Estados;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{

    protected $model = Cliente::class;

    public function definition(): array
    {
        $users = User::pluck('id')->all();
        $user = $this->faker->randomElement($users);

        $dados = [
            'cep' => $this->faker->numerify('########'),
            'logradouro' => $this->faker->streetName(),
            'numero' => $this->faker->buildingNumber(),
            'complemento' => $this->faker->streetSuffix,
            'bairro' => $this->faker->citySuffix(),
            'cidade' => $this->faker->city(),
            'uf' => $this->faker->randomElement(Estados::values()),
            'telefone' => preg_replace('/\D/', '', $this->faker->phoneNumber()),
            'observacoes' => $this->faker->sentence(),
            'created_by' => $user,
            'updated_by' => $user,
        ];

        $isPessoaJuridica = $this->faker->boolean(50);

        if ($isPessoaJuridica) {
            $dados['nome_razaosocial'] = $this->faker->company();
            $dados['sobrenome_nomefantasia'] = $this->faker->companySuffix();
            $dados['email'] = $this->faker->unique()->companyEmail();
            $dados['cpf_cnpj'] = $this->generateCNPJ();
        } else {
            $dados['nome_razaosocial'] = $this->faker->firstName();
            $dados['sobrenome_nomefantasia'] = $this->faker->lastName();
            $dados['email'] = $this->faker->unique()->safeEmail();
            $dados['cpf_cnpj'] = $this->generateCPF();
        }

        return $dados;
    }

    private function generateCNPJ(): string
    {
        $cnpj = [];

        for ($i = 0; $i < 12; $i++) {
            $cnpj[] = rand(0, 9);
        }

        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;
        foreach ($weights1 as $i => $weight) {
            $sum += $cnpj[$i] * $weight;
        }
        $cnpj[12] = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);

        $sum = 0;
        foreach ($weights2 as $i => $weight) {
            $sum += $cnpj[$i] * $weight;
        }
        $cnpj[13] = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);

        return implode('', $cnpj);
    }

    private function generateCPF(): string
    {
        $cpf = [];

        for ($i = 0; $i < 9; $i++) {
            $cpf[] = rand(0, 9);
        }

        for ($j = 9; $j < 11; $j++) {
            $sum = 0;
            for ($i = 0; $i < $j; $i++) {
                $sum += $cpf[$i] * (($j + 1) - $i);
            }
            $cpf[$j] = (($sum * 10) % 11) % 10;
        }

        return implode('', $cpf);
    }
}
