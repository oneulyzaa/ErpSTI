<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sales Order {{ $salesOrder->nomor_salesorder }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #1a1a1a;
            background: #fff;
        }

        /* .page { padding: 20px 26px 16px; } */

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
            /* font-family: 'DejaVu Sans Mono', monospace; */
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
        @page {
            margin: 20px 26px 20px 26px !important;
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
                        <tr><td class="meta-label">No. SO</td><td class="meta-value">{{ $salesOrder->nomor_salesorder }}</td></tr>
                        <tr><td class="meta-label">Tanggal</td><td class="meta-value">{{ $salesOrder->tanggal_pembuatan->format('d M Y') }}</td></tr>
                        @if($salesOrder->nomor_po)
                        <tr><td class="meta-label">Nomor PO</td><td class="meta-value">{{ $salesOrder->nomor_po }}</td></tr>
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
                    <div class="client-val">{{ $salesOrder->client->nama_perusahaan ?? '-' }}</div>
                    <div class="client-sub" style="margin-top:2px;">
                        Kontak: {{ $salesOrder->client->nama_kontak ?? '-' }}<br>
                        @if($salesOrder->client->email_perusahaan){{ $salesOrder->client->email_perusahaan }}@endif
                        @if($salesOrder->client->alamat_perusahaan)<br>{!! nl2br(e($salesOrder->client->alamat_perusahaan)) !!}@endif
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
            @if($salesOrder->nama_project)
            <tr>
                <td colspan="2" class="client-sep">
                    <div class="client-lbl">Nama Project</div>
                    <div class="client-val">{{ $salesOrder->nama_project }}</div>
                </td>
            </tr>
            @endif
@if($salesOrder->nomor_quotation)
<tr>
    <td colspan="2" class="client-sep">
        <div class="client-lbl">Referensi Quotation</div>
        <div class="client-sub" style="font-family:monospace;">{{ $salesOrder->nomor_quotation }}</div>
    </td>
</tr>
@endif
            @if($salesOrder->keterangan)
            <tr>
                <td colspan="2" class="client-sep">
                    <div class="client-lbl">Keterangan</div>
                    <div class="client-sub">{{ $salesOrder->keterangan }}</div>
                </td>
            </tr>
            @endif
        </table>

        {{-- ═══ MATERIAL TABLE ═══ --}}
        @if($salesOrder->items->isNotEmpty())
        <div class="section-bar">Produksi</div>
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
                        <div style="font-weight:bold;">{{ $item->nama_item }}</div>
                        @if($item->description)
                            <div style="font-size:7px;color:#666;">{{ $item->deksripsi_item }}</div>
                        @endif
                    </td>
                    <td style="text-align:right;">{{ number_format($item->jumlah_item) }}</td>
                    <td style="text-align:center;">{{ $item->satuan }}</td>
                    <td style="text-align:center;">
                        {{-- cek jika item ini punya tidak punya material, maka harga satuan dari item tersebut --}}
                        @if($item->materials && $item->materials->count())
                            - 
                        @else
                            {{ floatval($item->harga_item) > 0 ? 'Rp '.number_format($item->harga_item, 0, ',', '.') : '-' }}
                        @endif
                    </td>
                    <td style="text-align:right;">
                        @php
                            if($item->materials->count() > 1){
                                $subtotal = 0;
                                foreach($item->materials as $material){
                                    $subtotal += $material->jumlah_material * $material->harga_material;
                                    // echo $material->harga_material;
                                }
                            }
                            else{
                                $subtotal = $item->harga_item * $item->jumlah_item;
                            }
                        @endphp 
                        {{ 'Rp '.number_format($subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-mat-row">
                    <td colspan="5" style="text-align:right;">Total Produksi + Material</td>
                    <td style="text-align:right;">Rp {{ number_format($salesOrder->subtotal_produksi + $salesOrder->subtotal_material, 0, ',', '.') }}</td>
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
                    <td><strong>{{ $labor->nama_labor }}</strong></td>
                    <td style="text-align:center;">{{ $labor->jumlah_sdm }}</td>
                    <td style="text-align:center;">{{ number_format($labor->jumlah_hari, 0) }}</td>
                    <td style="text-align:right;">Rp {{ number_format($labor->rate_hari, 0) }}</td>
                    <td style="text-align:right;">Rp {{ number_format($labor->rate_hari * $labor->jumlah_hari, 0, ',', '.') }}</td>
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

        {{-- ═══ OTHER COSTS TABLE ═══ --}}
        @php
            $otherCosts = $salesOrder->otherCosts ?? collect();
            $totalOth   = $otherCosts->sum('subtotal');
        @endphp
        @if($otherCosts->isNotEmpty())
        <div class="section-bar">Biaya Lain-Lain</div>
        <table class="lab-table">
            <thead>
                <tr>
                    <th class="col-lno">#</th>
                    <th class="th-left col-lab">Nama Biaya</th>
                    {{-- <th class="th-center col-mp">Qty</th>
                    <th class="th-right col-rate">Rate</th> --}}
                    <th class="th-right col-lsub">Biaya</th>
                </tr>
            </thead>
            <tbody>
                @foreach($otherCosts as $i => $cost)
                <tr class="{{ $i % 2 === 0 ? 'row-odd' : 'row-even' }}">
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $cost->nama_biaya }}</strong></td>
                    <td style="text-align:right;">Rp {{ number_format($cost->jumlah_biaya, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-lab-row">
                    <td colspan="2" style="text-align:right;">Total Biaya Lain-Lain</td>
                    <td style="text-align:right;">Rp {{ number_format($salesOrder->subtotal_lainlain, 0) }}</td>
                </tr>
            </tfoot>
        </table>
        @endif

        @php
            $subtotal = $salesOrder->subtotal_produksi + $salesOrder->subtotal_material + $salesOrder->subtotal_labor + $salesOrder->subtotal_lainlain - $salesOrder->diskon;
            $pajakAmount = $subtotal * ($salesOrder->pajak / 100);
        @endphp
        {{-- ═══ SUMMARY ═══ --}}
        <table class="summary-wrap">
            <tr>
                <td class="summary-label">Total Produksi</td>
                <td class="summary-value">Rp {{ number_format($salesOrder->subtotal_produksi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">Total Material</td>
                <td class="summary-value">Rp {{ number_format($salesOrder->subtotal_material, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">Total Labor</td>
                <td class="summary-value">Rp {{ number_format($salesOrder->subtotal_labor, 0, ',', '.') }}</td>
            </tr>
            @if($salesOrder->subtotal_lainlain > 0)
            <tr>
                <td class="summary-label">Total Biaya Lain-Lain</td>
                <td class="summary-value">Rp {{ number_format($salesOrder->subtotal_lainlain, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($salesOrder->diskon > 0)
            <tr>
                <td class="summary-label">Diskon</td>
                <td class="summary-value">Rp {{ number_format($salesOrder->diskon, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td class="summary-label">Subtotal</td>
                <td class="summary-value">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">PPN ({{ number_format($salesOrder->pajak, 0) }}%)</td>
                <td class="summary-value">Rp {{ number_format($pajakAmount, 0, ',', '.') }}</td>
            </tr>
            <tr class="summary-total">
                <td>GRAND TOTAL</td>
                <td class="summary-value">Rp {{ number_format($salesOrder->grandtotal, 0, ',', '.') }}</td>
            </tr>
        </table>

        {{-- ═══ FOOTER / NOTES ═══ --}}
        @if($salesOrder->keterangan)
        <div class="footer-note">
            <strong>Keterangan:</strong><br>
            {!! nl2br(e($salesOrder->keterangan)) !!}
        </div>
        @endif

        {{-- ═══ SIGNATURE ═══ --}}
        <table class="signature-wrap">
            <tr>
                <td>
                    <div>Dibuat Oleh,</div>
                    <div class="signature-line"></div>
                    <div style="margin-top:4px;font-weight:bold;">Staf Penjualan</div>
                </td>
                <td>
                    <div>Disetujui Oleh,</div>
                    <div class="signature-line"></div>
                    <div style="margin-top:4px;font-weight:bold;">Direktur</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>