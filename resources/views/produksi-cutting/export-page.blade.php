<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Produksi Cutting - PT Getronics Batam</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Arial', sans-serif; 
            font-size: 9px; 
            padding: 15px;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #0d47a1;
            padding-bottom: 10px;
        }
        .header h1 { font-size: 18px; color: #0d47a1; }
        .header .sub-title { font-size: 14px; font-weight: bold; }
        .header .periode { font-size: 11px; color: #555; }
        .header .info { font-size: 9px; color: #777; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 8px; }
        th { background-color: #0d47a1; color: white; padding: 5px 3px; border: 1px solid #0d47a1; text-align: center; }
        td { padding: 4px 3px; border: 1px solid #ddd; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .table-striped tbody tr:nth-child(even) { background-color: #f8f9fa; }
        
        .total-row { background-color: #0d47a1 !important; color: white !important; font-weight: bold; }
        .total-row td { background-color: #0d47a1 !important; color: white !important; border-color: #0d47a1; }
        
        .summary {
            margin-top: 15px;
            padding: 12px 15px;
            background: #e3f2fd;
            border-left: 4px solid #0d47a1;
            font-size: 10px;
        }
        .summary table { border: none; width: 100%; }
        .summary td { border: none; padding: 3px 5px; background: transparent; }
        .summary .label { font-weight: bold; color: #0d47a1; }
        .summary .value.good { color: #2e7d32; }
        .summary .value.bad { color: #c62828; }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PT Getronics Batam</h1>
        <div class="sub-title">Laporan Produksi Cutting</div>
        <div class="periode">
            Periode: {{ Carbon\Carbon::parse($tanggal_mulai)->format('d F Y') }} 
            s/d {{ Carbon\Carbon::parse($tanggal_akhir)->format('d F Y') }}
        </div>
        <div class="info">
            {{ $minggu ?? 'Semua Data' }} | Total Data: {{ $data->count() }} record
        </div>
    </div>

    <table class="table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Line Cutting</th>
                <th>Operator</th>
                <th>Produk</th>
                <th>Part Number</th>
                <th>Lot</th>
                <th>Warna</th>
                <th>Target</th>
                <th>QTY</th>
                <th>Reject</th>
                <th>Hasil</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td class="text-center">{{ $item->line_cutting ?? '-' }}</td>
                <td>{{ $item->nama_operator ?? '-' }}</td>
                <td>{{ $item->produk ?? '-' }}</td>
                <td>{{ $item->part_number ?? '-' }}</td>
                <td class="text-center">{{ $item->lot_produk ?? '-' }}</td>
                <td class="text-center">{{ $item->warna ?? '-' }}</td>
                <td class="text-right">{{ number_format($item->target ?? 0) }}</td>
                <td class="text-right">{{ number_format($item->qty ?? 0) }}</td>
                <td class="text-right">{{ number_format($item->reject ?? 0) }}</td>
                <td class="text-right">{{ number_format(($item->qty ?? 0) - ($item->reject ?? 0)) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="8" class="text-right"><strong>TOTAL</strong></td>
                <td class="text-right"><strong>{{ number_format($totalTarget ?? $data->sum('target')) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalQty ?? $data->sum('qty')) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalReject ?? $data->sum('reject')) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalHasil ?? $data->sum('qty') - $data->sum('reject')) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <!-- 🔥 DAILY STATS (jika ada) -->
    @if(isset($dailyStats) && count($dailyStats) > 0)
    <div style="margin-top: 20px; page-break-before: avoid;">
        <h4 style="text-align: center; margin-bottom: 10px;">📊 Rincian Harian</h4>
        <table style="width: 100%; border-collapse: collapse; font-size: 8px;">
            <thead>
                <tr style="background-color: #e3f2fd;">
                    <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Hari</th>
                    <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Tanggal</th>
                    <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Target</th>
                    <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">QTY</th>
                    <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Reject</th>
                    <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Hasil</th>
                    <th style="border: 1px solid #ddd; padding: 4px; text-align: center;">Efisiensi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dailyStats as $stat)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center; {{ isset($stat['is_weekend']) && $stat['is_weekend'] ? 'background-color: #fff3cd;' : '' }}">
                        {{ $stat['nama_hari'] ?? '-' }}
                        @if(isset($stat['is_weekend']) && $stat['is_weekend'])
                            <span style="color: #856404;">(Libur)</span>
                        @endif
                    </td>
                    <td style="border: 1px solid #ddd; padding: 4px; text-align: center;">{{ $stat['hari'] ?? '-' }}</td>
                    <td style="border: 1px solid #ddd; padding: 4px; text-align: right;">{{ number_format($stat['target'] ?? 0) }}</td>
                    <td style="border: 1px solid #ddd; padding: 4px; text-align: right;">{{ number_format($stat['qty'] ?? 0) }}</td>
                    <td style="border: 1px solid #ddd; padding: 4px; text-align: right;">{{ number_format($stat['reject'] ?? 0) }}</td>
                    <td style="border: 1px solid #ddd; padding: 4px; text-align: right;">{{ number_format($stat['hasil'] ?? 0) }}</td>
                    <td style="border: 1px solid #ddd; padding: 4px; text-align: right;">
                        {{ isset($stat['target']) && $stat['target'] > 0 ? round(($stat['qty'] / $stat['target']) * 100, 2) : 0 }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #0d47a1; color: white; font-weight: bold;">
                    <td colspan="2" style="padding: 4px; text-align: right;">TOTAL</td>
                    <td style="padding: 4px; text-align: right;">{{ number_format(collect($dailyStats)->sum('target')) }}</td>
                    <td style="padding: 4px; text-align: right;">{{ number_format(collect($dailyStats)->sum('qty')) }}</td>
                    <td style="padding: 4px; text-align: right;">{{ number_format(collect($dailyStats)->sum('reject')) }}</td>
                    <td style="padding: 4px; text-align: right;">{{ number_format(collect($dailyStats)->sum('hasil')) }}</td>
                    <td style="padding: 4px; text-align: right;"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <div class="summary">
        <table>
            <tr>
                <td class="label">Target:</td>
                <td>{{ number_format($totalTarget ?? $data->sum('target')) }}</td>
                <td class="label">QTY:</td>
                <td>{{ number_format($totalQty ?? $data->sum('qty')) }}</td>
                <td class="label">Reject:</td>
                <td class="bad">{{ number_format($totalReject ?? $data->sum('reject')) }}</td>
                <td class="label">Hasil:</td>
                <td class="good">{{ number_format($totalHasil ?? $data->sum('qty') - $data->sum('reject')) }}</td>
                <td class="label">Efisiensi:</td>
                <td>{{ number_format($efisiensi ?? 0, 2) }}%</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dicetak: {{ date('d/m/Y H:i') }} | Sistem Manajemen Produksi PT Getronics Batam
    </div>
</body>
</html>