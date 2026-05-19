{{-- pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #1a1a1a;
            background: #fff;
        }

        .page { padding: 20px 26px 16px; }

        /* ── HEADER ── */
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

        .meta-table { border-collapse: collapse; margin-left: auto; }
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
            width: 76px;
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
        .status-sent      { background: #dbeafe; color: #1d4ed8; }
        .status-paid      { background: #dcfce7; color: #15803d; }
        .status-overdue   { background: #fee2e2; color: #b91c1c; }
        .status-cancelled { background: #f3e8ff; color: #7c3aed; }

        .divider {
            border: none;
            border-top: 2.5px solid #1B5DBC;
            margin: 7px 0 6px;
        }

        /* ── CLIENT BLOCK ── */
        .client-block {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #c8d0dc;
            margin-bottom: 8px;
        }
        .client-block td {
            padding: 5px 8px;
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
        .client-val  { font-size: 9px; font-weight: bold; color: #111; }
        .client-sub  { font-size: 8px; color: #444; line-height: 1.5; }
        .client-sep  { border-top: 1px dotted #dde3ec; }

        /* ── SECTION BAR ── */
        .section-bar {
            background: #1e3a5f;
            color: #fff;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 3px 6px;
            margin-bottom: 0;
        }

        /* ── ITEM TABLE ── */
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .item-table th {
            background: #1e3a5f;
            color: #fff;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 3.5px 5px;
            border: 1px solid #1e3a6e;
        }
        .item-table td {
            border: 1px solid #ccd3df;
            padding: 3px 5px;
            font-size: 8px;
            vertical-align: middle;
        }
        .th-left   { text-align: left; }
        .th-right  { text-align: right; }
        .th-center { text-align: center; }

        .col-no   { width: 22px; text-align: center; }
        .col-qty  { width: 40px; text-align: right; }
        .col-unit { width: 32px; text-align: center; }
        .col-up   { width: 85px; text-align: right; }
        .col-sub  { width: 85px; text-align: right; }

        .row-odd  { background: #fff; }
        .row-even { background: #f5f7fc; }

        .total-items-row td {
            background: #d6e4f5;
            font-weight: bold;
            border-top: 2px solid #1e3a5f;
            font-size: 8.5px;
        }

        /* ── SUMMARY ── */
        .summary-wrap {
            width: 260px;
            margin-left: auto;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .summary-wrap td {
            padding: 2.5px 8px;
            font-size: 8.5px;
            border: 1px solid #c8d0dc;
        }
        .summary-label {
            text-align: left;
            font-weight: bold;
            color: #444;
            background: #f0f4fc;
        }
        .summary-value {
            text-align: right;
            font-family: 'DejaVu Sans Mono', monospace;
        }
        .summary-total td {
            font-size: 10px;
            font-weight: bold;
            background: #e4eaf5;
            border-top: 2px solid #1B5DBC;
        }
        .summary-total .summary-value { color: #1B5DBC; }

        /* ── FOOTER NOTES ── */
        .footer-note {
            margin-top: 10px;
            font-size: 7.5px;
            color: #555;
            line-height: 1.6;
            border-top: 1px solid #dde3ec;
            padding-top: 8px;
        }

        /* ── SIGNATURE ── */
        .signature-wrap { width: 100%; margin-top: 24px; }
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
                <div class="doc-title">INVOICE</div>
                <table class="meta-table">
                    <tr><td class="meta-label">No. Invoice</td><td class="meta-value">{{ $invoice->invoice_number }}</td></tr>
                    <tr><td class="meta-label">Tanggal</td><td class="meta-value">{{ $invoice->date->format('d M Y') }}</td></tr>
                    @if($invoice->due_date)
                    <tr><td class="meta-label">Jatuh Tempo</td><td class="meta-value">{{ $invoice->due_date->format('d M Y') }}</td></tr>
                    @endif
                    @if($invoice->so_number)
                    <tr><td class="meta-label">Ref. SO</td><td class="meta-value">{{ $invoice->so_number }}</td></tr>
                    @endif
                    <tr>
                        <td class="meta-label">Status</td>
                        <td class="meta-value">
                            <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst(str_replace('_', ' ', $invoice->status)) }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <hr class="divider">

    {{-- ═══ CLIENT INFO ═══ --}}
    <table class="client-block">
        <tr>
            <td>
                <div class="client-lbl">Kepada</div>
                <div class="client-val">{{ $invoice->client_company }}</div>
                <div class="client-sub" style="margin-top:2px;">
                    Kontak: {{ $invoice->client_name }}
                    @if($invoice->client_attention)<br>Attn: {{ $invoice->client_attention }}@endif
                    @if($invoice->client_cc)<br>CC: {{ $invoice->client_cc }}@endif
                    @if($invoice->client_email)<br>{{ $invoice->client_email }}@endif
                </div>
            </td>
        </tr>
        @if($invoice->description)
        <tr>
            <td class="client-sep">
                <div class="client-lbl">Deskripsi</div>
                <div class="client-sub">{{ $invoice->description }}</div>
            </td>
        </tr>
        @endif
    </table>

    {{-- ═══ Produksi ITEMS TABLE ═══ --}}
    @if($invoice->items->isNotEmpty())
    <div class="section-bar">Item Produksi</div>
    <table class="item-table">
        <thead>
            <tr>
                <th class="col-no">#</th>
                <th class="th-left">Nama Item</th>
                <th class="th-center col-unit">Sat.</th>
                <th class="th-right col-qty">Qty</th>
                <th class="th-right col-up">Harga Satuan</th>
                <th class="th-right col-sub">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $i => $item)
            <tr class="{{ $i % 2 === 0 ? 'row-odd' : 'row-even' }}">
                <td style="text-align:center;">{{ $i + 1 }}</td>
                <td>
                    <div style="font-weight:bold;">{{ $item->item_name }}</div>
                    @if($item->description)
                        <div style="font-size:7px;color:#666;">{{ $item->description }}</div>
                    @endif
                </td>
                <td style="text-align:center;">{{ $item->unit }}</td>
                <td style="text-align:right;">{{ number_format($item->qty, 2, ',', '.') }}</td>
                <td style="text-align:right;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td style="text-align:right;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-items-row">
                <td colspan="5" style="text-align:right;">Total Produksi</td>
                <td style="text-align:right;">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- ═══ SUMMARY ═══ --}}
    <table class="summary-wrap">
        <tr>
            <td class="summary-label">Subtotal</td>
            <td class="summary-value">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="summary-label">PPN ({{ number_format($invoice->tax_percentage, 0) }}%)</td>
            <td class="summary-value">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</td>
        </tr>
        <tr class="summary-total">
            <td>TOTAL</td>
            <td class="summary-value">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- ═══ TERMS & CONDITIONS ═══ --}}
    @if($invoice->term_and_condition)
    <div class="footer-note">
        <strong>Syarat & Ketentuan:</strong><br>
        {!! nl2br(e($invoice->term_and_condition)) !!}
    </div>
    @endif

    {{-- ═══ NOTES ═══ --}}
    @if($invoice->notes)
    <div class="footer-note">
        <strong>Catatan:</strong><br>
        {!! nl2br(e($invoice->notes)) !!}
    </div>
    @endif

    {{-- ═══ SIGNATURE ═══ --}}
    <table class="signature-wrap">
        <tr>
            <td>
                <div>Dibuat Oleh,</div>
                <div class="signature-line"></div>
                <div style="margin-top:4px;font-weight:bold;">Marketing</div>
            </td>
            <td>
                <div>Disetujui Oleh,</div>
                <div class="signature-line"></div>
                <div style="margin-top:4px;font-weight:bold;">Manager</div>
            </td>
        </tr>
    </table>

</div>
</body>
</html>