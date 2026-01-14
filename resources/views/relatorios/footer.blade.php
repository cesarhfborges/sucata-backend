<style>
    .aviso {
        width: 100%;
        margin-top: 12px;
        font-size: 9px;
        border: 1px solid #000;
        padding: 0 6px;
    }
    .aviso p {
        padding: 0;
    }
    .aviso strong {
        margin-bottom: 12px;
    }


    .assinatura {
        width: 100%;
        border-collapse: collapse;
        margin-top: 28px;
        font-size: 14px;
    }

    .assinatura tr {
    }
    .assinatura tr td {
        text-align: center;
    }
</style>

<br>
<div class="aviso">
    <p>
        <strong>AVISO IMPORTANTE:</strong>
    </p>
    <p>
        A NÃO DEVOLUÇÃO DA SUCATA NO PERÍODO DE 90 DIAS, IMPLICARÁ NO FATURAMENTO DAS MESMAS, CUJO O VALOR SERÁ O MESMO DA PEÇA.
    </p>
</div>

@if($cliente)
    <table class="assinatura">
        <tr>
            <td>_______/_______/_______</td>
            <td>__________________________________________________</td>
        </tr>
        <tr>
            <td>Data</td>
            <td>{{ $cliente['nome_razaosocial'] }}</td>
        </tr>
    </table>
@endif

<htmlpagefooter name="footer">
    <div style="border-top:1px solid #000;font-size:9px;text-align:right;padding-top:4px">
        Página {PAGENO} de {nbpg}
    </div>
</htmlpagefooter>

<sethtmlpagefooter name="footer" value="on" />
