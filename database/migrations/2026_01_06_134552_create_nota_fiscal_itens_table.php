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
        Schema::create('nota_fiscal_itens', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('nota_fiscal_id')->nullable(false);
            $table->string('material_id')->nullable(false);

            $table->integer('faturado')->default(0);
            $table->integer('saldo_devedor')->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->foreign('nota_fiscal_id')->references('id')->on('notas_fiscais');
            $table->foreign('material_id')->references('codigo')->on('materiais');

            $table->index('created_by');
            $table->index('updated_by');

            $table->index(
                ['nota_fiscal_id', 'saldo_devedor'],
                'idx_nfi_nota_saldo'
            );

            $table->unique(
                ['nota_fiscal_id', 'material_id'],
                'nota_fiscal_itens_nota_material_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_fiscal_itens');
    }
};
