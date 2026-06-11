@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Detail Produksi Crimping</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th width="30%">Tanggal</th><td>{{ $produksiCrimping->tanggal }}</td></tr>
                <tr><th>Line Crimping</th><td>{{ $produksiCrimping->line_crimping }}</td></tr>
                <tr><th>Nama Operator</th><td>{{ $produksiCrimping->nama_operator }}</td></tr>
                <tr><th>Produk</th><td>{{ $produksiCrimping->produk }}</td></tr>
                <tr><th>Lot Produk</th><td>{{ $produksiCrimping->lot_produk ?? '-' }}</td></tr>
                <tr><th>Part Number</th><td>{{ $produksiCrimping->part_number }}</td></tr>
                <tr><th>Target</th><td>{{ number_format($produksiCrimping->target) }}</td></tr>
                <tr><th>Actual</th><td>{{ number_format($produksiCrimping->actual) }}</td></tr>
                <tr><th>Reject</th><td>{{ number_format($produksiCrimping->reject) }}</td></tr>
            </table>
            
            <div class="mt-3">
                <a href="{{ route('produksi-crimping.edit', $produksiCrimping->id) }}" class="btn btn-warning">Edit</a>
                <a href="{{ route('produksi-crimping.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection
