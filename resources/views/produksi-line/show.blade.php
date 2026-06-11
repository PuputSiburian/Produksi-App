@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Detail Data Produksi Line</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr><th width="40%">Tanggal</th><td>{{ $produksiLine->tanggal }}</td></tr>
                        <tr><th>Proses</th><td>{{ $produksiLine->proses }}</td></tr>
                        <tr><th>Nama Line</th><td>{{ $produksiLine->nama_line }}</td></tr>
                        <!-- <tr><th>Shift</th><td>{{ $produksiLine->shift }}</td></tr> -- DIHAPUS -->
                        <tr><th>Nama Operator</th><td>{{ $produksiLine->nama_operator ?? '-' }}</td></tr>
                        <tr><th>Produk</th><td>{{ $produksiLine->produk }}</td></tr>
                        <tr><th>Part Number</th><td>{{ $produksiLine->part_number }}</td></tr>
                        <tr><th>Lot Produk</th><td>{{ $produksiLine->lot_produk ?? '-' }}</td></tr>
                        <tr><th>Target</th><td>{{ number_format($produksiLine->target) }}</td></tr>
                        <tr><th>Actual</th><td>{{ number_format($produksiLine->qty) }}</td></tr> <!-- ubah actual jadi qty -->
                        <tr><th>Reject</th><td>{{ number_format($produksiLine->reject) }}</td></tr>
                        <tr><th>Downtime</th><td>{{ $produksiLine->downtime ?? 0 }} menit</td></tr>
                        <tr><th>Hasil</th><td>{{ number_format($produksiLine->qty - $produksiLine->reject) }}</td></tr>
                        <tr><th>Keterangan</th><td>{{ $produksiLine->keterangan ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('produksi-line.edit', $produksiLine->id) }}" class="btn btn-warning">Edit</a>
                <a href="{{ route('produksi-line.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection