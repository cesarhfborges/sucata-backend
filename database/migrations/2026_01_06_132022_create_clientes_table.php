<?php

use App\Enums\Estados;
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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
//            $table->string('codigo')->primary()->unique();

            $table->string('nome')->nullable(false);
            $table->string('cpf_cnpj')->nullable(false)->unique();

            $table->string('cep')->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->enum('uf', Estados::values())->nullable();

            $table->string('telefone')->nullable();
            $table->string('email')->nullable();

            $table->longText('observacoes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
