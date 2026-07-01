<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Delivery Order {{ $deliveryOrder->do_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8.5px;
            color: #1a1a1a;
            background: #fff;
        }

        .header-wrap {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .header-wrap td { vertical-align: top; padding: 0; }

        .logo-cell { width: 200px; }
        .logo-cell img { width: 150px; display: block; }

        .company-info {
            font-size: 7px;
            color: #555;
            line-height: 1.3;
            margin-top: 3px;
        }
        .company-info a { color: #1B5DBC; text-decoration: none; }

        .title-cell { text-align: right; }

        .doc-title {
            font-size: 17px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #1B5DBC;
            text-transform: uppercase;
            line-height: 1;
            margin-bottom: 4px;
        }

        .meta-table {
            border-collapse: collapse;
            margin-left: auto;
        }
        .meta-table td {
            border: 1px solid #c0c8d8;
            padding: 1.5px 6px;
            font-size: 7.5px;
            white-space: nowrap;
        }
        .meta-label {
            background: #e4eaf5;
            font-weight: bold;
            color: #333;
            text-align: right;
            width: 70px;
        }
        .meta-value {
            background: #fff;
            min-width: 110px;
            /* font-family: 'DejaVu Sans Mono', monospace; */
        }

        .divider {
            border: none;
            border-top: 2px solid #1B5DBC;
            margin: 4px 0 4px;
        }

        .client-block {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #c8d0dc;
            margin-bottom: 4px;
        }
        .client-block td {
            padding: 3px 7px;
            font-size: 8px;
            vertical-align: top;
        }

        .client-lbl {
            font-size: 6.5px;
            font-weight: bold;
            color: #888;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 1px;
        }
        .client-val {
            font-size: 8.5px;
            font-weight: bold;
            color: #111;
        }
        .client-sub {
            font-size: 7.5px;
            color: #444;
            line-height: 1.3;
        }
        .client-divider { border-right: 1px solid #dde3ec; }
        .client-sep { border-top: 1px dotted #dde3ec; }

        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .item-table th {
            background: #ffffff;
            color: #000;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 2.5px 5px;
            border: 1px solid #d5d5d5;
        }
        .item-table td {
            border: 1px solid #ccd3df;
            padding: 2px 5px;
            font-size: 7.5px;
            vertical-align: middle;
        }
        .item-table .th-left { text-align: left; }
        .item-table .th-right { text-align: right; }
        .item-table .th-center { text-align: center; }

        .col-no   { width: 22px; text-align: center; }
        .col-name { /* auto */ }
        .col-qty  { width: 48px; text-align: right; }
        .col-unit { width: 38px; text-align: center; }

        .row-odd  { background: #fff; }
        .row-even { background: #ffffff; }

        .total-row td {
            background: #d6e4f5;
            font-weight: bold;
            border-top: 2px solid #eaeaea;
            font-size: 8px;
        }

        .footer-note {
            margin-top: 6px;
            font-size: 7px;
            color: #555;
            line-height: 1.3;
            border-top: 1px solid #dde3ec;
            padding-top: 5px;
        }

        .signature-wrap {
            width: 100%;
            margin-top: 10px;
        }
        .signature-wrap td {
            width: 50%;
            text-align: center;
            padding: 0 20px;
            font-size: 8px;
        }
        .signature-line {
            margin-top: 55px;
            border-top: 1px solid #333;
            width: 150px;
            display: inline-block;
        }
        @page {
            margin: 12px 16px 12px 16px !important;
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
                    <div class="company-info">
                        Ruko Palazo Blok AB 46, Ciantra<br>
                        Cikarang Selatan, Bekasi 17530<br>
                        Telp: +6221-22108157<br>
                        <a href="mailto:marketing@stintegrator.com">marketing@stintegrator.com</a>
                    </div>
                </td>
                <td class="title-cell">
                    <div class="doc-title">DELIVERY ORDER</div>
                    <table class="meta-table">
                        <tr><td class="meta-label">No. DO</td><td class="meta-value">{{ $deliveryOrder->do_number }}</td></tr>
                        <tr><td class="meta-label">Tanggal</td><td class="meta-value">{{ $deliveryOrder->date->format('d M Y') }}</td></tr>
                        @if($deliveryOrder->delivery_date)
                        <tr><td class="meta-label">Pengiriman</td><td class="meta-value">{{ $deliveryOrder->delivery_date->format('d M Y') }}</td></tr>
                        @endif
                        @if($deliveryOrder->so_number)
                        <tr><td class="meta-label">Ref. SO</td><td class="meta-value">{{ $deliveryOrder->so_number }}</td></tr>
                        @endif
                        @if($deliveryOrder->nomor_po)
                        <tr>
                            <td class="meta-label">Nomor PO</td>
                            <td class="meta-value">{{ $deliveryOrder->nomor_po }}</td>
                        </tr>
                        @endif
                        <tr><td class="meta-label">Status</td><td class="meta-value">{{ ucfirst(str_replace('_',' ',$deliveryOrder->status)) }}</td></tr>
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
                    <div class="client-val">{{ $deliveryOrder->client_company }}</div>
                    <div class="client-sub" style="margin-top:1px;">
                        Kontak: {{ $deliveryOrder->client_name }}<br>
                        @if($deliveryOrder->client_attention)Attn: {{ $deliveryOrder->client_attention }}<br>@endif
                        @if($deliveryOrder->client_cc)CC: {{ $deliveryOrder->client_cc }}<br>@endif
                        @if($deliveryOrder->client_email){{ $deliveryOrder->client_email }}@endif
                    </div>
                </td>
                <td style="width:50%;">
                    <div class="client-lbl">Pengirim</div>
                    <div class="client-val">PT. Sistem Teknologi Integrator</div>
                    <div class="client-sub" style="margin-top:1px;">
                        Ruko Palazo Blok AB 46, Ciantra<br>
                        Cikarang Selatan, Bekasi 17530
                    </div>
                </td>
            </tr>
            @if($deliveryOrder->destination_address)
            <tr>
                <td colspan="2" class="client-sep">
                    <div class="client-lbl">Alamat Tujuan</div>
                    <div class="client-sub">{{ $deliveryOrder->destination_address }}</div>
                </td>
            </tr>
            @endif
             @if($deliveryOrder->description)
             <tr>
                 <td colspan="2" class="client-sep">
                     <div class="client-lbl">Deskripsi</div>
                     <div class="client-sub">{{ $deliveryOrder->description }}</div>
                 </td>
             </tr>
             @endif
             @if($deliveryOrder->project_name)
             <tr>
                 <td colspan="2" class="client-sep">
                     <div class="client-lbl">Nama Project</div>
                     <div class="client-val">{{ $deliveryOrder->project_name }}</div>
                 </td>
             </tr>
             @endif
         </table>

        {{-- ═══ ITEMS TABLE ═══ --}}
        <div style="font-size:7.5px;font-weight:bold;text-transform:uppercase;letter-spacing:1px;padding:2px 6px;background:#eaeaea;color:#000;margin-bottom:0;">Item Pengiriman</div>
        <table class="item-table">
            <thead>
                <tr>
                    <th class="col-no">#</th>
                    <th class="th-left col-name">Description</th>
                    <th class="th-right col-qty">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deliveryOrder->items as $i => $item)
                <tr class="{{ $i % 2 === 0 ? 'row-odd' : 'row-even' }}">
                    <td style="text-align:center;">{{ $i + 1 }}</td>
                    <td>
                        <div style="font-weight:bold;">{{ $item->item_name }}</div>
                        @if($item->description)
                            <div style="font-size:6.5px;color:#666;">{{ $item->description }}</div>
                        @endif
                    </td>
                    <td style="text-align:right;">{{ number_format($item->qty, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" style="text-align:right;">Total Item: {{ $deliveryOrder->items->count() }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- ═══ FOOTER / NOTES ═══ --}}
        @if($deliveryOrder->notes)
        <div class="footer-note">
            <strong>Catatan:</strong><br>
            {!! nl2br(e($deliveryOrder->notes)) !!}
        </div>
        @endif

        {{-- ═══ SIGNATURE ═══ --}}
        <table class="signature-wrap">
            <tr>
                <td>
                    <div>Pengirim,</div>
                    <div class="signature-line"></div>
                    <div style="margin-top:3px;font-weight:bold;">Agung Indrianto</div>
                </td>
                <td>
                    <div>Diterima Oleh,</div>
                    <div class="signature-line"></div>
                    <div style="margin-top:3px;font-weight:bold;">&nbsp;</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>