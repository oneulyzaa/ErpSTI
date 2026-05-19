<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        @page { margin: 15mm 10mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #333; }
        .header { text-align: center; margin-bottom: 16px; }
        .header img { max-height: 50px; margin-bottom: 6px; }
        .header h2 { margin: 2px 0; font-size: 15px; color: #1e3a5f; }
        .header p { margin: 1px 0; font-size: 10px; color: #555; }
        table { width: 100%; border-collapse: collapse; font-size: 8px; }
        th { background: #1e3a5f; color: #fff; padding: 5px 3px; text-align: center; font-weight: bold; }
        td { padding: 3px; border: 1px solid #ccc; vertical-align: middle; }
        tr:nth-child(even) { background: #f8fafc; }
        .right { text-align: right; }
        .center { text-align: center; }
        .grand-total td { background: #e8f0fe; font-weight: bold; }
        .pay-lunas { color: #15803d; font-weight: bold; }
        .pay-sebagian { color: #92400e; }
        .pay-belum { color: #b91c1c; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 7px; color: #999; padding: 5px 0; border-top: 1px solid #ddd; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <div class="header">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="Logo STI">
        @endif
        <h2>LAPORAN PENJUALAN</h2>
        <p>PT. Sistem Teknologi Integrator</p>
        @if($dateFrom && $dateTo)
            <p>Periode: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
        @elseif($dateFrom)
            <p>Dari: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}</p>
        @elseif($dateTo)
            <p>Sampai: {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. SO</th>
                <th>Proyek</th>
                <th>Perusahaan</th>
                <th>Tanggal</th>
                <th>Quotation</th>
                <th>Sales Order</th>
                <th>Produksi</th>
                <th>DO</th>
                <th>Invoice</th>
                <th>Pembayaran</th>
                <th>Terbayar</th>
                <th>Tagihan</th>
                <th>Nilai SO (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 0; @endphp
            @foreach($salesOrders as $so)
            @php
                $no++;
                $quo = $so->quotation;
                $quoStatus = $quo ? $quo->status : '-';

                $prodColl = $productions->get($so->id, collect());
                $prodStatus = $prodColl->first() ? $prodColl->first()->status : '-';

                $doColl = $deliveryOrders->get($so->id, collect());
                $doStatus = $doColl->first() ? $doColl->first()->status : '-';

                $invColl = $invoices->get($so->id, collect());
                $inv = $invColl->first();
                $invStatus = $inv ? $inv->status : '-';

                $totalPaid = 0;
                $totalInv  = 0;
                if ($inv) {
                    $totalInv  = $inv->total;
                    $totalPaid = $inv->receipts->where('status', 'confirmed')->sum('amount');
                }

                $payLabel = $totalInv > 0 && $totalPaid >= $totalInv ? 'Lunas'
                          : ($totalPaid > 0 ? 'Sebagian' : 'Belum Dibayar');
                $payClass = $totalPaid >= $totalInv && $totalInv > 0 ? 'pay-lunas'
                          : ($totalPaid > 0 ? 'pay-sebagian' : 'pay-belum');
            @endphp
            <tr>
                <td class="center">{{ $no }}</td>
                <td>{{ $so->so_number }}</td>
                <td>{{ $so->project_name ?: '-' }}</td>
                <td>{{ $so->client_company }}</td>
                <td class="center">{{ $so->date->format('d/m/Y') }}</td>
                <td class="center">{{ ucfirst($quoStatus) }}</td>
                <td class="center">{{ ucfirst(str_replace('_', ' ', $so->status)) }}</td>
                <td class="center">{{ $prodStatus !== '-' ? ucfirst(str_replace('_', ' ', $prodStatus)) : '-' }}</td>
                <td class="center">{{ $doStatus !== '-' ? ucfirst(str_replace('_', ' ', $doStatus)) : '-' }}</td>
                <td class="center">{{ $invStatus !== '-' ? ucfirst($invStatus) : '-' }}</td>
                <td class="center {{ $payClass }}">{{ $payLabel }}</td>
                <td class="right">{{ number_format($totalPaid, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($totalInv, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($so->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="13" class="right">GRAND TOTAL</td>
                <td class="right">{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Laporan di-generot pada {{ now()->format('d/m/Y H:i') }} | PT. Sistem Teknologi Integrator
    </div>

</body>
</html>
