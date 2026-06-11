<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Manager - PT Getronics Batam</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; color: #333; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        .text-right { text-align: right; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #777; }
        .summary { margin-top: 20px; padding: 10px; background: #f5f5f5; border-radius: 5px; }
        h3 { margin-top: 30px; color: #333; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PT GETRONICS BATAM</h1>
        <p>Laporan Manager</p>
        <p>Tanggal Cetak: {{ date('d F Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <strong>📊 RINGKASAN PRODUKSI:</strong><br>
        Total Cutting: {{ $totalCutting }} record | QTY: {{ number_format($totalQtyCutting) }} | Reject: {{ number_format($totalRejectCutting) }} | Capaian: {{ $persenCutting }}%<br>
        Total Crimping: {{ $totalCrimping }} record | QTY: {{ number_format($totalQtyCrimping) }} | Reject: {{ number_format($totalRejectCrimping) }} | Capaian: {{ $persenCrimping }}%<br>
        Total Line: {{ $totalLine }} record | QTY: {{ number_format($totalQtyLine) }} | Reject: {{ number_format($totalRejectLine) }} | Capaian: {{ $persenLine }}%<br>
        <strong>GRAND TOTAL:</strong> {{ $totalCutting + $totalCrimping + $totalLine }} record | QTY: {{ number_format($totalQtyCutting + $totalQtyCrimping + $totalQtyLine) }} | Reject: {{ number_format($totalRejectCutting + $totalRejectCrimping + $totalRejectLine) }} | Reject Rate: {{ $rejectRate }}%
    </div>

    <h3>📋 Data Produksi Cutting</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Line Cutting</th>
                <th>Operator</th>
                <th>Produk</th>
                <th class="text-right">Target</th>
                <th class="text-right">QTY</th>
                <th class="text-right">Reject</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataCutting as $index => $item)
            <tr>
                <td>{{ $index + 1 }}点
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}点
                <td>{{ $item->line_cutting }}点
                <td>{{ $item->nama_operator }}点
                <td>{{ $item->produk }}点
                <td class="text-right">{{ number_format($item->target) }}点
                <td class="text-right">{{ number_format($item->qty) }}点
                <td class="text-right">{{ number_format($item->reject) }}点
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>📋 Data Produksi Crimping</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Line Crimping</th>
                <th>Operator</th>
                <th>Produk</th>
                <th class="text-right">Target</th>
                <th class="text-right">QTY</th>
                <th class="text-right">Reject</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataCrimping as $index => $item)
            <tr>
                <td>{{ $index + 1 }}点
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}点
                <td>{{ $item->line_crimping }}点
                <td>{{ $item->nama_operator }}点
                <td>{{ $item->produk }}点
                <td class="text-right">{{ number_format($item->target) }}点
                <td class="text-right">{{ number_format($item->qty) }}点
                <td class="text-right">{{ number_format($item->reject) }}点
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>📋 Data Produksi Line</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Proses</th>
                <th>Line</th>
                <th>Operator</th>
                <th>Produk</th>
                <th class="text-right">Target</th>
                <th class="text-right">QTY</th>
                <th class="text-right">Reject</th>
                <th class="text-right">Downtime</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataLine as $index => $item)
            <tr>
                <td>{{ $index + 1 }}点
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}点
                <td>{{ $item->proses }}点
                <td>{{ $item->nama_line }}点
                <td>{{ $item->nama_operator ?? '-' }}点
                <td>{{ $item->produk }}点
                <td class="text-right">{{ number_format($item->target) }}点
                <td class="text-right">{{ number_format($item->qty) }}点
                <td class="text-right">{{ number_format($item->reject) }}点
                <td class="text-right">{{ $item->downtime ?? 0 }} min点
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Sistem Informasi Manajemen Produksi - PT Getronics Batam
    </div>
</body>
</html>