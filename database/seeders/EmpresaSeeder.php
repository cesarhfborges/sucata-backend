<?php

namespace Database\Seeders;

use App\Enums\Estados;
use App\Models\Empresa;
use Faker;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker\Factory::create();

        $empresas = [
            'Recopeças Industrial',
            'Aldeny Maria Barbosa',
            'SAT',
            'Platomix',
            'Capital',
            'Embreagens Central',
        ];

        foreach ($empresas as $nome) {
            Empresa::create([
                'razao_social' => $nome,
                'nome_fantasia' => 'Platoflex embreagens',
                'cnpj' => $faker->numerify('########') . "0001" . $faker->numerify('##'),
                'cep' => '71990006',
                'logradouro' => 'ADE Conjunto 22 lote 32',
                'numero' => '32',
                'complemento' => '',
                'bairro' => 'ADE',
                'cidade' => 'Riacho fundo',
                'uf' => Estados::DF,
                'email' => 'contato@platoflex.com.br',
                'telefone' => '6133992727',
                'observacoes' => '',
            ]);
        }
    }
}
