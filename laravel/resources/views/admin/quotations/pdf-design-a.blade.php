<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Project Quote {{ $quotation->nomor_quotation }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #1a1a1a;
            background: #fff;
        }
        /* .page { padding: 20px 26px 16px; } */
        /* ══════════════════════════════
           HEADER
        ══════════════════════════════ */
        .header-wrap {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .header-wrap td { vertical-align: top; padding: 0; }
        .logo-cell { width: 220px; }
        .logo-cell img { width: 95px; display: block; }
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
            /* font-family: 'DejaVu Sans Mono', monospace; */
        }
        /* ══════════════════════════════
           DIVIDERS
        ══════════════════════════════ */
        .divider {
            border: none;
            border-top: 2.5px solid #1B5DBC;
            margin: 7px 0 6px;
        }
        /* ══════════════════════════════
           SECTION TITLE BAR
        ══════════════════════════════ */
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
        /* ══════════════════════════════
           CLIENT BLOCK
        ══════════════════════════════ */
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
        /* ══════════════════════════════
           MATERIAL TABLE
        ══════════════════════════════ */
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
        /* ══════════════════════════════
           LABOR TABLE
        ══════════════════════════════ */
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
        .lab-table .th-left { text-align: left; }
        .lab-table .th-right { text-align: right; }
        .lab-table .th-center { text-align: center; }
        .total-lab-row td {
            background: #d6e4f5;
            font-weight: bold;
            border-top: 2px solid #1B5DBC;
            font-size: 8.5px;
        }
        /* ══════════════════════════════
           GRAND TOTAL
        ══════════════════════════════ */
        .grand-wrap {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .grand-wrap td { padding: 0; vertical-align: top; }
        .grand-inner {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #c0c8d8;
        }
        .grand-inner td {
            padding: 2.5px 8px;
            font-size: 8.5px;
        }
        .grand-lbl { font-weight: bold; color: #444; }
        .grand-val {
            text-align: right;
            font-family: 'DejaVu Sans Mono', monospace;
            font-weight: bold;
            color: #1a1a1a;
        }
        .grand-total-row td {
            background: #1B5DBC;
            color: #fff;
            font-size: 10.5px;
            font-weight: bold;
            padding: 5px 8px;
        }
        .grand-total-row .grand-val { color: #fff; }
        /* ══════════════════════════════
           MONO UTILITY
        ══════════════════════════════ */
        .mono { font-family: 'DejaVu Sans Mono', monospace; }
        .tc { text-align: center; }
        .tr { text-align: right; }
        .tl { text-align: left; }
        .bold { font-weight: bold; }
        .muted { color: #bbb; }

        /* ══════════════════════════════
           OPSI A — TERMS (full-width)
        ══════════════════════════════ */
        .terms-bar {
            background: #1B5DBC;
            color: #fff;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 8px;
        }
        .terms-body {
            border: 1px solid #c0c8d8;
            border-top: none;
            padding: 7px 10px;
            font-size: 7.5px;
            color: #333;
            line-height: 1.8;
            margin-bottom: 10px;
        }

        /* ══════════════════════════════
           OPSI A — SIGNATURE BLOCK
        ══════════════════════════════ */
        .sig-section {
            border-top: 2px solid #1B5DBC;
            padding-top: 8px;
            page-break-inside: avoid;
        }
        .sig-section-lbl {
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #888;
            margin-bottom: 8px;
        }
        .sig-outer {
            width: 100%;
            border-collapse: collapse;
        }
        .sig-outer td {
            text-align: center;
            vertical-align: bottom;
            padding: 0 6px;
            width: 33.33%;
            border-right: 1px dotted #dde3ec;
        }
        .sig-outer td:last-child { border-right: none; }
        .sig-box { height: 52px; }
        .sig-name-line {
            border-top: 1.5px solid #333;
            padding-top: 4px;
            font-size: 8px;
            font-weight: bold;
            color: #111;
            white-space: nowrap;
            overflow: hidden;
        }
        .sig-role {
            font-size: 7px;
            color: #888;
            margin-top: 2px;
            font-style: italic;
        }
        /* Date row below signatures */
        .sig-date-outer {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .sig-date-outer td {
            text-align: center;
            padding: 0 6px;
            width: 33.33%;
        }
        .sig-date-lbl {
            font-size: 6.5px;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 2px;
        }
        .sig-date-line {
            /* border-top: 1px solid #ccc; */
            padding-top: 3px;
            font-size: 7px;
            color: #bbb;
        }

        /* ══════════════════════════════
           CONTACT + THANK YOU (bawah kiri)
        ══════════════════════════════ */
        .thankyou {
            font-size: 15px;
            font-weight: bold;
            color: #1B5DBC;
            margin-top: 10px;
            letter-spacing: 1px;
        }
        .contact-box {
            font-size: 7.5px;
            color: #555;
            margin-top: 4px;
            line-height: 1.7;
        }

        /* ══════════════════════════════
           FOOTER
        ══════════════════════════════ */
        .footer-strip {
            background: #1B5DBC;
            height: 4px;
            margin-top: 12px;
        }
        .footer-text {
            text-align: center;
            font-size: 6.5px;
            color: #aaa;
            margin-top: 4px;
        }
        @page {
            margin: 20px 26px 20px 26px !important;
        }
    </style>
</head>
<body>
<div class="page">
    {{-- ══════════════════════════════
         HEADER
    ══════════════════════════════ --}}
    <table class="header-wrap">
        <tr>
            <td class="logo-cell">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="STI Logo">
                @else
                    <div style="font-size:15px;font-weight:bold;color:#1B5DBC;line-height:1.3;">
                        PT. SISTEM TEKNOLOGI<br>INTEGRATOR
                    </div>
                @endif
                <div class="company-info">
                    Ruko Palazo Blok AB 46, Ciantra,<br>
                    Cikarang Selatan, Bekasi 17530<br>
                    Telp: +6221-22108157 &nbsp;|&nbsp;
                    <a href="mailto:marketing@stintegrator.com">marketing@stintegrator.com</a>
                </div>
            </td>
            <td class="title-cell">
                <div class="doc-title">Project&nbsp;Quote</div>
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Date</td>
                        <td class="meta-value">{{ $quotation->tanggal_pembuatan->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Quote #</td>
                        <td class="meta-value">{{ $quotation->nomor_quotation }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Customer ID</td>
                        <td class="meta-value">{{ $quotation->client->id_customer ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Valid Until</td>
                        <td class="meta-value">{{ $quotation->valid_sampai->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <hr class="divider">

    {{-- ══════════════════════════════
         CLIENT BLOCK
    ══════════════════════════════ --}}
    @php
        $clCompany = $quotation->client?->nama_perusahaan ?? '-';
        $clName    = $quotation->client?->nama_kontak ?? '-';
        $clEmail   = $quotation->client?->email_perusahaan ?? '-';
        $clAddr    = $quotation->client?->alamat_perusahaan ?? '-';
    @endphp
    <table class="client-block">
        <tr>
            <td class="client-divider" style="width:30%;">
                <div class="client-lbl">Attention</div>
                <div class="client-val">{{ $clName }}</div>
            </td>
            <td class="client-divider" style="width:30%;">
                <div class="client-lbl">Company</div>
                <div class="client-val">{{ $clCompany }}</div>
            </td>
            <td style="width:40%;">
                <div class="client-lbl">Project</div>
                <div class="client-sub">{{ $quotation->nama_project ?? '-' }}</div>
            </td>
        </tr>
        <tr class="client-sep">
            <td class="client-divider">
                <div class="client-lbl">Contact Name</div>
                <div class="client-sub">{{ $clName }}</div>
            </td>
            <td class="client-divider">
                <div class="client-lbl">Email</div>
                <div class="client-sub">{{ $clEmail }}</div>
            </td>
            <td>
                <div class="client-lbl">Status</div>
                <div class="client-sub">{{ ucfirst($quotation->status ?? '-') }}</div>
            </td>
        </tr>
        @if($clAddr)
        <tr class="client-sep">
            <td colspan="3">
                <div class="client-lbl">Address</div>
                <div class="client-sub">{{ $clAddr }}</div>
            </td>
        </tr>
        @endif
    </table>

    {{-- ══════════════════════════════
         MATERIAL TABLE (with material details)
    ══════════════════════════════ --}}
    @php
        $items    = $quotation->items;
        $itemList = $items->values();
        // Calculate total: product subtotals + material subtotals
        $totalMat = 0;
        $tableRows = [];
        foreach ($itemList as $i => $item) {
            $itemSubtotal = ($item->jumlah_item ?? 0) * ($item->harga_item ?? 0);
            $totalMat += $itemSubtotal;
            $tableRows[] = ['type'=>'product', 'data'=>$item, 'idx'=>$i+1, 'subtotal'=>$itemSubtotal];
            if ($item->materials && $item->materials->count()) {
                foreach ($item->materials as $mi => $mat) {
                    $matSubtotal = ($mat->jumlah_material ?? 0) * ($mat->harga_material ?? 0);
                    $totalMat += $matSubtotal;
                    $tableRows[] = ['type'=>'material', 'data'=>$mat, 'idx'=>($i+1).'.'.($mi+1), 'subtotal'=>$matSubtotal];
                }
            }
        }
        $rowCount = count($tableRows);
        $padCount = max(0, 18 - $rowCount);
    @endphp
    <div class="section-bar">Produksi</div>
    <table class="mat-table">
        <thead>
            <tr>
                <th class="col-no th-center">#</th>
                <th class="th-left">Produk / Material</th>
                <th class="th-center" style="width:38px;">Sat</th>
                <th class="th-center" style="width:38px;">Qty</th>
                <th class="th-right" style="width:82px;">Unit Price</th>
                <th class="th-right" style="width:82px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tableRows as $i => $row)
            @if($row['type'] === 'product')
            <tr style="background:#eef2f7;font-weight:bold;">
                <td class="col-no mono tc">{{ $row['idx'] }}</td>
                <td>{{ $row['data']->nama_item }}</td>
                <td class="tc">{{ $row['data']->satuan }}</td>
                <td class="tr mono">{{ number_format($row['data']->jumlah_item, 0, ',', '.') }}</td>
                <td class="tr mono">&nbsp;{{ floatval($row['data']->harga_item) > 0 ? 'Rp '.number_format($row['data']->harga_item, 0, ',', '.') : '-' }}</td>
                <td class="tr mono">&nbsp;{{ floatval($row['subtotal']) > 0 ? 'Rp '.number_format($row['subtotal'], 0, ',', '.') : '-' }}</td>
            </tr>
            @else
            <tr class="{{ $i % 2 === 0 ? 'row-odd' : 'row-even' }}">
                <td class="col-no mono tc muted" style="font-size:7px;">{{ $row['idx'] }}</td>
                <td style="padding-left:16px;">{{ $row['data']->nama_material }}</td>
                <td class="tc">{{ $row['data']->satuan_material }}</td>
                <td class="tr mono">{{ number_format($row['data']->jumlah_material, 0, ',', '.') }}</td>
                <td class="tr mono">Rp&nbsp;{{ number_format($row['data']->harga_material, 0, ',', '.') }}</td>
                <td class="tr mono">Rp&nbsp;{{ number_format($row['subtotal'], 0, ',', '.') }}</td>
            </tr>
            @endif
            @endforeach
            
            <tr class="total-mat-row">
                <td colspan="5" class="tr" style="font-size:8px;letter-spacing:.5px;color:#2c4f8a;">
                    TOTAL PRODUKSI
                </td>
                <td class="tr mono">Rp&nbsp;{{ number_format($totalMat, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ══════════════════════════════
         LABOR TABLE
    ══════════════════════════════ --}}
    @php
        $labors   = $quotation->labors;
        $totalLab = 0;
        $padLab   = max(0, 8 - $labors->count());
    @endphp
    @if($quotation->labors && $quotation->labors->count())
        <div class="section-bar" style="background:#2c6bc4;">Labor</div>
        <table class="lab-table">
            <thead>
                <tr>
                    <th class="th-center" style="width:22px;">#</th>
                    <th class="th-left">Work Item</th>
                    <th class="th-center" style="width:28px;">MP</th>
                    <th class="th-center" style="width:36px;">Days</th>
                    <th class="th-right" style="width:82px;">Rate / Day</th>
                    <th class="th-right" style="width:82px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($labors as $i => $labor)
                @php $laborSubtotal = ($labor->jumlah_sdm ?? 0) * ($labor->jumlah_hari ?? 0) * ($labor->rate_hari ?? 0); $totalLab += $laborSubtotal; @endphp
                <tr class="{{ $i % 2 === 0 ? 'row-odd' : 'row-even' }}">
                    <td class="tc mono">{{ $i + 1 }}</td>
                    <td>{{ $labor->nama_labor }}</td>
                    <td class="tc mono">{{ $labor->jumlah_sdm }}</td>
                    <td class="tc mono">{{ number_format($labor->jumlah_hari, 0) }}</td>
                    <td class="tr mono">Rp&nbsp;{{ number_format($labor->rate_hari, 0, ',', '.') }}</td>
                    <td class="tr mono">Rp&nbsp;{{ number_format($laborSubtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach

                <tr class="total-lab-row">
                    <td colspan="5" class="tr" style="font-size:8px;letter-spacing:.5px;color:#1B5DBC;">
                        TOTAL LABOR
                    </td>
                    <td class="tr mono">Rp&nbsp;{{ number_format($totalLab, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- ══════════════════════════════
         BIAYA LAIN-LAIN TABLE
    ══════════════════════════════ --}}
    @php
        $otherCosts = $quotation->otherCosts ?? collect();
        $totalOth   = $otherCosts->sum('jumlah_biaya');
    @endphp
    @if($otherCosts->count())
    <div class="section-bar" style="background:#4a7bd4;">Biaya Lain-Lain</div>
    <table class="lab-table">
        <thead>
            <tr>
                <th class="th-center" style="width:22px;">#</th>
                <th class="th-left">Nama Biaya</th>
                <th class="th-right" style="width:82px;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($otherCosts as $i => $cost)
            <tr class="{{ $i % 2 === 0 ? 'row-odd' : 'row-even' }}">
                <td class="tc mono">{{ $i + 1 }}</td>
                <td>{{ $cost->nama_biaya }}</td>
                <td class="tr mono">Rp&nbsp;{{ number_format($cost->jumlah_biaya, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-lab-row">
                <td colspan="2" class="tr" style="font-size:8px;letter-spacing:.5px;color:#1B5DBC;">
                    TOTAL BIAYA LAIN-LAIN
                </td>
                <td class="tr mono">Rp&nbsp;{{ number_format($totalOth, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- ══════════════════════════════
         GRAND TOTAL
    ══════════════════════════════ --}}
    @php
        $discount   = $quotation->diskon ?? 0;
        $grandTotal = $totalMat + $totalLab + $totalOth - $discount;
    @endphp
    <table class="grand-wrap">
        <tr>
            <td style="width:58%;"></td>
            <td style="width:42%;">
                <table class="grand-inner">
                    <tr>
                        <td class="grand-lbl">Total Produksi</td>
                        <td class="grand-val mono">Rp&nbsp;{{ number_format($totalMat, 0, ',', '.') }}</td>
                    </tr>
                    @if($quotation->labors && $quotation->labors->count())
                    <tr>
                        <td class="grand-lbl">Total Labor</td>
                        <td class="grand-val mono">Rp&nbsp;{{ number_format($totalLab, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($quotation->otherCosts && $quotation->otherCosts->count())
                    <tr>
                        <td class="grand-lbl">Total Biaya Lain-Lain</td>
                        <td class="grand-val mono">Rp&nbsp;{{ number_format($totalOth, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($discount > 0)
                    <tr>
                        <td class="grand-lbl">Diskon</td>
                        <td class="grand-val mono">Rp&nbsp;{{ number_format($discount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr class="grand-total-row">
                        <td>GRAND TOTAL</td>
                        <td class="grand-val">Rp&nbsp;{{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ══════════════════════════════
         OPSI A — TERMS (full width)
    ══════════════════════════════ --}}

    <div class="terms-bar">Terms & Conditions</div>
    <div class="terms-body">
        @if($quotation->keterangan)
            {!! nl2br(e($quotation->keterangan)) !!}
        @else
            1. Penawaran ini hanya berlaku sampai tanggal yang tertera di atas.<br>
            2. Untuk menerima penawaran, tanda tangan dan kembalikan dokumen ini.<br>
            3. Termin pembayaran:
            &nbsp;&nbsp;– 30%&nbsp; Setelah PO + TT 14 hari kalender
            &nbsp;&nbsp;– 40%&nbsp; Setelah pengiriman
            &nbsp;&nbsp;– 30%&nbsp; Setelah 8 AST<br>
            4. Harga belum termasuk PPN 12%.<br>
            5. Garansi: 12 bulan.
        @endif
    </div>

    {{-- Contact + Thank You --}}
    <table style="width:100%;border-collapse:collapse;margin-bottom:10px;">
        <tr>
            <td style="vertical-align:top;width:50%;">
                <div class="thankyou">THANK YOU</div>
                <div class="contact-box">
                    Untuk pertanyaan mengenai penawaran ini, hubungi:<br>
                    <strong>Agung Indikirono</strong><br>
                    +62 813-9816-4077<br>
                    marketing@stintegrator.com
                </div>
            </td>
            <td style="width:50%;"></td>
        </tr>
    </table>

    {{-- ══════════════════════════════
         OPSI A — SIGNATURE BLOCK (full width, 3 kolom)
    ══════════════════════════════ --}}
    <div class="sig-section">
        <div class="sig-section-lbl">Agreement & Signatures</div>
        <table class="sig-outer">
            <tr>
                <td>
                    <div class="sig-box"></div>
                    <div class="sig-name-line">PT. Sistem Teknologi Integrator</div>
                    <div class="sig-role">Prepared by</div>
                </td>
                <td>
                    <div class="sig-box"></div>
                    <div class="sig-name-line">&nbsp;</div>
                    <div class="sig-role">Checked by</div>
                </td>
                <td>
                    <div class="sig-box"></div>
                    <div class="sig-name-line">{{ $quotation->client?->nama_perusahaan ?? '-' }}</div>
                    <div class="sig-role">Approved by Customer</div>
                </td>
            </tr>
        </table>
        {{-- Baris tanggal terpisah di bawah tanda tangan --}}
        <table class="sig-date-outer">
            <tr>
                <td>
                    <div class="sig-date-lbl">Date</div>
                    <div class="sig-date-line">____ / ____ / ________</div>
                </td>
                <td>
                    <div class="sig-date-lbl">Date</div>
                    <div class="sig-date-line">____ / ____ / ________</div>
                </td>
                <td>
                    <div class="sig-date-lbl">Date</div>
                    <div class="sig-date-line">____ / ____ / ________</div>
                </td>
            </tr>
        </table>
    </div>

</div>{{-- end .page --}}
<div class="footer-strip"></div>
<div class="footer-text">
    Generated: {{ now()->format('d/m/Y H:i') }} WIB &nbsp;&middot;&nbsp; {{ $quotation->nomor_quotation }}
</div>
</body>
</html>