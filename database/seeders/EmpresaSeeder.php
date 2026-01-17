<?php

namespace Database\Seeders;

use App\Enums\Estados;
use App\Models\Empresa;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresas = [
            [
                'nome' => 'Recopeças Industrial',
                'cnpj' => '36763985000154',
            ],
            [
                'nome' => 'Aldeny Maria Barbosa',
                'cnpj' => '69892970000145',
            ],
            [
                'nome' => 'SAT',
                'cnpj' => '52101586000184',
            ],
            [
                'nome' => 'Platomix',
                'cnpj' => '38500951000100',
            ],
            [
                'nome' => 'Capital',
                'cnpj' => '63032851000136',
            ],
            [
                'nome' => 'Embreagens Central',
                'cnpj' => '57663035000128',
            ],
        ];

        foreach ($empresas as $empresa) {
            Empresa::create([
                'razao_social' => $empresa['nome'],
                'cnpj' => $empresa['cnpj'],
                'nome_fantasia' => 'Platoflex embreagens',
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
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
