<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Produksi Crimping</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0d47a1; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; color: #0d47a1; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; vertical-align: top; }
        th { background-color: #0d47a1; color: white; font-size: 9px; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; }
        .text-right { text-align: right; }
        .summary { margin-top: 20px; padding: 10px; background: #e3f2fd; font-size: 9px; border-radius: 5px; }
        
        .col-no { width: 5%; }
        .col-tanggal { width: 8%; }
        .col-line { width: 8%; }
        .col-operator { width: 8%; }
        .col-produk { width: 8%; }
        .col-part { width: 10%; }
        .col-lot { width: 8%; }
        .col-warna { width: 8%; }
        .col-target { width: 7%; text-align: right; }
        .col-qty { width: 7%; text-align: right; }
        .col-reject { width: 7%; text-align: right; }
        .col-hasil { width: 7%; text-align: right; }
        .col-keterangan { width: 12%; word-wrap: break-word; }
        
        .table-striped tbody tr:nth-child(even) { background-color: #f5f5f5; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PT GETRONICS BATAM</h1>
        <p>Laporan Produksi Crimping</p>
        <p>Tanggal Cetak: {{ date('d F Y H:i:s') }}</p>
        <p>Total Data: {{ $data->count() }} record</p>
    </div>

    <table class="table-striped">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-tanggal">Tanggal</th>
                <th class="col-line">Line Crimping</th>
                <th class="col-operator">Operator</th>
                <th class="col-produk">Produk</th>
                <th class="col-part">Part Number</th>
                <th class="col-lot">Lot</th>
                <th class="col-warna">Warna</th>
                <th class="col-target">Target</th>
                <th class="col-qty">QTY</th>
                <th class="col-reject">Reject</th>
                <th class="col-hasil">Hasil</th>
                <th class="col-keterangan">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td class="col-no">{{ $index + 1 }}</td>
                <td class="col-tanggal">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td class="col-line">{{ $item->line_crimping }}</td>
                <td class="col-operator">{{ $item->nama_operator }}</td>
                <td class="col-produk">{{ $item->produk }}</td>
                <td class="col-part">{{ $item->part_number ?? '-' }}</td>
                <td class="col-lot">{{ $item->lot_produk ?? '-' }}</td>
                <td class="col-warna">{{ $item->warna ?? '-' }}</td>
                <td class="col-target text-right">{{ number_format($item->target) }}</td>
                <td class="col-qty text-right">{{ number_format($item->qty) }}</td>
                <td class="col-reject text-right">{{ number_format($item->reject) }}</td>
                <td class="col-hasil text-right">{{ number_format(($item->qty ?? 0) - ($item->reject ?? 0)) }}</td>
                <td class="col-keterangan">{{ $item->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #0d47a1; color: white; font-weight: bold;">
                <td colspan="8" class="text-right">TOTAL</td>
                <td class="text-right">{{ number_format($data->sum('target')) }}</td>
                <td class="text-right">{{ number_format($data->sum('qty')) }}</td>
                <td class="text-right">{{ number_format($data->sum('reject')) }}</td>
                <td class="text-right">{{ number_format($data->sum('qty') - $data->sum('reject')) }}</td>
                <td class="text-right"> </td>
            </tr>
        </tfoot>
    </table>

    <div class="summary">
        <strong>RINGKASAN PRODUKSI CRIMPING:</strong><br>
        Total Target: {{ number_format($data->sum('target')) }} unit<br>
        Total QTY: {{ number_format($data->sum('qty')) }} unit<br>
        Total Reject: {{ number_format($data->sum('reject')) }} unit<br>
        Total Produksi Baik: {{ number_format($data->sum('qty') - $data->sum('reject')) }} unit<br>
        Efisiensi Produksi: {{ $data->sum('target') > 0 ? round(($data->sum('qty') / $data->sum('target')) * 100, 2) : 0 }}%
    </div>

    <div class="footer">
        Sistem Informasi Manajemen Produksi - PT Getronics Batam
    </div>
</body>
</html>