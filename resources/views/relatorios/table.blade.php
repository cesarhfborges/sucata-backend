<style>
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 6px;
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
        <th>NF-e</th>
        <th style="width: 50px">Série</th>
        <th>Código</th>
        <th>Produto</th>
        <th style="width: 60px">Status</th>
        <th style="width: 70px">Faturado</th>
        <th style="width: 70px">Saldo</th>
    </tr>
    </thead>
    <tbody>
    @foreach($itens as $item)
        <tr>
            <td>{{ $item->notaFiscal->nota_fiscal ?? '' }}</td>
            <td>{{ $item->notaFiscal->serie ?? ''}}</td>
            <td>{{ $item->material->codigo ?? '' }}</td>
            <td>{{ $item->material->descricao ?? ''}}</td>
            <td>{{ $item->saldo_devedor > 0 ? 'PENDENTE' : 'DEVOLVIDA' }}</td>
            <td align="right">{{ $item->faturado ?? ''}}</td>
            <td align="right">{{ $item->saldo_devedor ?? ''}}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr style="background:#f0f0f0">
        <td colspan="4">Total de registros: {{ $totais->quantidade }}</td>
        <td align="right">Totais</td>
        <td align="right">{{ $totais->faturado }}</td>
        <td align="right">{{ $totais->saldo_devedor }}</td>
    </tr>
    </tfoot>
</table>
