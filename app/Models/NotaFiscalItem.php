<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaFiscalItem extends Model
{
    use HasFactory;

    /**
     * O nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'nota_fiscal_itens';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array
     */
    protected $fillable = [
        'nota_fiscal_id',
        'material_id',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d\TH:i:s',
        'updated_at' => 'datetime:Y-m-d\TH:i:s',
    ];

    /**
     * Relacionamento: O item pertence a uma Nota Fiscal.
     * * @return BelongsTo
     */
    public function notaFiscal(): BelongsTo
    {
        return $this->belongsTo(NotaFiscal::class, 'nota_fiscal_id');
    }

    /**
     * Relacionamento: O item pertence a um Material.
     * Nota: A chave estrangeira é 'material_id' e a chave local em materiais é 'codigo'.
     * * @return BelongsTo
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id', 'codigo');
    }
}
