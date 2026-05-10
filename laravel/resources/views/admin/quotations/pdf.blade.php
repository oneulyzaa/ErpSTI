<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Project Quote {{ $quotation->quote_number }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #1a1a1a;
            background: #fff;
        }

        /* ── PAGE ── */
        .page { padding: 18px 24px 14px; }

        /* ── HEADER ── */
        .header-table { width:100%; border-collapse:collapse; margin-bottom:6px; }
        .logo-cell { width: 200px; vertical-align:middle; }
        .logo-cell img { width:160px; }
        .company-info { font-size:7.5px; color:#444; line-height:1.6; margin-top:3px; }
        .company-info a { color:#1B5DBC; text-decoration:none; }
        .title-cell { text-align:right; vertical-align:top; }
        .title-text {
            font-size:22px; font-weight:bold; letter-spacing:2px;
            color:#1B5DBC; text-transform:uppercase;
        }

        /* ── META BOX (DATE/QUOTE/CUST/VALID) ── */
        .meta-box { float:right; margin-top:4px; }
        .meta-table { border-collapse:collapse; }
        .meta-table td {
            border:1px solid #bbb;
            padding:2px 6px;
            font-size:8px;
        }
        .meta-label {
            background:#e8ecf2;
            font-weight:bold;
            color:#333;
            text-align:right;
            width:70px;
        }
        .meta-value { background:#fff; min-width:110px; }

        /* ── DIVIDER ── */
        .divider { border:none; border-top:2px solid #1B5DBC; margin:6px 0; clear:both; }
        .divider-gray { border:none; border-top:1px solid #ccc; margin:4px 0; }

        /* ── CLIENT + MATERIAL TABLE ── */
        .main-table {
            width:100%; border-collapse:collapse;
            margin-bottom:4px;
        }
        .main-table th, .main-table td {
            border:1px solid #ccc;
            padding:3px 5px;
            font-size:8px;
            vertical-align:top;
        }
        .main-table thead th {
            background:#2c4f8a;
            color:#fff;
            font-weight:bold;
            text-align:center;
            font-size:7.5px;
            text-transform:uppercase;
        }
        .main-table thead th.th-left { text-align:left; }

        /* client column */
        .col-client { width:155px; }
        .client-label { font-size:7px; font-weight:bold; color:#555; text-transform:uppercase; letter-spacing:.5px; }
        .client-val { font-size:8.5px; font-weight:bold; color:#1a1a1a; }
        .client-sub { font-size:7.5px; color:#444; line-height:1.5; }

        /* material columns */
        .col-no     { width:22px; text-align:center; }
        .col-mat    { width:auto; }
        .col-qty    { width:36px; text-align:right; }
        .col-up     { width:70px; text-align:right; }
        .col-sub    { width:70px; text-align:right; }
        .col-tot    { width:70px; text-align:right; }

        .mono { font-family:'DejaVu Sans Mono', monospace; }
        .text-right { text-align:right; }
        .text-center { text-align:center; }
        .bold { font-weight:bold; }

        /* row shading */
        .row-even { background:#f7f9fc; }
        .row-odd  { background:#fff; }

        /* total row */
        .total-mat-row td {
            background:#dce8f5;
            font-weight:bold;
            border-top:2px solid #2c4f8a;
        }

        /* ── LABOR TABLE ── */
        .labor-table { width:100%; border-collapse:collapse; margin-bottom:4px; }
        .labor-table th, .labor-table td {
            border:1px solid #ccc;
            padding:3px 5px;
            font-size:8px;
            vertical-align:middle;
        }
        .labor-table thead th {
            background:#1B5DBC;
            color:#fff;
            font-weight:bold;
            text-align:center;
            font-size:7.5px;
            text-transform:uppercase;
        }
        .labor-table thead th.th-left { text-align:left; }
        .total-lab-row td {
            background:#dce8f5;
            font-weight:bold;
            border-top:2px solid #1B5DBC;
        }

        /* ── GRAND TOTAL ── */
        .grand-table { width:100%; border-collapse:collapse; margin-top:4px; }
        .grand-table td { padding:2px 5px; font-size:8.5px; }
        .grand-label { font-weight:bold; color:#333; }
        .grand-val { text-align:right; font-family:'DejaVu Sans Mono',monospace; font-weight:bold; }
        .grand-total-row td { background:#1B5DBC; color:#fff; font-size:10px; font-weight:bold; padding:4px 6px; }

        /* ── TERMS + SIGNATURE ── */
        .bottom-table { width:100%; border-collapse:collapse; margin-top:8px; }
        .bottom-table td { vertical-align:top; }
        .terms-box { font-size:7.5px; color:#333; line-height:1.7; }
        .terms-title { font-weight:bold; font-size:8px; text-transform:uppercase; letter-spacing:.5px; margin-bottom:3px; }
        .thankyou { font-family:'DejaVu Sans', serif; font-size:14px; font-weight:bold; color:#1B5DBC; margin-top:10px; }
        .contact-box { font-size:7.5px; color:#444; margin-top:6px; line-height:1.6; }
        .sig-table { width:100%; border-collapse:collapse; }
        .sig-table td { text-align:center; padding:0 8px; }
        .sig-line { border-top:1px solid #555; margin-top:40px; padding-top:3px; font-size:8px; font-weight:bold; }
        .sig-role { font-size:7px; color:#666; }

        /* ── FOOTER ── */
        .footer-strip { background:#1B5DBC; height:3px; margin-top:10px; }
        .footer-text { text-align:center; font-size:7px; color:#999; margin-top:4px; }
    </style>
</head>
<body>
<div class="page">

    {{-- ══ HEADER ══ --}}
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="STI Logo">
                @else
                    <div style="font-size:16px;font-weight:bold;color:#1B5DBC;">PT. SISTEM TEKNOLOGI<br>INTEGRATOR</div>
                @endif
                <div class="company-info">
                    Ruko Palazo Blok AB 46, Ciantra, Cikarang Selatan, Bekasi 17530<br>
                    Telp: +6221-22108157<br>
                    <a href="mailto:marketing@stintegrator.com">marketing@stintegrator.com</a>
                </div>
            </td>
            <td class="title-cell">
                <div class="title-text">PROJECT QUOTE</div>
                <div class="meta-box">
                    <table class="meta-table">
                        <tr>
                            <td class="meta-label">DATE</td>
                            <td class="meta-value mono">{{ $quotation->date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">QUOTE #</td>
                            <td class="meta-value mono">{{ $quotation->quote_number }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">CUSTOMER ID</td>
                            <td class="meta-value mono">{{ $quotation->customer_id ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">VALID UNTIL</td>
                            <td class="meta-value mono">{{ $quotation->valid_until->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <hr class="divider">

    {{-- ══ MAIN TABLE (CLIENT | MATERIAL) ══ --}}
    @php
        // Pad material rows to at least 20 for visual consistency like template
        $items      = $quotation->items;
        $padCount   = max(0, 20 - $items->count());
        $totalMat   = $items->sum('subtotal');
    @endphp

    <table class="main-table">
        <thead>
            <tr>
                {{-- Client header --}}
                <th class="col-client th-left">CLIENT</th>
                {{-- Material header --}}
                <th class="col-no">#</th>
                <th class="col-mat th-left">MATERIAL</th>
                <th class="col-qty">QTY</th>
                <th class="col-up">UNIT PRICE</th>
                <th class="col-sub">SUB TOTAL</th>
                <th class="col-tot">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            {{-- ROW 1: Attn & first item --}}
            @php $firstItem = $items->first(); $itemList = $items->values(); @endphp
            <tr class="row-odd">
                <td class="col-client">
                    <span class="client-label">Attn : </span>
                    <span class="client-val">{{ $quotation->client_attention ?? $quotation->client_name }}</span>
                </td>
                <td class="text-center mono">{{ $firstItem ? 1 : '' }}</td>
                <td>{{ $firstItem?->material_name }}</td>
                <td class="text-right mono">{{ $firstItem ? number_format($firstItem->qty, 0, ',', '.') : '' }}</td>
                <td class="text-right mono">{{ $firstItem ? 'Rp ' . number_format($firstItem->unit_price, 0, ',', '.') : 'Rp' }}</td>
                <td class="text-right mono">{{ $firstItem ? 'Rp ' . number_format($firstItem->subtotal, 0, ',', '.') : 'Rp' }}</td>
                <td class="text-right mono"></td>
            </tr>
            {{-- ROW 2: CC --}}
            @php $item2 = $itemList->get(1); @endphp
            <tr class="row-even">
                <td class="col-client">
                    <span class="client-label">Cc : </span>
                    <span class="client-sub">{{ $quotation->client_cc ?? '' }}</span>
                </td>
                <td class="text-center mono">{{ $item2 ? 2 : '' }}</td>
                <td>{{ $item2?->material_name }}</td>
                <td class="text-right mono">{{ $item2 ? number_format($item2->qty, 0, ',', '.') : '' }}</td>
                <td class="text-right mono">{{ $item2 ? 'Rp ' . number_format($item2->unit_price, 0, ',', '.') : 'Rp' }}</td>
                <td class="text-right mono">{{ $item2 ? 'Rp ' . number_format($item2->subtotal, 0, ',', '.') : 'Rp' }}</td>
                <td class="text-right mono"></td>
            </tr>
            {{-- ROW 3: Company --}}
            @php $item3 = $itemList->get(2); @endphp
            <tr class="row-odd">
                <td class="col-client">
                    <div class="client-val" style="font-size:10px;font-weight:bold;margin-top:2px;">
                        {{ $quotation->client_company }}
                    </div>
                </td>
                <td class="text-center mono">{{ $item3 ? 3 : '' }}</td>
                <td>{{ $item3?->material_name }}</td>
                <td class="text-right mono">{{ $item3 ? number_format($item3->qty, 0, ',', '.') : '' }}</td>
                <td class="text-right mono">{{ $item3 ? 'Rp ' . number_format($item3->unit_price, 0, ',', '.') : 'Rp' }}</td>
                <td class="text-right mono">{{ $item3 ? 'Rp ' . number_format($item3->subtotal, 0, ',', '.') : 'Rp' }}</td>
                <td class="text-right mono"></td>
            </tr>
            {{-- ROW 4: Customer name label --}}
            @php $item4 = $itemList->get(3); @endphp
            <tr class="row-even">
                <td class="col-client" style="padding-bottom:6px;">
                    <span class="client-sub">{{ $quotation->client_name }}</span>
                </td>
                <td class="text-center mono">{{ $item4 ? 4 : '' }}</td>
                <td>{{ $item4?->material_name }}</td>
                <td class="text-right mono">{{ $item4 ? number_format($item4->qty, 0, ',', '.') : '' }}</td>
                <td class="text-right mono">{{ $item4 ? 'Rp ' . number_format($item4->unit_price, 0, ',', '.') : 'Rp' }}</td>
                <td class="text-right mono">{{ $item4 ? 'Rp ' . number_format($item4->subtotal, 0, ',', '.') : 'Rp' }}</td>
                <td class="text-right mono"></td>
            </tr>
            {{-- ROW 5: Email --}}
            @php $item5 = $itemList->get(4); @endphp
            <tr class="row-odd">
                <td class="col-client">
                    @if($quotation->client_email)
                        <div class="client-label">Email Address</div>
                        <div class="client-sub">{{ $quotation->client_email }}</div>
                    @endif
                </td>
                <td class="text-center mono">{{ $item5 ? 5 : '' }}</td>
                <td>{{ $item5?->material_name }}</td>
                <td class="text-right mono">{{ $item5 ? number_format($item5->qty, 0, ',', '.') : '' }}</td>
                <td class="text-right mono">{{ $item5 ? 'Rp ' . number_format($item5->unit_price, 0, ',', '.') : 'Rp' }}</td>
                <td class="text-right mono">{{ $item5 ? 'Rp ' . number_format($item5->subtotal, 0, ',', '.') : 'Rp' }}</td>
                <td class="text-right mono"></td>
            </tr>
            {{-- ROW 6: Description of Work --}}
            @php $item6 = $itemList->get(5); @endphp
            <tr class="row-even">
                <td class="col-client">
                    @if($quotation->description_of_work)
                        <div class="client-label">Description of Work</div>
                        <div class="client-sub">{{ $quotation->description_of_work }}</div>
                    @endif
                </td>
                <td class="text-center mono">{{ $item6 ? 6 : '' }}</td>
                <td>{{ $item6?->material_name }}</td>
                <td class="text-right mono">{{ $item6 ? number_format($item6->qty, 0, ',', '.') : '' }}</td>
                <td class="text-right mono">{{ $item6 ? 'Rp ' . number_format($item6->unit_price, 0, ',', '.') : 'Rp' }}</td>
                <td class="text-right mono">{{ $item6 ? 'Rp ' . number_format($item6->subtotal, 0, ',', '.') : 'Rp' }}</td>
                <td class="text-right mono"></td>
            </tr>

            {{-- Remaining items (index 6+) with empty client column --}}
            @for($i = 6; $i < max($items->count(), 20); $i++)
                @php $it = $itemList->get($i); $rowClass = $i % 2 === 0 ? 'row-even' : 'row-odd'; @endphp
                <tr class="{{ $rowClass }}">
                    <td class="col-client"></td>
                    <td class="text-center mono">{{ $it ? $i + 1 : '' }}</td>
                    <td>{{ $it?->material_name }}</td>
                    <td class="text-right mono">{{ $it ? number_format($it->qty, 0, ',', '.') : '' }}</td>
                    <td class="text-right mono">{{ $it ? 'Rp ' . number_format($it->unit_price, 0, ',', '.') : 'Rp' }}</td>
                    <td class="text-right mono">{{ $it ? 'Rp ' . number_format($it->subtotal, 0, ',', '.') : 'Rp' }}</td>
                    <td class="text-right mono">{{ ($i === 6 && $it) ? 'Rp ' . number_format($totalMat, 0, ',', '.') : '' }}</td>
                </tr>
            @endfor

            {{-- Total Material row --}}
            <tr class="total-mat-row">
                <td class="col-client" style="text-align:right;font-size:7.5px;">TOTAL MATERIALS</td>
                <td colspan="5" style="text-align:right;font-size:8px;" class="mono">
                    Rp {{ number_format($totalMat, 0, ',', '.') }}
                </td>
                <td class="text-right mono">Rp {{ number_format($totalMat, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ══ LABOR TABLE ══ --}}
    @php
        $labors   = $quotation->labors;
        $totalLab = $labors->sum('subtotal');
    @endphp

    <table class="labor-table">
        <thead>
            <tr>
                <th style="width:155px;" class="th-left">LABOR</th>
                <th style="width:22px;">#</th>
                <th class="th-left" style="min-width:120px;">ITEM</th>
                <th style="width:30px;">MP</th>
                <th style="width:36px;">Days</th>
                <th style="width:80px;text-align:right;">RATE</th>
                <th style="width:80px;text-align:right;">SUB TOTAL</th>
                <th style="width:70px;text-align:right;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($labors as $i => $labor)
            @php $rowClass = $i % 2 === 0 ? 'row-odd' : 'row-even'; @endphp
            <tr class="{{ $rowClass }}">
                <td></td>
                <td class="text-center mono">{{ $i + 1 }}</td>
                <td>{{ $labor->labor_name }}</td>
                <td class="text-center mono">{{ $labor->mp }}</td>
                <td class="text-center mono">{{ number_format($labor->days, 0) }}</td>
                <td class="text-right mono">Rp {{ number_format($labor->rate, 0, ',', '.') }}</td>
                <td class="text-right mono">Rp {{ number_format($labor->subtotal, 0, ',', '.') }}</td>
                <td class="text-right mono">{{ $i === 0 ? '' : '' }}</td>
            </tr>
            @endforeach
            {{-- pad to at least 10 rows --}}
            @for($p = $labors->count(); $p < 10; $p++)
            <tr class="{{ $p % 2 === 0 ? 'row-odd' : 'row-even' }}">
                <td></td><td></td><td></td><td></td><td></td>
                <td class="text-right mono" style="color:#bbb;">Rp</td>
                <td class="text-right mono" style="color:#bbb;">Rp</td>
                <td></td>
            </tr>
            @endfor
            <tr class="total-lab-row">
                <td colspan="6" style="text-align:right;font-size:7.5px;">TOTAL LABOR</td>
                <td class="text-right mono">Rp {{ number_format($totalLab, 0, ',', '.') }}</td>
                <td class="text-right mono bold">Rp {{ number_format($totalLab, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ══ GRAND TOTAL ══ --}}
    @php
        $subTotal  = $totalMat + $totalLab;
        $taxAmount = $subTotal * ($quotation->tax_percentage / 100);
        $grandTotal= $subTotal + $taxAmount;
    @endphp
    <table class="grand-table">
        <tr>
            <td style="width:155px;"></td>
            <td>
                <table style="width:100%;border-collapse:collapse;float:right;max-width:360px;">
                    <tr>
                        <td class="grand-label" style="font-size:8px;padding:2px 5px;">Total Material</td>
                        <td class="grand-val"   style="font-size:8px;padding:2px 5px;">Rp {{ number_format($totalMat, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="grand-label" style="font-size:8px;padding:2px 5px;">Total Labor</td>
                        <td class="grand-val"   style="font-size:8px;padding:2px 5px;">Rp {{ number_format($totalLab, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="grand-label" style="font-size:8px;padding:2px 5px;border-top:1px solid #ccc;">Subtotal</td>
                        <td class="grand-val"   style="font-size:8px;padding:2px 5px;border-top:1px solid #ccc;">Rp {{ number_format($subTotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="grand-label" style="font-size:8px;padding:2px 5px;">PPN {{ number_format($quotation->tax_percentage, 0) }}%</td>
                        <td class="grand-val"   style="font-size:8px;padding:2px 5px;">Rp {{ number_format($taxAmount, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="grand-total-row">
                        <td>GRAND TOTAL</td>
                        <td class="grand-val" style="color:#fff;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ══ BOTTOM: TERMS + SIGNATURE ══ --}}
    <table class="bottom-table" style="margin-top:10px;">
        <tr>
            <td style="width:42%;padding-right:12px;">
                <div class="terms-box">
                    <div class="terms-title">Terms and Conditions</div>
                    @if($quotation->notes)
                        {!! nl2br(e($quotation->notes)) !!}
                    @else
                        1. This quotation is only valid through date above.<br>
                        2. To accept the quote, sign and return quoted sheet to the address above.<br>
                        3. Term of payment :<br>
                        &nbsp;&nbsp;&nbsp;- 30%&nbsp; After PO + TT 14 calendar days<br>
                        &nbsp;&nbsp;&nbsp;- 40%&nbsp; After delivery<br>
                        &nbsp;&nbsp;&nbsp;- 30%&nbsp; After 8 AST<br>
                        4. Price exclude Tax 12%<br>
                        5. Warranty : 12 months
                    @endif

                    <div class="thankyou">THANK YOU</div>
                    <div class="contact-box">
                        For questions concerning this quote, please contact:<br>
                        <strong>Agung Indikirono</strong><br>
                        +62 813-9816-4077<br>
                        marketing@stintegrator.com
                    </div>
                </div>
            </td>
            <td style="width:58%;">
                <table class="sig-table">
                    <tr>
                        <td>
                            <div class="sig-line">PT. Sistem Teknologi Integrator</div>
                            <div class="sig-role">Prepared by</div>
                        </td>
                        <td>
                            <div class="sig-line">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                            <div class="sig-role">Checked by</div>
                        </td>
                        <td>
                            <div class="sig-line">{{ $quotation->client_company }}</div>
                            <div class="sig-role">Approved by Customer</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</div>{{-- end page --}}

<div class="footer-strip"></div>
<div class="footer-text">
    Generated: {{ now()->format('d/m/Y H:i') }} WIB &nbsp;·&nbsp; {{ $quotation->quote_number }}
</div>
</body>
</html>
