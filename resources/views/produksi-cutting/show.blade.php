@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Detail Data Produksi Cutting</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Tanggal</th>
                            <td>{{ \Carbon\Carbon::parse($produksiCutting->tanggal)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Line Cutting</th>
                            <td>{{ $produksiCutting->line_cutting }}</td>
                        </tr>
                        <tr>
                            <th>Nama Operator</th>
                            <td>{{ $produksiCutting->nama_operator }}</td>
                        </tr>
                        <tr>
                            <th>Proses</th>
                            <td>{{ $produksiCutting->proses ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Produk</th>
                            <td>{{ $produksiCutting->produk }}</td>
                        </tr>
                        <tr>
                            <th>Lot Produk</th>
                            <td>{{ $produksiCutting->lot_produk ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Part Number</th>  <!-- DITAMBAH -->
                            <td>{{ $produksiCutting->part_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Warna</th>
                            <td>{{ $produksiCutting->warna }}</td>
                        </tr>
                        <tr>
                            <th>Target</th>
                            <td>{{ number_format($produksiCutting->target) }}</td>
                        </tr>
                        <tr>
                            <th>Quantity (Qty)</th>
                            <td>{{ number_format($produksiCutting->qty) }}</td>
                        </tr>
                        <tr>
                            <th>Reject</th>
                            <td>{{ number_format($produksiCutting->reject) }}</td>
                        </tr>
                    </table>

                    <a href="{{ route('produksi-cutting.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection