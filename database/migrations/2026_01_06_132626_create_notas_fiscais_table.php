<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notas_fiscais', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('empresa_id')->nullable(false);
            $table->unsignedBigInteger('cliente_id')->nullable(false);

            $table->integer('nota_fiscal');
            $table->integer('serie');
            $table->date('emissao');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->index('created_by');
            $table->index('updated_by');

            $table->index(
                ['empresa_id', 'cliente_id', 'emissao'],
                'idx_nf_empresa_cliente_emissao'
            );

            $table->unique(
                ['empresa_id', 'nota_fiscal', 'serie'],
                'notas_fiscais_empresa_nota_serie_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas_fiscais');
    }
};
