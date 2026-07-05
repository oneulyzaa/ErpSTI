<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        @page { margin: 15mm 10mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 16px; }
        .header img { max-height: 50px; margin-bottom: 6px; }
        .header h2 { margin: 2px 0; font-size: 15px; color: #1e3a5f; }
        .header p { margin: 1px 0; font-size: 10px; color: #555; }
        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        th { background: #1e3a5f; color: #fff; padding: 6px 4px; text-align: center; font-weight: bold; }
        td { padding: 4px; border: 1px solid #ccc; vertical-align: middle; }
        tr:nth-child(even) { background: #f8fafc; }
        .right { text-align: right; }
        .center { text-align: center; }
        .grand-total td { background: #e8f0fe; font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 7px; color: #999; padding: 5px 0; border-top: 1px solid #ddd; }
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
                <th width="5%">No</th>
                <th width="12%">No. Sales Order</th>
                <th width="18%">Nama Klien</th>
                <th width="12%">Nomor PO</th>
                <th width="20%">Nama Project</th>
                <th width="10%">Tanggal SO</th>
                <th width="10%">Status Invoice</th>
                <th width="13%">Nilai Project</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 0; @endphp
            @foreach($salesOrders as $so)
            @php
                $no++;
                $invColl = $invoices->get($so->id, collect());
                $inv = $invColl->first();
                $invStatus = $inv ? $inv->status : '-';
            @endphp
            <tr>
                <td class="center">{{ $no }}</td>
                <td>{{ $so->so_number }}</td>
                <td>{{ $so->client_company }}</td>
                <td>{{ $so->nomor_po ?: '-' }}</td>
                <td>{{ $so->project_name ?: '-' }}</td>
                <td class="center">{{ $so->date->format('d/m/Y') }}</td>
                <td class="center">{{ $invStatus !== '-' ? ucfirst($invStatus) : '-' }}</td>
                <td class="right">{{ number_format($so->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="7" class="right">GRAND TOTAL</td>
                <td class="right">{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Laporan di-generate pada {{ now()->format('d/m/Y H:i') }} | PT. Sistem Teknologi Integrator
    </div>

</body>
</html>