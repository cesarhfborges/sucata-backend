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

            $table->timestamps();

            $table->foreign('nota_fiscal_id')->references('id')->on('notas_fiscais');
            $table->foreign('material_id')->references('codigo')->on('materiais');
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
