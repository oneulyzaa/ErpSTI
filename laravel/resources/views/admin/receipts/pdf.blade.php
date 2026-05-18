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
            font-size: 9px;
            color: #1a1a1a;
            background: #fff;
        }

        .page { padding: 20px 26px 16px; }

        .header-wrap {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .header-wrap td { vertical-align: top; padding: 0; }

        .logo-cell { width: 220px; }
        .logo-cell img { width: 165px; display: block; }

        .company-info {
            font-size: 7.5px;
            color: #555;
            line-height: 1.65;
            margin-top: 5px;
        }
        .company-info a { color: #1B5DBC; text-decoration: none; }

        .title-cell { text-align: right; }

        .doc-title {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 3px;
            color: #1B5DBC;
            text-transform: uppercase;
            line-height: 1;
            margin-bottom: 8px;
        }

        .meta-table {
            border-collapse: collapse;
            margin-left: auto;
        }
        .meta-table td {
            border: 1px solid #c0c8d8;
            padding: 2.5px 7px;
            font-size: 8px;
            white-space: nowrap;
        }
        .meta-label {
            background: #e4eaf5;
            font-weight: bold;
            color: #333;
            text-align: right;
            width: 90px;
        }
        .meta-value {
            background: #fff;
            min-width: 120px;
            font-family: 'DejaVu Sans Mono', monospace;
        }

        .status-badge {
            display: inline-block;
            padding: 1px 8px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .status-draft     { background: #e2e8f0; color: #475569; }
        .status-confirmed { background: #dcfce7; color: #15803d; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }

        .divider {
            border: none;
            border-top: 2.5px solid #1B5DBC;
            margin: 7px 0 6px;
        }

        .client-block {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #c8d0dc;
            margin-bottom: 6px;
        }
        .client-block td {
            padding: 4px 8px;
            font-size: 8.5px;
            vertical-align: top;
        }

        .client-lbl {
            font-size: 7px;
            font-weight: bold;
            color: #888;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 1px;
        }
        .client-val {
            font-size: 9px;
            font-weight: bold;
            color: #111;
        }
        .client-sub {
            font-size: 8px;
            color: #444;
            line-height: 1.5;
        }
        .client-sep { border-top: 1px dotted #dde3ec; }

        .amount-box {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .amount-box td {
            border: 1px solid #c8d0dc;
            padding: 8px 12px;
        }
        .amount-label {
            font-size: 7.5px;
            font-weight: bold;
            color: #888;
            text-transform: uppercase;
            letter-spacing: .5px;
            background: #f0f4fc;
            width: 140px;
            text-align: right;
        }
        .amount-value {
            font-size: 18px;
            font-weight: bold;
            color: #1B5DBC;
            font-family: 'DejaVu Sans Mono', monospace;
            text-align: right;
        }

        .payment-detail {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .payment-detail td {
            border: 1px solid #ccd3df;
            padding: 3px 8px;
            font-size: 8.5px;
        }
        .payment-detail .pd-label {
            font-weight: bold;
            color: #555;
            background: #f5f7fc;
            width: 140px;
        }
        .payment-detail .pd-value {
            font-family: 'DejaVu Sans Mono', monospace;
        }

        .invoice-ref {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .invoice-ref td {
            border: 1px solid #c8d0dc;
            padding: 4px 8px;
            font-size: 8.5px;
        }
        .invoice-ref .ir-label {
            font-weight: bold;
            color: #555;
            background: #f0f4fc;
            width: 140px;
        }
        .invoice-ref .ir-value {
            font-family: 'DejaVu Sans Mono', monospace;
        }
        .invoice-ref .ir-status {
            font-weight: bold;
        }

        .footer-note {
            margin-top: 10px;
            font-size: 7.5px;
            color: #555;
            line-height: 1.6;
            border-top: 1px solid #dde3ec;
            padding-top: 8px;
        }

        .signature-wrap {
            width: 100%;
            margin-top: 20px;
        }
        .signature-wrap td {
            width: 50%;
            text-align: center;
            padding: 0 20px;
            font-size: 8.5px;
        }
        .signature-line {
            margin-top: 36px;
            border-top: 1px solid #333;
            width: 160px;
            display: inline-block;
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
                        <div style="font-size:18px;font-weight:bold;color:#1B5DBC;">PT. STI</div>
                    @endif
                    <div class="company-info">
                        Ruko Palazo Blok AB 46, Ciantra<br>
                        Cikarang Selatan, Bekasi 17530<br>
                        Telp: +6221-22108157<br>
                        <a href="mailto:marketing@stintegrator.com">marketing@stintegrator.com</a>
                    </div>
                </td>
                <td class="title-cell">
                    <div class="doc-title">TANDA TERIMA</div>
                    <table class="meta-table">
                        <tr><td class="meta-label">No. Tanda Terima</td><td class="meta-value">{{ $receipt->receipt_number }}</td></tr>
                        <tr><td class="meta-label">Tanggal</td><td class="meta-value">{{ $receipt->date->format('d M Y') }}</td></tr>
                        <tr><td class="meta-label">Status</td><td class="meta-value"><span class="status-badge status-{{ $receipt->status }}">{{ ucfirst($receipt->status) }}</span></td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <hr class="divider">

        {{-- ═══ CLIENT INFO ═══ --}}
        <table class="client-block">
            <tr>
                <td style="width:100%;">
                    <div class="client-lbl">Diterima Dari</div>
                    <div class="client-val">{{ $receipt->client_company }}</div>
                    <div class="client-sub" style="margin-top:2px;">
                        Kontak: {{ $receipt->client_name }}<br>
                        @if($receipt->client_attention)Attn: {{ $receipt->client_attention }}<br>@endif
                        @if($receipt->client_email){{ $receipt->client_email }}@endif
                    </div>
                </td>
            </tr>
            @if($receipt->description)
            <tr>
                <td class="client-sep">
                    <div class="client-lbl">Keterangan</div>
                    <div class="client-sub">{{ $receipt->description }}</div>
                </td>
            </tr>
            @endif
        </table>

        {{-- ═══ AMOUNT ═══ --}}
        <table class="amount-box">
            <tr>
                <td class="amount-label">Jumlah Pembayaran</td>
                <td class="amount-value">Rp {{ number_format($receipt->amount, 0, ',', '.') }}</td>
            </tr>
        </table>

        {{-- ═══ PAYMENT DETAIL ═══ --}}
        <table class="payment-detail">
            <tr>
                <td class="pd-label">Metode Pembayaran</td>
                <td class="pd-value">
                    @php
                        $methodLabels = ['cash'=>'Cash / Tunai','transfer'=>'Transfer Bank','cheque'=>'Cek / Giro','other'=>'Lainnya'];
                    @endphp
                    {{ $methodLabels[$receipt->payment_method] ?? $receipt->payment_method }}
                </td>
            </tr>
            @if($receipt->payment_reference)
            <tr>
                <td class="pd-label">No. Referensi</td>
                <td class="pd-value">{{ $receipt->payment_reference }}</td>
            </tr>
            @endif
        </table>

        {{-- ═══ INVOICE REFERENCE ═══ --}}
        @if($receipt->invoice)
        <table class="invoice-ref">
            <tr>
                <td class="ir-label">Referensi Invoice</td>
                <td class="ir-value">{{ $receipt->invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td class="ir-label">Total Invoice</td>
                <td class="ir-value">Rp {{ number_format($receipt->invoice->total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="ir-label">Status Invoice</td>
                <td class="ir-value ir-status">{{ ucfirst(str_replace('_',' ',$receipt->invoice->status)) }}</td>
            </tr>
        </table>
        @endif

        {{-- ═══ NOTES ═══ --}}
        @if($receipt->notes)
        <div class="footer-note">
            <strong>Catatan:</strong><br>
            {!! nl2br(e($receipt->notes)) !!}
        </div>
        @endif

        {{-- ═══ SIGNATURE ═══ --}}
        <table class="signature-wrap">
            <tr>
                <td>
                    <div>Diterima Oleh,</div>
                    <div class="signature-line"></div>
                    <div style="margin-top:4px;font-weight:bold;">Finance</div>
                </td>
                <td>
                    <div>Mengetahui,</div>
                    <div class="signature-line"></div>
                    <div style="margin-top:4px;font-weight:bold;">Manager</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
