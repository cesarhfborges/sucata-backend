<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $nome_razaosocial
 * @property string $sobrenome_nomefantasia
 * @property string $cpf_cnpj
 * @property string $telefone
 * @property string $email
 */
class Cliente extends Model
{

    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nome_razaosocial',
        'sobrenome_nomefantasia',
        'cpf_cnpj',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'telefone',
        'email',
        'observacoes'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d\TH:i:s',
        'updated_at' => 'datetime:Y-m-d\TH:i:s',
    ];
}
