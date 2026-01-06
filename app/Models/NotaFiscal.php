<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotaFiscal extends Model
{

    use HasFactory;

    protected $table = 'notas_fiscais';

    protected $fillable = [
        'empresa_id',
        'cliente_id',
        'nota_fiscal',
        'serie',
        'emissao',
        'status',
    ];

    protected $casts = [
        'emissao' => 'datetime',
        'nota_fiscal' => 'integer',
        'serie' => 'integer',
        'created_at' => 'datetime:Y-m-d\TH:i:s',
        'updated_at' => 'datetime:Y-m-d\TH:i:s',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function itens()
    {
        return $this->hasMany(NotaFiscalItem::class, 'nota_fiscal_id');
    }
}
