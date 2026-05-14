<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rencana Produksi {{ $production->production_number }}</title>
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
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 2px;
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

        .info-block {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #c8d0dc;
            margin-bottom: 8px;
        }
        .info-block td {
            padding: 4px 8px;
            font-size: 8.5px;
            vertical-align: top;
        }
        .info-lbl {
            font-size: 7px;
            font-weight: bold;
            color: #888;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 1px;
        }
        .info-val {
            font-size: 9px;
            font-weight: bold;
            color: #111;
        }
        .info-sub {
            font-size: 8px;
            color: #444;
            line-height: 1.5;
        }
        .info-divider { border-right: 1px solid #dde3ec; }
        .info-sep { border-top: 1px dotted #dde3ec; }

        .product-block {
            border: 1px solid #c8d0dc;
            margin-bottom: 6px;
        }
        .product-header {
            background: #1e3a5f;
            color: #fff;
            font-size: 8.5px;
            font-weight: bold;
            padding: 4px 8px;
            text-transform: uppercase;
        }
        .product-header .qty-info {
            float: right;
            font-weight: normal;
            font-size: 7.5px;
            color: #b8cae0;
        }

        .mat-table {
            width: 100%;
            border-collapse: collapse;
        }
        .mat-table th {
            background: #2c4f8a;
            color: #fff;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 3px 5px;
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
        .col-mat  { }
        .col-qty  { width: 50px; text-align: right; }
        .col-sat  { width: 40px; text-align: center; }

        .row-odd  { background: #fff; }
        .row-even { background: #f5f7fc; }

        .summary-wrap {
            width: 260px;
            margin-left: auto;
            border-collapse: collapse;
            margin-top: 8px;
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
                        marketing@stintegrator.com
                    </div>
                </td>
                <td class="title-cell">
                    <div class="doc-title">RENCANA PRODUKSI</div>
                    <table class="meta-table">
                        <tr><td class="meta-label">No.</td><td class="meta-value">{{ $production->production_number }}</td></tr>
                        <tr><td class="meta-label">Tanggal</td><td class="meta-value">{{ $production->date->format('d M Y') }}</td></tr>
                        @if($production->target_date)
                        <tr><td class="meta-label">Target</td><td class="meta-value">{{ $production->target_date->format('d M Y') }}</td></tr>
                        @endif
                        <tr><td class="meta-label">Status</td><td class="meta-value">{{ ucfirst(str_replace('_',' ',$production->status)) }}</td></tr>
                        <tr><td class="meta-label">Ref. SO</td><td class="meta-value">{{ $production->so_number ?: '-' }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <hr class="divider">

        {{-- ═══ INFO ═══ --}}
        <table class="info-block">
            <tr>
                <td class="info-divider" style="width:50%;">
                    <div class="info-lbl">Project</div>
                    <div class="info-val">{{ $production->project_name ?: '-' }}</div>
                    <div class="info-sub" style="margin-top:2px;">
                        Klien: {{ $production->client_company ?: '-' }}
                    </div>
                </td>
                <td style="width:50%;">
                    <div class="info-lbl">Dari</div>
                    <div class="info-val">PT. Sistem Teknologi Integrator</div>
                    <div class="info-sub" style="margin-top:2px;">
                        Ruko Palazo Blok AB 46, Ciantra<br>
                        Cikarang Selatan, Bekasi 17530
                    </div>
                </td>
            </tr>
            @if($production->notes)
            <tr>
                <td colspan="2" class="info-sep">
                    <div class="info-lbl">Catatan</div>
                    <div class="info-sub">{{ $production->notes }}</div>
                </td>
            </tr>
            @endif
        </table>

        {{-- ═══ PRODUCTS ═══ --}}
        @foreach($production->items as $pi => $product)
        <div class="product-block">
            <div class="product-header">
                {{ $loop->iteration }}. {{ $product->product_name }}
                <span class="qty-info">Qty: {{ number_format($product->product_qty, 2) }} {{ $product->unit }} | {{ ucfirst(str_replace('_', ' ', $product->status)) }}</span>
            </div>

            @if($product->materials->isNotEmpty())
            <table class="mat-table">
                <thead>
                    <tr>
                        <th class="col-no">#</th>
                        <th class="th-left col-mat">Bahan Baku</th>
                        <th class="th-center col-sat">Satuan</th>
                        <th class="th-right col-qty">Qty Dibutuhkan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->materials as $mi => $mat)
                    <tr class="{{ $mi % 2 === 0 ? 'row-odd' : 'row-even' }}">
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $mat->nama_bahan_baku }}</strong></td>
                        <td style="text-align:center;">{{ $mat->satuan }}</td>
                        <td style="text-align:right;">{{ number_format($mat->qty_required, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div style="padding:6px 8px;font-size:8px;color:#888;font-style:italic;">
                Belum ada bahan baku yang ditentukan.
            </div>
            @endif
        </div>
        @endforeach

        {{-- ═══ SUMMARY ═══ --}}
        @php
            $totalProducts = $production->items->count();
            $totalMaterials = $production->items->sum(fn($p) => $p->materials->count());
        @endphp
        <table class="summary-wrap">
            <tr>
                <td class="summary-label">Jumlah Produk</td>
                <td class="summary-value">{{ $totalProducts }}</td>
            </tr>
            <tr>
                <td class="summary-label">Total Jenis Bahan Baku</td>
                <td class="summary-value">{{ $totalMaterials }}</td>
            </tr>
        </table>

        {{-- ═══ FOOTER ═══ --}}
        @if($production->notes)
        <div class="footer-note">
            <strong>Catatan:</strong><br>
            {!! nl2br(e($production->notes)) !!}
        </div>
        @endif

        {{-- ═══ SIGNATURE ═══ --}}
        <table class="signature-wrap">
            <tr>
                <td>
                    <div>Dibuat Oleh,</div>
                    <div class="signature-line"></div>
                    <div style="margin-top:4px;font-weight:bold;">Produksi</div>
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
