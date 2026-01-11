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

            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresas');
            $table->foreign('cliente_id')->references('id')->on('clientes');

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
