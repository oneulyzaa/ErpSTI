<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sales Order {{ $salesOrder->so_number }}</title>
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
            width: 76px;
        }
        .meta-value {
            background: #fff;
            min-width: 120px;
            font-family: 'DejaVu Sans Mono', monospace;
        }

        .divider {
            border: none;
            border-top: 2.5px solid #1B5DBC;
            margin: 7px 0 6px;
        }

        .section-bar {
            background: #1B5DBC;
            color: #fff;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 3px 6px;
            margin-bottom: 0;
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
        .client-divider { border-right: 1px solid #dde3ec; }
        .client-sep { border-top: 1px dotted #dde3ec; }

        .mat-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .mat-table th {
            background: #2c4f8a;
            color: #fff;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 3.5px 5px;
            border: 1px solid #1e3a6e;
        }
        .mat-table td {
            border: 1px solid #ccd3df;
            padding: 2.5px 5px;
            font-size: 8px;
            vertical-align: middle;
        }
        .mat-table .th-left { text-align: left; }
        .mat-table .th-right { text-align: right; }
        .mat-table .th-center { text-align: center; }

        .col-no   { width: 22px; text-align: center; }
        .col-mat  { /* auto */ }
        .col-qty  { width: 38px; text-align: right; }
        .col-up   { width: 80px; text-align: right; }
        .col-sub  { width: 80px; text-align: right; }

        .row-odd  { background: #fff; }
        .row-even { background: #f5f7fc; }

        .total-mat-row td {
            background: #d6e4f5;
            font-weight: bold;
            border-top: 2px solid #2c4f8a;
            font-size: 8.5px;
        }

        .lab-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .lab-table th {
            background: #1B5DBC;
            color: #fff;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 3.5px 5px;
            border: 1px solid #144da0;
        }
        .lab-table td {
            border: 1px solid #ccd3df;
            padding: 2.5px 5px;
            font-size: 8px;
            vertical-align: middle;
        }

        .col-lno  { width: 22px; text-align: center; }
        .col-lab  { /* auto */ }
        .col-mp   { width: 28px; text-align: center; }
        .col-days { width: 32px; text-align: center; }
        .col-rate { width: 70px; text-align: right; }
        .col-lsub { width: 80px; text-align: right; }

        .total-lab-row td {
            background: #d4e4fc;
            font-weight: bold;
            border-top: 2px solid #1B5DBC;
            font-size: 8.5px;
        }

        .summary-wrap {
            width: 280px;
            margin-left: auto;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .summary-wrap td {
            padding: 2px 8px;
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
                    <div class="doc-title">SALES ORDER</div>
                    <table class="meta-table">
                        <tr><td class="meta-label">No. SO</td><td class="meta-value">{{ $salesOrder->so_number }}</td></tr>
                        <tr><td class="meta-label">Tanggal</td><td class="meta-value">{{ $salesOrder->date->format('d M Y') }}</td></tr>
                        @if($salesOrder->delivery_date)
                        <tr><td class="meta-label">Pengiriman</td><td class="meta-value">{{ $salesOrder->delivery_date->format('d M Y') }}</td></tr>
                        @endif
                        <tr><td class="meta-label">Status</td><td class="meta-value">{{ ucfirst(str_replace('_',' ',$salesOrder->status)) }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <hr class="divider">

        {{-- ═══ CLIENT INFO ═══ --}}
        <table class="client-block">
            <tr>
                <td class="client-divider" style="width:50%;">
                    <div class="client-lbl">Kepada</div>
                    <div class="client-val">{{ $salesOrder->client_company }}</div>
                    <div class="client-sub" style="margin-top:2px;">
                        Kontak: {{ $salesOrder->client_name }}<br>
                        @if($salesOrder->client_attention)Attn: {{ $salesOrder->client_attention }}<br>@endif
                        @if($salesOrder->client_cc)CC: {{ $salesOrder->client_cc }}<br>@endif
                        @if($salesOrder->client_email){{ $salesOrder->client_email }}@endif
                    </div>
                </td>
                <td style="width:50%;">
                    <div class="client-lbl">Dari</div>
                    <div class="client-val">PT. Sistem Teknologi Integrator</div>
                    <div class="client-sub" style="margin-top:2px;">
                        Ruko Palazo Blok AB 46, Ciantra<br>
                        Cikarang Selatan, Bekasi 17530
                    </div>
                </td>
            </tr>
            @if($salesOrder->project_name)
            <tr>
                <td colspan="2" class="client-sep">
                    <div class="client-lbl">Nama Project</div>
                    <div class="client-val">{{ $salesOrder->project_name }}</div>
                </td>
            </tr>
            @endif
            @if($salesOrder->quote_number)
            <tr>
                <td colspan="2" class="client-sep">
                    <div class="client-lbl">Referensi Quotation</div>
                    <div class="client-sub" style="font-family:monospace;">{{ $salesOrder->quote_number }}</div>
                </td>
            </tr>
            @endif
            @if($salesOrder->description_of_work)
            <tr>
                <td colspan="2" class="client-sep">
                    <div class="client-lbl">Deskripsi Pekerjaan</div>
                    <div class="client-sub">{{ $salesOrder->description_of_work }}</div>
                </td>
            </tr>
            @endif
        </table>

        {{-- ═══ MATERIAL TABLE ═══ --}}
        @if($salesOrder->items->isNotEmpty())
        <div class="section-bar">Material</div>
        <table class="mat-table">
            <thead>
                <tr>
                    <th class="col-no">#</th>
                    <th class="th-left col-mat">Material / Jasa</th>
                    <th class="th-center col-qty">Qty</th>
                    <th class="th-center" style="width:30px;">Sat</th>
                    <th class="th-right col-up">Harga Satuan</th>
                    <th class="th-right col-sub">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesOrder->items as $i => $item)
                <tr class="{{ $i % 2 === 0 ? 'row-odd' : 'row-even' }}">
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <div style="font-weight:bold;">{{ $item->material_name }}</div>
                        @if($item->description)
                            <div style="font-size:7px;color:#666;">{{ $item->description }}</div>
                        @endif
                    </td>
                    <td style="text-align:right;">{{ number_format($item->qty, 2, ',', '.') }}</td>
                    <td style="text-align:center;">{{ $item->unit }}</td>
                    <td style="text-align:right;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td style="text-align:right;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-mat-row">
                    <td colspan="5" style="text-align:right;">Total Material</td>
                    <td style="text-align:right;">Rp {{ number_format($salesOrder->subtotal_material, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        @endif

        {{-- ═══ LABOR TABLE ═══ --}}
        @if($salesOrder->labors->isNotEmpty())
        <div class="section-bar">Labor</div>
        <table class="lab-table">
            <thead>
                <tr>
                    <th class="col-lno">#</th>
                    <th class="th-left col-lab">Pekerjaan</th>
                    <th class="th-center col-mp">MP</th>
                    <th class="th-center col-days">Days</th>
                    <th class="th-right col-rate">Rate / Hari</th>
                    <th class="th-right col-lsub">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesOrder->labors as $i => $labor)
                <tr class="{{ $i % 2 === 0 ? 'row-odd' : 'row-even' }}">
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $labor->labor_name }}</strong></td>
                    <td style="text-align:center;">{{ $labor->mp }}</td>
                    <td style="text-align:center;">{{ number_format($labor->days, 0) }}</td>
                    <td style="text-align:right;">Rp {{ number_format($labor->rate, 0, ',', '.') }}</td>
                    <td style="text-align:right;">Rp {{ number_format($labor->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-lab-row">
                    <td colspan="5" style="text-align:right;">Total Labor</td>
                    <td style="text-align:right;">Rp {{ number_format($salesOrder->subtotal_labor, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        @endif

        {{-- ═══ SUMMARY ═══ --}}
        <table class="summary-wrap">
            <tr>
                <td class="summary-label">Total Material</td>
                <td class="summary-value">Rp {{ number_format($salesOrder->subtotal_material, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">Total Labor</td>
                <td class="summary-value">Rp {{ number_format($salesOrder->subtotal_labor, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">Subtotal</td>
                <td class="summary-value">Rp {{ number_format($salesOrder->subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">PPN ({{ number_format($salesOrder->tax_percentage, 0) }}%)</td>
                <td class="summary-value">Rp {{ number_format($salesOrder->tax_amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="summary-total">
                <td>TOTAL</td>
                <td class="summary-value">Rp {{ number_format($salesOrder->total, 0, ',', '.') }}</td>
            </tr>
        </table>

        {{-- ═══ FOOTER / NOTES ═══ --}}
        @if($salesOrder->notes)
        <div class="footer-note">
            <strong>Terms & Conditions:</strong><br>
            {!! nl2br(e($salesOrder->notes)) !!}
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
