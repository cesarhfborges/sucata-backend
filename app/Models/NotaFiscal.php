<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
    ];

    protected $appends = [
        'pendente',
    ];

    protected $casts = [
        'emissao' => 'date:Y-m-d',
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

    protected function pendente(): Attribute
    {
        return Attribute::get(function () {
            if ($this->relationLoaded('itens')) {
                return $this->itens->contains(
                    fn($item) => $item->saldo_devedor > 0
                );
            }

            return $this->itens()
                ->where('saldo_devedor', '>', 0)
                ->exists();
        });
    }

    public function itens(): HasMany
    {
        return $this->hasMany(NotaFiscalItem::class, 'nota_fiscal_id');
    }
}
