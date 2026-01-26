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

            $table->string('codigo', 200)->nullable();

            $table->string('nome_razaosocial', 200)->nullable(false);
            $table->string('sobrenome_nomefantasia', 200)->nullable(false);
            $table->string('cpf_cnpj', 14)->unique()->nullable(false);

            $table->string('rg_inscricao', 20)->nullable();

            $table->string('cep', 8)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->enum('uf', Estados::values())->nullable();

            $table->string('telefone', 20)->nullable();
            $table->string('email', 200)->nullable();

            $table->longText('observacoes')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->index('created_by');
            $table->index('updated_by');

            $table->index('nome_razaosocial', 'idx_cliente_nome');
            $table->index('sobrenome_nomefantasia', 'idx_cliente_fantasia');
            $table->index('cpf_cnpj', 'idx_cpf_cnpj' .
                '');
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
