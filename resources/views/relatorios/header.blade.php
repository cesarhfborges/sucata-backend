<style>
    body { font-family: DejaVu Sans; font-size: 10px; }
    .header {
        border-bottom: 2px solid #000;
        padding-bottom: 5px;
        margin-bottom: 8px;
    }
    .header-table {
        width: 100%;
        border-collapse: collapse;
    }
    .header-table tr td {
        vertical-align: middle;
    }
    .header-table tr td.logo {
        width: 150px;
    }
    .header-table tr td.titulo {
        text-align: center;
        font-size: 14px;
        font-weight: bold;
    }
    .header-table tr td.info {
        width: 150px;
        text-align: right;
    }

    .cliente {
        margin-top: 6px;
        padding: 5px;
        border: 1px solid #000;
    }

    .cliente table {
        width: 100%;
        border-collapse: collapse;
    }

    .cliente table td {
        padding: 2px 4px;
    }

    .cliente table td span.label {
        width: 90px;
        font-weight: bold;
    }
</style>

<div class="header">
    <table class="header-table">
        <tr>
            <td class="logo">
                <img src="{{ $logotipo }}" alt="Platôflex" height="30px">
            </td>

            <td class="titulo">
                RELATÓRIO DE SUCATAS
            </td>

            <td class="info">
                Data: {{ $data }}<br>
                Hora: {{ $hora }}
            </td>
        </tr>
    </table>
</div>

@if(!empty($cliente))
    <div class="cliente">
        <table>

            <!-- CPF / CNPJ -->
            <tr>
                <td colspan="2">
                    <span class="label">CPF/CNPJ:</span><br>
                    {{ $cliente['cpf_cnpj'] }}
                </td>
            </tr>

            <!-- Nome / Fantasia -->
            <tr>
                <td style="width: 50%;">
                <span class="label">
                    @if(strlen($cliente['cpf_cnpj']) > 11)
                        Razão social:
                    @else
                        Nome:
                    @endif
                </span><br>
                    {{ $cliente['nome_razaosocial'] }}
                </td>

                <td style="width: 50%;">
                <span class="label">
                    @if(strlen($cliente['cpf_cnpj']) > 11)
                        Nome fantasia:
                    @else
                        Sobrenome:
                    @endif
                </span><br>
                    {{ $cliente['sobrenome_nomefantasia'] }}
                </td>
            </tr>

            <!-- Telefone / Email -->
            <tr>
                <td style="width: 50%;">
                    <span class="label">Telefone:</span><br>
                    {{ $cliente['telefone'] }}
                </td>

                <td style="width: 50%;">
                    <span class="label">Email:</span><br>
                    {{ $cliente['email'] }}
                </td>
            </tr>

        </table>
    </div>
@endif
