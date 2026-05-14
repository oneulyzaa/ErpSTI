<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Project Quote {{ $quotation->quote_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #1a1a1a;
            background: #fff;
        }

        .page { padding: 20px 26px 16px; }

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

        /* Meta grid aligned right */
        .meta-table {
            border-collapse: collapse;
            margin-left: auto; /* push to right inside td */
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
        .grand-sep td { border-top: 1px solid #c0c8d8; }
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
           BOTTOM: TERMS + SIGNATURES
        ══════════════════════════════ */
        .bottom-wrap {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .bottom-wrap td { vertical-align: top; }

        .terms-box { font-size: 7.5px; color: #333; line-height: 1.75; }
        .terms-title {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #1B5DBC;
            border-bottom: 1px solid #1B5DBC;
            padding-bottom: 2px;
            margin-bottom: 4px;
        }
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
            margin-top: 5px;
            line-height: 1.7;
        }

        /* Signature area */
        .sig-outer {
            width: 100%;
            border-collapse: collapse;
        }
        .sig-outer td {
            text-align: center;
            vertical-align: bottom;
            padding: 0 6px;
            width: 33.33%;
        }
        .sig-box { height: 56px; } /* space for wet signature */
        .sig-name-line {
            border-top: 1.5px solid #333;
            padding-top: 4px;
            font-size: 8px;
            font-weight: bold;
            color: #111;
            min-width: 80px;
            white-space: nowrap;
            overflow: hidden;
        }
        .sig-role {
            font-size: 7px;
            color: #888;
            margin-top: 2px;
            font-style: italic;
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
    </style>
</head>
<body>
<div class="page">

    {{-- ══════════════════════════════
         HEADER
    ══════════════════════════════ --}}
    <table class="header-wrap">
        <tr>
            {{-- Logo + Company Info --}}
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

            {{-- Title + Meta --}}
            <td class="title-cell">
                <div class="doc-title">Project&nbsp;Quote</div>
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Date</td>
                        <td class="meta-value">{{ $quotation->date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Quote #</td>
                        <td class="meta-value">{{ $quotation->quote_number }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Customer ID</td>
                        <td class="meta-value">{{ $quotation->customer_id ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Valid Until</td>
                        <td class="meta-value">{{ $quotation->valid_until->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <hr class="divider">

    {{-- ══════════════════════════════
         CLIENT BLOCK — dedicated rows (no coupling with item rows)
    ══════════════════════════════ --}}
    @php
        $clCompany = $quotation->client?->nama_perusahaan ?? $quotation->client_company;
        $clName    = $quotation->client?->nama_kontak_perusahaan ?? $quotation->client_name;
        $clEmail   = $quotation->client?->email_perusahaan ?? $quotation->client_email;
        $clAddr    = $quotation->client_address ?? ($quotation->client?->alamat_pengiriman_perusahaah ?? '');
    @endphp
    <table class="client-block">
        <tr>
            <td class="client-divider" style="width:30%;">
                <div class="client-lbl">Attention</div>
                <div class="client-val">{{ $quotation->client_attention ?? $clName }}</div>
            </td>
            <td class="client-divider" style="width:30%;">
                <div class="client-lbl">Company</div>
                <div class="client-val">{{ $clCompany }}</div>
            </td>
            <td style="width:40%;">
                <div class="client-lbl">Cc</div>
                <div class="client-sub">{{ $quotation->client_cc ?? '-' }}</div>
            </td>
        </tr>
        <tr class="client-sep">
            <td class="client-divider">
                <div class="client-lbl">Contact Name</div>
                <div class="client-sub">{{ $clName }}</div>
            </td>
            <td class="client-divider">
                <div class="client-lbl">Email</div>
                <div class="client-sub">{{ $clEmail ?? '-' }}</div>
            </td>
            <td>
                <div class="client-lbl">Description of Work</div>
                <div class="client-sub">{{ $quotation->description_of_work ?? '-' }}</div>
            </td>
        </tr>
        @if($quotation->project_name)
        <tr class="client-sep">
            <td colspan="3">
                <div class="client-lbl">Project Name</div>
                <div class="client-val">{{ $quotation->project_name }}</div>
            </td>
        </tr>
        @endif
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
         MATERIAL TABLE
    ══════════════════════════════ --}}
    @php
        $items    = $quotation->items;
        $itemList = $items->values();
        $totalMat = $items->sum('subtotal');
        $padCount = max(0, 15 - $items->count());
    @endphp

    <div class="section-bar">Produksi</div>
    <table class="mat-table">
        <thead>
            <tr>
                <th class="col-no th-center">#</th>
                <th class="th-left">Produk / Jasa</th>
                <th class="th-center" style="width:38px;">Qty</th>
                <th class="th-right" style="width:82px;">Unit Price</th>
                <th class="th-right" style="width:82px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($itemList as $i => $item)
            <tr class="{{ $i % 2 === 0 ? 'row-odd' : 'row-even' }}">
                <td class="col-no mono tc">{{ $i + 1 }}</td>
                <td>{{ $item->material_name }}</td>
                <td class="tr mono">{{ number_format($item->qty, 0, ',', '.') }}</td>
                <td class="tr mono">Rp&nbsp;{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="tr mono">Rp&nbsp;{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            {{-- Padding rows --}}
            @for($p = 0; $p < $padCount; $p++)
            @php $idx = $items->count() + $p; @endphp
            <tr class="{{ $idx % 2 === 0 ? 'row-odd' : 'row-even' }}">
                <td class="col-no"></td>
                <td></td>
                <td class="tr mono muted">-</td>
                <td class="tr mono muted">Rp&nbsp;-</td>
                <td class="tr mono muted">Rp&nbsp;-</td>
            </tr>
            @endfor

            {{-- Total Material --}}
            <tr class="total-mat-row">
                <td colspan="4" class="tr" style="font-size:8px;letter-spacing:.5px;color:#2c4f8a;">
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
        $totalLab = $labors->sum('subtotal');
        $padLab   = max(0, 8 - $labors->count());
    @endphp

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
            <tr class="{{ $i % 2 === 0 ? 'row-odd' : 'row-even' }}">
                <td class="tc mono">{{ $i + 1 }}</td>
                <td>{{ $labor->labor_name }}</td>
                <td class="tc mono">{{ $labor->mp }}</td>
                <td class="tc mono">{{ number_format($labor->days, 0) }}</td>
                <td class="tr mono">Rp&nbsp;{{ number_format($labor->rate, 0, ',', '.') }}</td>
                <td class="tr mono">Rp&nbsp;{{ number_format($labor->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            @for($p = 0; $p < $padLab; $p++)
            @php $idx = $labors->count() + $p; @endphp
            <tr class="{{ $idx % 2 === 0 ? 'row-odd' : 'row-even' }}">
                <td></td><td></td><td></td><td></td>
                <td class="tr mono muted">Rp&nbsp;-</td>
                <td class="tr mono muted">Rp&nbsp;-</td>
            </tr>
            @endfor

            <tr class="total-lab-row">
                <td colspan="5" class="tr" style="font-size:8px;letter-spacing:.5px;color:#1B5DBC;">
                    TOTAL LABOR
                </td>
                <td class="tr mono">Rp&nbsp;{{ number_format($totalLab, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ══════════════════════════════
         GRAND TOTAL
         — use a two-column table: left spacer / right summary box
    ══════════════════════════════ --}}
    @php
        $subTotal  = $totalMat + $totalLab;
        $taxAmount = $subTotal * ($quotation->tax_percentage / 100);
        $grandTotal = $subTotal + $taxAmount;
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
                    <tr>
                        <td class="grand-lbl">Total Labor</td>
                        <td class="grand-val mono">Rp&nbsp;{{ number_format($totalLab, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="grand-sep">
                        <td class="grand-lbl" style="border-top:1px solid #c0c8d8;">Subtotal</td>
                        <td class="grand-val mono" style="border-top:1px solid #c0c8d8;">
                            Rp&nbsp;{{ number_format($subTotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="grand-lbl">PPN {{ number_format($quotation->tax_percentage, 0) }}%</td>
                        <td class="grand-val mono">Rp&nbsp;{{ number_format($taxAmount, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="grand-total-row">
                        <td>GRAND TOTAL</td>
                        <td class="grand-val">Rp&nbsp;{{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ══════════════════════════════
         BOTTOM: TERMS (left) + SIGNATURES (right)
         One outer table, two cells, no float
    ══════════════════════════════ --}}
    <table style="width:100%;border-collapse:collapse;margin-top:10px;">
        <tr>
            {{-- ── LEFT: Terms & Conditions + Thank You + Contact ── --}}
            <td style="width:42%;vertical-align:top;padding-right:12px;
                        border-right:1px solid #dde3ec;">
                <div class="terms-box">
                    <div class="terms-title">Terms &amp; Conditions</div>
                    @if($quotation->notes)
                        {!! nl2br(e($quotation->notes)) !!}
                    @else
                        1. Penawaran ini hanya berlaku sampai tanggal yang tertera di atas.<br>
                        2. Untuk menerima penawaran, tanda tangan dan kembalikan dokumen ini.<br>
                        3. Termin pembayaran:<br>
                        &nbsp;&nbsp;– 30%&nbsp; Setelah PO + TT 14 hari kalender<br>
                        &nbsp;&nbsp;– 40%&nbsp; Setelah pengiriman<br>
                        &nbsp;&nbsp;– 30%&nbsp; Setelah 8 AST<br>
                        4. Harga belum termasuk PPN 12%.<br>
                        5. Garansi: 12 bulan.
                    @endif
                </div>

                <div class="thankyou">THANK YOU</div>
                <div class="contact-box">
                    Untuk pertanyaan mengenai penawaran ini, hubungi:<br>
                    <strong>Agung Indikirono</strong><br>
                    +62 813-9816-4077<br>
                    marketing@stintegrator.com
                </div>
            </td>

            {{-- ── RIGHT: Three signature blocks, evenly spaced ── --}}
            <td style="width:58%;vertical-align:bottom;padding-left:14px;">
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
                            <div class="sig-name-line">{{ $quotation->client_company }}</div>
                            <div class="sig-role">Approved by Customer</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</div>{{-- end .page --}}

<div class="footer-strip"></div>
<div class="footer-text">
    Generated: {{ now()->format('d/m/Y H:i') }} WIB &nbsp;&middot;&nbsp; {{ $quotation->quote_number }}
</div>
</body>
</html>