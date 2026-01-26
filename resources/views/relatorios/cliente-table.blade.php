<style>
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 6px;
        table-layout: fixed;
    }

    .table td {
        white-space: nowrap; /* Impede a quebra de linha */
        overflow: hidden; /* Esconde texto longo extra */
        text-overflow: ellipsis; /* Adiciona "..." em texto cortado */
    }

    .table th,
    .table td {
        border: 1px solid #000;
        padding: 3px;
        font-size: 9px;
    }

    .table th {
        background: #f0f0f0;
        text-align: left;
    }
</style>

<table class="table" width="100%" border="1" cellspacing="0" cellpadding="3">
    <thead>
    <tr style="background:#f0f0f0">
        <th>CPF/CNPJ</th>
        <th>Cliente</th>
        <th>NF-e</th>
        <th>Série</th>
        <th>Código</th>
        {{--        <th>Produto</th>--}}
        <th>Status</th>
        <th>Faturado</th>
        <th>Saldo</th>
    </tr>
    </thead>
    <tbody>
    @foreach($itens as $item)
        <tr>
            <td>
                @php
                    $cpf_cnpj = $item->notaFiscal->cliente->cpf_cnpj ?? '';
                        echo strlen($cpf_cnpj) < 12
                        ? preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cpf_cnpj)
                        : preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cpf_cnpj)
                @endphp
                {{--                {{ $item->notaFiscal->cliente->cpf_cnpj ?? '' }}--}}
            </td>
            <td>
                {{ $item->notaFiscal->cliente->nome_razaosocial ?? '' }}
            </td>
            <td>{{ $item->notaFiscal->nota_fiscal ?? '' }}</td>
            <td>{{ $item->notaFiscal->serie ?? ''}}</td>
            <td>{{ $item->material->codigo ?? '' }}</td>
            {{--            <td>{{ $item->material->descricao ?? ''}}</td>--}}
            <td>{{ $item->saldo_devedor > 0 ? 'PENDENTE' : 'DEVOLVIDA' }}</td>
            <td align="right">{{ $item->faturado ?? ''}}</td>
            <td align="right">{{ $item->saldo_devedor ?? ''}}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr style="background:#f0f0f0">
        <td colspan="5">Total de registros: {{ $totais->quantidade }}</td>
        <td align="right">Totais</td>
        <td align="right">{{ $totais->faturado }}</td>
        <td align="right">{{ $totais->saldo_devedor }}</td>
    </tr>
    </tfoot>
</table>
