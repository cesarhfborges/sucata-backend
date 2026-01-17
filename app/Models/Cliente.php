<?php

namespace App\Models;

use App\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $nome_razaosocial
 * @property string $sobrenome_nomefantasia
 * @property string $cpf_cnpj
 * @property string $telefone
 * @property string $email
 */
class Cliente extends Model
{

    use HasFactory, TracksUserActions;

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

    protected $hidden = [
        'created_by',
        'updated_by',
    ];

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function atualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
