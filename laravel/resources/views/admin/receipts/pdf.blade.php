<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Tanda Terima {{ $receipt->receipt_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8.5px;
            color: #1a1a1a;
            background: #fff;
        }

        .page { padding: 12px 16px 10px; }

        /* ── HEADER ── */
        .header-wrap {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .header-wrap td { vertical-align: top; padding: 0; }

        .logo-cell { width: 55%; }
        .logo-cell img { width: 85px; display: block; }

        .company-info {
            font-size: 8.5px;
            color: #1B5DBC;
            font-weight: bold;
            line-height: 1.3;
            margin-top: 2px;
        }

        .title-cell { text-align: right; vertical-align: middle; }

        .doc-title {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1.5px;
            color: #1B5DBC;
            text-transform: uppercase;
            line-height: 1;
        }

        .divider {
            border: none;
            border-top: 1.5px solid #1B5DBC;
            margin: 5px 0 6px;
        }

        /* ── CLIENT + META ROW ── */
        .info-wrap {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 8px;
        }
        .info-wrap td { vertical-align: top; padding: 1px 0; }
        .info-client { width: 55%; }
        .info-client-lbl { width: 60px; color: #333; }
        .info-meta { text-align: left; vertical-align: top; }
        .meta-table {
            border-collapse: collapse;
            font-size: 8px;
        }
        .meta-table td { padding: 1px 4px; white-space: nowrap; }
        .meta-label { width: 75px; color: #333; }
        .meta-value { color: #111; }

        /* ── ITEM TABLE ── */
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 8px;
        }
        .item-table th {
            border: 1px solid #aaa;
            padding: 3px 6px;
            text-align: center;
            background: #fff;
            font-weight: bold;
        }
        .item-table td {
            border: 1px solid #aaa;
            padding: 3px 6px;
            vertical-align: middle;
        }
        .col-no     { width: 30px; text-align: center; }
        .col-desc   { text-align: left; }
        .col-amount { width: 120px; text-align: right; }
        .col-remark { width: 80px; text-align: center; }

        .empty-row td { height: 15px; }

        /* ── TOTAL ROW ── */
        .total-row td {
            border: 1px solid #aaa;
            padding: 3px 6px;
            font-size: 8px;
        }

        /* ── SIGNATURE (kanan saja) ── */
        .sig-outer {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .sig-outer td { vertical-align: top; padding: 0; }
        .sig-spacer { width: 40%; }

        .signature-wrap {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-wrap th {
            border: 1px solid #aaa;
            padding: 3px 6px;
            text-align: center;
            font-size: 8px;
            background: #fff;
            font-weight: bold;
        }
        .signature-wrap td {
            border: 1px solid #aaa;
            padding: 3px 6px;
            font-size: 8px;
            vertical-align: bottom;
            text-align: center;
        }
        .signature-info td {
            border: 1px solid #aaa;
            padding: 3px 6px;
            font-size: 8px;
            vertical-align: bottom;
            text-align: center;
        }

        /* ── FOOTER NOTE ── */
        .footer-note {
            margin-top: 6px;
            font-size: 7.5px;
            color: #333;
            line-height: 1.35;
        }

        .page-divider {
            border: none;
            border-top: 1px solid #aaa;
            margin-top: 8px;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ═══ HEADER ═══ --}}
    <table class="header-wrap">
        <tr>
            <td class="logo-cell">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo STI">
                @else
                    <div style="font-size:16px;font-weight:bold;color:#1B5DBC;">PT. STI</div>
                @endif
                <div class="company-info">PT. SISTEM TEKNOLOGI INTEGRATOR</div>
            </td>
            <td class="title-cell">
                <div class="doc-title">TANDA TERIMA</div>
            </td>
        </tr>
    </table>

    <hr class="divider">

    {{-- ═══ CLIENT + META ═══ --}}
    <table class="info-wrap">
        <tr>
            <td class="info-client">
                <table style="border-collapse:collapse;font-size:8px;">
                    <tr>
                        <td class="info-client-lbl">Kepada :</td>
                        <td style="color:#111;">
                            <strong>{{ $receipt->client_company }}</strong><br>
                            @if($receipt->client_address){!! nl2br(e($receipt->client_address)) !!}<br>@endif
                            @if($receipt->client_phone)Telp : {{ $receipt->client_phone }}@endif
                        </td>
                    </tr>
                </table>
            </td>
            <td class="info-meta">
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Date</td>
                        <td class="meta-value">{{ $receipt->date->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Receipt No</td>
                        <td class="meta-value">{{ $receipt->receipt_number }}</td>
                    </tr>
                    @if($receipt->nomor_po)
                    <tr>
                        <td class="meta-label">Nomor PO</td>
                        <td class="meta-value">{{ $receipt->nomor_po }}</td>
                    </tr>
                    @endif
                    @if($receipt->project_name)
                    <tr>
                        <td class="meta-label">Project</td>
                        <td class="meta-value">{{ $receipt->project_name }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- ═══ ITEM TABLE ═══ --}}
    <table class="item-table">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-desc">Description</th>
                <th class="col-amount">Amount</th>
                <th class="col-remark">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($receipt->items ?? [] as $i => $item)
            <tr>
                <td class="col-no">{{ $i + 1 }}</td>
                <td class="col-desc">{{ $item->description ?? '' }}</td>
                <td class="col-amount">Rp &nbsp;{{ number_format($item->amount, 0, ',', '.') }}</td>
                <td class="col-remark" style="text-align:center;">{{ $item->remarks ?? '' }}</td>
            </tr>
            @empty
            @if($receipt->invoice)
            <tr>
                <td class="col-no">1</td>
                <td class="col-desc">Pembayaran Inv No. {{ $receipt->invoice->invoice_number }}</td>
                <td class="col-amount">Rp &nbsp;{{ number_format($receipt->amount, 0, ',', '.') }}</td>
                <td class="col-remark"></td>
            </tr>
            @endif
            @endforelse

            {{-- baris kosong pengisi --}}
            @php $filledRows = max(count($receipt->items ?? []), $receipt->invoice ? 1 : 0); @endphp
            @for($e = 0; $e < max(0, 4 - $filledRows); $e++)
            <tr class="empty-row">
                <td></td><td></td><td></td><td></td>
            </tr>
            @endfor
        </tbody>
        <tfoot>
            @if($receipt->discount > 0)
            <tr class="total-row">
                <td colspan="2" style="text-align:right;border:1px solid #aaa;">Diskon &nbsp;</td>
                <td style="text-align:right;border:1px solid #aaa;">
                    Rp &nbsp;{{ number_format($receipt->discount, 0, ',', '.') }}
                </td>
                <td style="border:1px solid #aaa;"></td>
            </tr>
            @endif
            <tr class="total-row">
                <td colspan="2" style="text-align:right;font-weight:bold;border:1px solid #aaa;">
                    {{ $receipt->discount > 0 ? 'Total Setelah Diskon' : '' }}
                </td>
                <td style="text-align:right;font-weight:bold;border:1px solid #aaa;">
                    Rp &nbsp;{{ number_format($receipt->amount - $receipt->discount, 0, ',', '.') }}
                </td>
                <td style="border:1px solid #aaa;"></td>
            </tr>
        </tfoot>
    </table>

    {{-- ═══ SIGNATURE (rata kanan) ═══ --}}
    <table class="sig-outer">
        <tr>
            <td class="sig-spacer"></td>
            <td style="width:60%;">
                <table class="signature-wrap">
                    <thead>
                        <tr>
                            <th style="width:33%;">Send by</th>
                            <th style="width:33%;">Received By</th>
                            <th style="width:34%;">Payment schedule</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:3px 6px;height:38px; border-top: 1px solid #000;">
                            
                            </td>
                            <td style="padding:3px 6px; border-top: 1px solid #000;"></td>
                            <td style="padding:3px 6px; border-top: 1px solid #000;"></td>
                        </tr>     
                    {{-- Baris Name --}}
                    <tr>
                        <td style="padding:3px 6px; border-top: 1px solid #000;"></td>
                        <td style="padding:3px 6px; text-align:left; border-top: 1px solid #000;">
                            Name :&nbsp;
                        </td>
                        <td style="padding:3px 6px; border-top: 1px solid #000;"></td>
                    </tr>

                    {{-- Baris Date --}}
                    <tr>
                        <td style="padding:3px 6px; border-top: 1px solid #000;">
                            Anita Widya
                        </td>
                        <td style="padding:3px 6px; text-align:left; border-top: 1px solid #000;">
                            Date &nbsp;:&nbsp;
                        </td>
                        <td style="padding:3px 6px; border-top: 1px solid #000;"></td>
                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>

    {{-- ═══ NOTES ═══ --}}
    <div class="footer-note">
        Ket :<br>
        @if($receipt->notes)
            {!! nl2br(e($receipt->notes)) !!}
        @else
            Mohon diisi dan di tandatangani payment schedulnya, lalu dikirim kembali melalui :<br>
            Email : finance@stintegrator.com
        @endif
    </div>

    <hr class="page-divider">

</div>
</body>
</html>