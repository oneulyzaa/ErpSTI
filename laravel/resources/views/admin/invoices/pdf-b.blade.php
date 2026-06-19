{{-- invoice/pdf-b.blade.php - Simple Summary Version --}}
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

        .page { padding: 22px 28px 18px; }

        /* ── HEADER ── */
        .header-wrap { width: 100%; border-collapse: collapse; }
        .header-wrap td { vertical-align: top; padding: 0; }

        .logo-cell { width: 210px; }
        .logo-cell img { width: 160px; display: block; }

        .company-info {
            font-size: 7.5px;
            color: #666;
            line-height: 1.7;
            margin-top: 5px;
        }
        .company-info a { color: #1B5DBC; text-decoration: none; }

        .title-cell { text-align: right; }

        .doc-title {
            font-size: 26px;
            font-weight: bold;
            letter-spacing: 4px;
            color: #1B5DBC;
            text-transform: uppercase;
            line-height: 1;
            margin-bottom: 10px;
        }

        .meta-table { border-collapse: collapse; margin-left: auto; }
        .meta-table td {
            padding: 2.5px 8px;
            font-size: 8px;
            white-space: nowrap;
            border: 0.5px solid #d4dae6;
        }
        .meta-label {
            background: #f2f5fb;
            color: #555;
            text-align: right;
            width: 78px;
        }
        .meta-value {
            background: #fff;
            min-width: 130px;
            font-family: 'DejaVu Sans Mono', monospace;
        }

        /* ── STATUS BADGES ── */
        .status-badge {
            display: inline-block;
            padding: 1px 7px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .status-unpaid    { background: #fff8e6; color: #a06000; border: 0.5px solid #f0c040; }
        .status-paid      { background: #edf7ed; color: #1e6e2e; border: 0.5px solid #7cc47c; }
        .status-cancelled { background: #f4f4f4; color: #888; border: 0.5px solid #ccc; text-decoration: line-through; }

        /* ── DIVIDER ── */
        .divider {
            border: none;
            border-top: 1.5px solid #1B5DBC;
            margin: 8px 0 7px;
        }

        /* ── CLIENT BLOCK ── */
        .client-block {
            width: 100%;
            border-collapse: collapse;
            border: 0.5px solid #d4dae6;
            border-radius: 3px;
            margin-bottom: 10px;
        }
        .client-block td {
            padding: 6px 10px;
            font-size: 8.5px;
            vertical-align: top;
        }
        .client-lbl {
            font-size: 6.5px;
            font-weight: bold;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: .8px;
            margin-bottom: 2px;
        }
        .client-val  { font-size: 9.5px; font-weight: bold; color: #111; }
        .client-sub  { font-size: 8px; color: #555; line-height: 1.6; margin-top: 1px; }
        .client-sep  { border-top: 0.5px dotted #e0e4ed; }
        .client-divider { border-right: 0.5px solid #e0e4ed; }

        /* ── SUMMARY ── */
        .summary-table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        .summary-table td {
            padding: 6px 12px;
            font-size: 9px;
            border: 0.5px solid #d4dae6;
        }
        .s-lbl { background: #f2f5fb; color: #444; text-align: left; white-space: nowrap; width: 180px; }
        .s-val { text-align: right; font-family: 'DejaVu Sans Mono', monospace; background: #fff; }
        .s-total td {
            border-top: 1.5px solid #1B5DBC;
            font-size: 11px;
            font-weight: bold;
            background: #e8eef8;
        }
        .s-total .s-val { color: #1B5DBC; }

        /* ── BANK INFO ── */
        .bank-table { border-collapse: collapse; border: 0.5px solid #d4dae6; }
        .bank-table td { padding: 3px 9px; font-size: 8.5px; border-top: 0.5px solid #d4dae6; }
        .bank-table tr:first-child td { border-top: none; }
        .bank-lbl { background: #f2f5fb; color: #555; white-space: nowrap; min-width: 80px; }
        .bank-val { font-family: 'DejaVu Sans Mono', monospace; border-left: 0.5px solid #d4dae6; }

        /* ── FOOTER NOTES ── */
        .footer-note {
            margin-top: 10px;
            font-size: 7.5px;
            color: #555;
            line-height: 1.65;
            border-top: 0.5px solid #e0e4ed;
            padding-top: 8px;
        }

        /* ── SIGNATURE ── */
        .signature-wrap { width: 100%; border-collapse: collapse; margin-top: 26px; }
        .signature-wrap td {
            width: 50%;
            text-align: center;
            padding: 0 20px;
            font-size: 8.5px;
        }
        .sig-line {
            margin-top: 78px;
            border-top: 0.5px solid #333;
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
                    <tr>
                        <td class="meta-label">No. Invoice</td>
                        <td class="meta-value">{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Tanggal</td>
                        <td class="meta-value">{{ $invoice->date->format('d M Y') }}</td>
                    </tr>
                    @if($invoice->due_date)
                    <tr>
                        <td class="meta-label">Jatuh Tempo</td>
                        <td class="meta-value">{{ $invoice->due_date->format('d M Y') }}</td>
                    </tr>
                    @endif
                    @if($invoice->so_number)
                    <tr>
                        <td class="meta-label">Ref. SO</td>
                        <td class="meta-value">{{ $invoice->so_number }}</td>
                    </tr>
                    @endif
                    @if($invoice->nomor_po)
                    <tr>
                        <td class="meta-label">Nomor PO</td>
                        <td class="meta-value">{{ $invoice->nomor_po }}</td>
                    </tr>
                    @endif
                    @if($invoice->project_name)
                    <tr>
                        <td class="meta-label">Project</td>
                        <td class="meta-value">{{ $invoice->project_name }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="meta-label">Status</td>
                        <td class="meta-value">
                            @php
                                $statusClass = match($invoice->status) {
                                    'paid'      => 'status-paid',
                                    'cancelled' => 'status-cancelled',
                                    default     => 'status-unpaid',
                                };
                                $statusLabel = match($invoice->status) {
                                    'paid'      => 'Paid',
                                    'cancelled' => 'Cancelled',
                                    default     => 'Unpaid',
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
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
            <td class="client-divider" style="width:50%;">
                <div class="client-lbl">Kepada</div>
                <div class="client-val">{{ $invoice->client_company }}</div>
                <div class="client-sub">
                    Kontak: {{ $invoice->client_name }}
                    @if($invoice->client_attention)<br>Attn: {{ $invoice->client_attention }}@endif
                    @if($invoice->client_cc)<br>CC: {{ $invoice->client_cc }}@endif
                    @if($invoice->client_email)<br>{{ $invoice->client_email }}@endif
                </div>
            </td>
            <td style="width:50%;">
                <div class="client-lbl">Dari</div>
                <div class="client-val">PT. Sistem Teknologi Integrator</div>
                <div class="client-sub">
                    Ruko Palazo Blok AB 46, Ciantra<br>
                    Cikarang Selatan, Bekasi 17530
                </div>
            </td>
        </tr>
        @if($invoice->description)
        <tr>
            <td colspan="2" class="client-sep">
                <div class="client-lbl">Deskripsi Pekerjaan</div>
                <div class="client-sub">{{ $invoice->description }}</div>
            </td>
        </tr>
        @endif
    </table>

    {{-- ═══ SUMMARY TABLE ═══ --}}
    @php
        $totalProduksi = $invoice->subtotal ?? 0;
        $totalLabor = $invoice->subtotal_labor ?? 0;
        $otherCosts = $invoice->otherCosts ?? collect();
        $totalOther = $otherCosts->sum('subtotal');
        $subtotalAll = $totalProduksi + $totalLabor + $totalOther;
    @endphp

    <table class="summary-table">
        @if($totalProduksi > 0)
        <tr>
            <td class="s-lbl">Subtotal Produksi</td>
            <td class="s-val">Rp {{ number_format($totalProduksi, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($totalLabor > 0)
        <tr>
            <td class="s-lbl">Subtotal Tenaga Kerja</td>
            <td class="s-val">Rp {{ number_format($totalLabor, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($totalOther > 0)
        <tr>
            <td class="s-lbl">Subtotal Biaya Lain-Lain</td>
            <td class="s-val">Rp {{ number_format($totalOther, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr>
            <td class="s-lbl">Subtotal</td>
            <td class="s-val">Rp {{ number_format($subtotalAll, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="s-lbl">PPN ({{ number_format($invoice->tax_percentage, 0) }}%)</td>
            <td class="s-val">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</td>
        </tr>
        @if($invoice->discount > 0)
        <tr>
            <td class="s-lbl">Diskon</td>
            <td class="s-val">Rp {{ number_format($invoice->discount, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="s-total">
            <td class="s-lbl" style="font-weight:bold;">GRAND TOTAL</td>
            <td class="s-val">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- ═══ BANK + SUMMARY ═══ --}}
    <div style="page-break-inside: avoid;">
    <table style="width:100%;border-collapse:collapse;margin-top:20px;">
        <tr>
            {{-- Informasi Pembayaran --}}
            <td style="vertical-align:bottom;padding-right:14px;">
                <div style="font-size:6.5px;font-weight:bold;color:#aaa;text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;">
                    Informasi Pembayaran
                </div>
                <table class="bank-table">
                    <tr>
                        <td class="bank-lbl">Nama Bank</td>
                        <td class="bank-val">Bank Mandiri</td>
                    </tr>
                    <tr>
                        <td class="bank-lbl">No. Rekening</td>
                        <td class="bank-val">12345678</td>
                    </tr>
                    <tr>
                        <td class="bank-lbl">A.N.</td>
                        <td class="bank-val">Sistem Teknologi Integrator</td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align:bottom;"></td>
        </tr>
    </table>

    {{-- ═══ SYARAT & KETENTUAN ═══ --}}
    @if($invoice->term_and_condition)
    <div class="footer-note">
        <strong>Syarat & Ketentuan:</strong><br>
        {!! nl2br(e($invoice->term_and_condition)) !!}
    </div>
    @endif

    {{-- ═══ CATATAN ═══ --}}
    @if($invoice->notes)
    <div class="footer-note">
        <strong>Catatan:</strong><br>
        {!! nl2br(e($invoice->notes)) !!}
    </div>
    @endif

    {{-- ═══ TANDA TANGAN ═══ --}}
    <table class="signature-wrap">
        <tr>
            <td>
                <div>Dibuat Oleh,</div>
                <div class="sig-line"></div>
            </td>
            <td>
                <div>Disetujui Oleh,</div>
                <div class="sig-line"></div>
            </td>
        </tr>
    </table>

    {{-- ═══ PAGE NUMBERS (if needed) ═══ --}}
    <div style="text-align:center;margin-top:20px;font-size:7px;color:#999;">
        Halaman 1
    </div>

</div>
</body>
</html>
