<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Produksi Line - PT Getronics Batam</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 9px; 
            margin: 20px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #007bff; 
            padding-bottom: 10px; 
        }
        .header h1 { 
            margin: 0; 
            font-size: 16px; 
            color: #007bff; 
        }
        .header p { 
            margin: 5px 0; 
        }
        
        .filter-form {
            margin-bottom: 20px;
            padding: 15px;
            background: #e9ecef;
            border-radius: 8px;
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        .filter-group label {
            font-size: 11px;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        .filter-group select, .filter-group input {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 12px;
        }
        .btn-filter {
            padding: 6px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-pdf {
            padding: 6px 15px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-filter:hover { background-color: #0056b3; }
        .btn-pdf:hover { background-color: #c82333; }
        
        .info-periode {
            text-align: center;
            padding: 10px;
            background: #d4edda;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 5px; 
            text-align: left; 
            vertical-align: top; 
        }
        th { 
            background-color: #007bff; 
            color: white; 
            font-size: 9px; 
        }
        .footer { 
            margin-top: 20px; 
            text-align: center; 
            font-size: 9px; 
        }
        .text-right { 
            text-align: right; 
        }
        .summary { 
            margin-top: 20px; 
            padding: 10px; 
            background: #f5f5f5; 
            font-size: 9px; 
        }
        
        .col-no { width: 5%; }
        .col-tanggal { width: 8%; }
        .col-proses { width: 8%; }
        .col-line { width: 5%; }
        .col-operator { width: 8%; }
        .col-produk { width: 8%; }
        .col-part { width: 10%; }
        .col-lot { width: 8%; }
        .col-target { width: 7%; text-align: right; }
        .col-qty { width: 7%; text-align: right; }
        .col-reject { width: 7%; text-align: right; }
        .col-downtime { width: 8%; text-align: right; }
        .col-hasil { width: 7%; text-align: right; }
        .col-keterangan { width: 12%; word-wrap: break-word; }
    </style>
</head>
<body>

<!-- FORM FILTER MINGGUAN (hanya tampil jika ada parameter filter) -->
@if(request()->has('tahun') || request()->has('bulan') || request()->has('minggu') || request()->get('filter') == 'weekly')
<form method="GET" action="{{ url('produksi-line-export-weekly') }}" class="filter-form">
    <div class="filter-group">
        <label>Pilih Tahun</label>
        <select name="tahun">
            @for($i = date('Y')-2; $i <= date('Y')+1; $i++)
                <option value="{{ $i }}" {{ (request()->get('tahun', date('Y')) == $i) ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
    </div>
    <div class="filter-group">
        <label>Pilih Bulan</label>
        <select name="bulan">
            <option value="1" {{ (request()->get('bulan', date('m')) == 1) ? 'selected' : '' }}>Januari</option>
            <option value="2" {{ (request()->get('bulan', date('m')) == 2) ? 'selected' : '' }}>Februari</option>
            <option value="3" {{ (request()->get('bulan', date('m')) == 3) ? 'selected' : '' }}>Maret</option>
            <option value="4" {{ (request()->get('bulan', date('m')) == 4) ? 'selected' : '' }}>April</option>
            <option value="5" {{ (request()->get('bulan', date('m')) == 5) ? 'selected' : '' }}>Mei</option>
            <option value="6" {{ (request()->get('bulan', date('m')) == 6) ? 'selected' : '' }}>Juni</option>
            <option value="7" {{ (request()->get('bulan', date('m')) == 7) ? 'selected' : '' }}>Juli</option>
            <option value="8" {{ (request()->get('bulan', date('m')) == 8) ? 'selected' : '' }}>Agustus</option>
            <option value="9" {{ (request()->get('bulan', date('m')) == 9) ? 'selected' : '' }}>September</option>
            <option value="10" {{ (request()->get('bulan', date('m')) == 10) ? 'selected' : '' }}>Oktober</option>
            <option value="11" {{ (request()->get('bulan', date('m')) == 11) ? 'selected' : '' }}>November</option>
            <option value="12" {{ (request()->get('bulan', date('m')) == 12) ? 'selected' : '' }}>Desember</option>
        </select>
    </div>
    <div class="filter-group">
        <label>Pilih Minggu ke-</label>
        <select name="minggu">
            <option value="1" {{ (request()->get('minggu', 1) == 1) ? 'selected' : '' }}>Minggu 1 (1-7)</option>
            <option value="2" {{ (request()->get('minggu', 1) == 2) ? 'selected' : '' }}>Minggu 2 (8-14)</option>
            <option value="3" {{ (request()->get('minggu', 1) == 3) ? 'selected' : '' }}>Minggu 3 (15-21)</option>
            <option value="4" {{ (request()->get('minggu', 1) == 4) ? 'selected' : '' }}>Minggu 4 (22-28)</option>
            <option value="5" {{ (request()->get('minggu', 1) == 5) ? 'selected' : '' }}>Minggu 5 (29-31)</option>
        </select>
    </div>
    <div class="filter-group">
        <button type="submit" class="btn-filter">Tampilkan</button>
    </div>
    <div class="filter-group">
        <button type="button" class="btn-pdf" onclick="exportToPDF()">Export PDF</button>
    </div>
</form>
@endif

<!-- HEADER LAPORAN -->
<div class="header">
    <h1>PT GETRONICS BATAM</h1>
    <p>Laporan Produksi Line / Assembly</p>
    <p>Tanggal Cetak: {{ date('d F Y H:i:s') }}</p>
</div>

<!-- INFORMASI PERIODE -->
<div class="info-periode">
    📅 Periode Laporan: 
    @if(isset($tanggal_mulai) && isset($tanggal_akhir) && $tanggal_mulai && $tanggal_akhir)
        {{ \Carbon\Carbon::parse($tanggal_mulai)->translatedFormat('d F Y') }} 
        s/d 
        {{ \Carbon\Carbon::parse($tanggal_akhir)->translatedFormat('d F Y') }}
        @if(isset($minggu))
            (Minggu ke-{{ $minggu }})
        @endif
    @else
        Semua Data (Tanpa Filter)
    @endif
</div>

<!-- TOTAL DATA -->
<p>Total Data: {{ isset($data) ? $data->count() : 0 }} record</p>

<!-- TABEL DATA -->
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th class="col-no">No</th>
            <th class="col-tanggal">Tanggal</th>
            <th class="col-proses">Proses</th>
            <th class="col-line">Line</th>
            <th class="col-operator">Operator</th>
            <th class="col-produk">Produk</th>
            <th class="col-part">Part Number</th>
            <th class="col-lot">Lot</th>
            <th class="col-target">Target</th>
            <th class="col-qty">QTY</th>
            <th class="col-reject">Reject</th>
            <th class="col-downtime">Downtime</th>
            <th class="col-hasil">Hasil</th>
            <th class="col-keterangan">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($data) && $data->count() > 0)
            @foreach($data as $index => $item)
            <tr>
                <td class="col-no">{{ $index + 1 }}</td>
                <td class="col-tanggal">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td class="col-proses">{{ $item->proses }}</td>
                <td class="col-line">{{ $item->nama_line }}</td>
                <td class="col-operator">{{ $item->nama_operator ?? '-' }}</td>
                <td class="col-produk">{{ $item->produk }}</td>
                <td class="col-part">{{ $item->part_number }}</td>
                <td class="col-lot">{{ $item->lot_produk ?? '-' }}</td>
                <td class="col-target text-right">{{ number_format($item->target) }}</td>
                <td class="col-qty text-right">{{ number_format($item->qty) }}</td>
                <td class="col-reject text-right">{{ number_format($item->reject) }}</td>
                <td class="col-downtime text-right">{{ $item->downtime ?? 0 }} min</td>
                <td class="col-hasil text-right">{{ number_format(($item->qty ?? 0) - ($item->reject ?? 0)) }}</td>
                <td class="col-keterangan">{{ $item->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="14" style="text-align: center;">Tidak ada data untuk periode yang dipilih</td>
            </tr>
        @endif
    </tbody>
    @if(isset($data) && $data->count() > 0)
    <tfoot>
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <td colspan="8" class="text-right">TOTAL</td>
            <td class="text-right">{{ number_format($data->sum('target')) }}</td>
            <td class="text-right">{{ number_format($data->sum('qty')) }}</td>
            <td class="text-right">{{ number_format($data->sum('reject')) }}</td>
            <td class="text-right">{{ number_format($data->sum('downtime')) }} min</td>
            <td class="text-right">{{ number_format($data->sum('qty') - $data->sum('reject')) }}</td>
            <td class="text-right"></td>
        </tr>
    </tfoot>
    @endif
</table>

<!-- RINGKASAN -->
@if(isset($data) && $data->count() > 0)
<div class="summary">
    <strong>📊 RINGKASAN PRODUKSI LINE:</strong><br>
    Total Target: {{ number_format($data->sum('target')) }} unit<br>
    Total QTY: {{ number_format($data->sum('qty')) }} unit<br>
    Total Reject: {{ number_format($data->sum('reject')) }} unit<br>
    Total Downtime: {{ number_format($data->sum('downtime')) }} menit<br>
    Total Produksi Baik: {{ number_format($data->sum('qty') - $data->sum('reject')) }} unit<br>
    Efisiensi Produksi: {{ $data->sum('target') > 0 ? round(($data->sum('qty') / $data->sum('target')) * 100, 2) : 0 }}%
</div>
@endif

<div class="footer">
    Sistem Informasi Manajemen Produksi - PT Getronics Batam
</div>

<script>
    function exportToPDF() {
        let tahun = document.querySelector('select[name="tahun"]').value;
        let bulan = document.querySelector('select[name="bulan"]').value;
        let minggu = document.querySelector('select[name="minggu"]').value;
        window.location.href = '{{ url("produksi-line-download-weekly") }}?tahun=' + tahun + '&bulan=' + bulan + '&minggu=' + minggu;
    }
</script>

</body>
</html>