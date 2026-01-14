<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Sucatas por Cliente</title>

    <style>
        @page {
            margin: 5mm 5mm 5mm 5mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #000;
        }

        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo {
            width: 150px;
        }

        .logo img {
            display: block;
            max-width: 150px;
        }

        .titulo {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
        }

        .info {
            margin-top: 6px;
            border: 1px solid #000;
            padding: 5px;
        }

        .info table {
            width: 100%;
            border-collapse: collapse;
        }

        .info td {
            padding: 2px 4px;
        }

        .info td span.label {
            width: 90px;
            font-weight: bold;
        }

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

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .resumo {
            background: #f0f0f0;
        }

        .assinatura-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 9px;
        }

        .assinatura-aviso {
            border: 1px solid #000;
            /*border-bottom: 1px solid #000;*/
            padding: 8px 6px;
        }

        .assinatura-label {
            padding-top: 0;
        }

        .assinatura-space {
            padding-top: 36px;
        }

        .assinatura-content {
            font-size: 14px;
        }

        .footer {
            position: fixed;
            bottom: -1mm;
            left: 0;
            right: 0;
            font-size: 9px;
            border-top: 1px solid #000;
        }

        .footer-table {
            width: 100%;
        }
    </style>
</head>
<body>

{{-- =========================
     CABEÇALHO
========================= --}}
<div class="header">
    <table class="header-table">
        <tr>
            <td class="logo">
                @if (!empty($logotipo))
                    <img src="{{ $logotipo }}" alt="Platôflex" height="30px">
                @endif
            </td>

            <td class="titulo">
                RELATÓRIO DE SUCATAS POR CLIENTE
            </td>

            <td class="right">
                Data: {{ $data }}<br>
                Hora: {{ $hora }}
            </td>
        </tr>
    </table>
</div>

{{-- =========================
     DADOS DO CLIENTE
========================= --}}
@if(isset($cliente))
    <div class="info">
        <table style="width: 100%; border-collapse: collapse;">

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


{{-- =========================
     TABELA DE ITENS
========================= --}}
<table class="table">
    <tr>
        <th style="width: 5%">NF</th>
        <th style="width: 5%">Série</th>
        <th style="width: 6%">Nº</th>
        <th style="width: 8%">Código</th>
        <th>Produto</th>
        <th style="width: 10%">Status</th>
        <th style="width: 10%">Faturado</th>
        <th style="width: 12%">Saldo Devedor</th>
    </tr>
    <tbody>
    @forelse ($itens as $item)
        <tr>
            <td>{{ $item['nf'] }}</td>
            <td>{{ $item['serie'] }}</td>
            <td>{{ $item['numero'] }}</td>
            <td>{{ $item['codigo'] }}</td>
            <td>{{ $item['produto'] }}</td>
            <td>{{ $item['status'] }}</td>
            <td class="right">{{ $item['faturado'] }}</td>
            <td class="right">{{ $item['saldo_devedor'] }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="8" style="text-align: center">
                Nenhum registro encontrado
            </td>
        </tr>
    @endforelse
    <tr class="resumo">
        <td colspan="4">Total de registros: {{$total['quantidade']}}</td>
        <td colspan="2" class="text-right">Total Faturado/Devedor</td>
        <td colspan="1" class="text-right">{{$total['faturado']}}</td>
        <td colspan="1" class="text-right">{{$total['saldo_devedor']}}</td>
    </tr>
    </tbody>
</table>

{{-- =========================
     AVISO
========================= --}}

<table class="assinatura-table">
    <thead>
    <tr>
        <th style="width: 30%"></th>
        <th style="width: 70%"></th>
    </tr>
    </thead>
    <tbody>
        <!-- AVISO 100% -->
        <tr>
            <td colspan="2" class="assinatura-aviso">
                <strong>AVISO IMPORTANTE:</strong><br>
                A NÃO DEVOLUÇÃO DA SUCATA NO PERÍODO DE 90 DIAS, IMPLICARÁ NO </br>
                FATURAMENTO DAS MESMAS, CUJO O VALOR SERÁ O MESMO DA PEÇA.
            </td>
        </tr>

        @if(isset($cliente))
            <tr class="assinatura-content">
                <td class="text-center assinatura-space">
                    _______/_______/_______
                </td>
                <td class="text-center assinatura-space">
                    __________________________________________________
                </td>
            </tr>

            <tr class="assinatura-content">
                <td class="text-center assinatura-label">
                    Data
                </td>
                <td class="text-center assinatura-label">
                    {{ $cliente['nome_razaosocial'] }}
                </td>
            </tr>
        @endif
    </tbody>

</table>


{{--=========================--}}
{{--     RODAPÉ--}}
{{--=========================--}}

<htmlpagefooter name="footer">
    <div style="border-top:1px solid #000;font-size:9px;text-align:right;padding-top:4px">
        Página {PAGENO} de {nbpg}
    </div>
</htmlpagefooter>

<sethtmlpagefooter name="footer" value="on" />

{{--<div class="footer">--}}
{{--    <table class="footer-table">--}}
{{--        <tr>--}}
{{--            <td>--}}
{{--                {{ $nome_fantasia }}--}}
{{--                Data: {{ $data }} às {{ $hora }}--}}
{{--            </td>--}}
{{--            <td class="right"></td>--}}
{{--        </tr>--}}
{{--    </table>--}}
{{--</div>--}}
{{--<script type="text/php">--}}
{{--    if (isset($pdf)) {--}}
{{--        $pdf->page_script(function ($pageNumber, $pageCount, $pdf, $fontMetrics) {--}}

{{--            $text = "Página {$pageNumber} de {$pageCount}";--}}

{{--            $font = $fontMetrics->getFont("DejaVu Sans", "normal");--}}
{{--            $size = 7;--}}
{{--            $color = [0, 0, 0];--}}

{{--            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);--}}

{{--            $x = $pdf->get_width() - $textWidth - 15;--}}
{{--            $y = $pdf->get_height() - 24;--}}

{{--            $pdf->text($x, $y, $text, $font, $size, $color);--}}
{{--        });--}}
{{--    }--}}
{{--</script>--}}
</body>
</html>
