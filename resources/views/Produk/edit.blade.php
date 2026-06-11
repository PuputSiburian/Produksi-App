@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Produk</h3>
            <a href="{{ route('produk.index') }}" class="btn btn-default btn-sm float-right">Kembali</a>
        </div>
        <form action="{{ route('produk.update', $produk->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kode Produk <span class="text-danger">*</span></label>
                            <input type="text" name="kode_produk" class="form-control" value="{{ $produk->kode_produk }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="nama_produk" class="form-control" value="{{ $produk->nama_produk }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Part Number</label>
                            <input type="text" name="part_number" class="form-control" value="{{ $produk->part_number }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Stasiun <span class="text-danger">*</span></label>
                            <select name="stasiun" class="form-control" required>
                                <option value="Cutting" {{ $produk->stasiun == 'Cutting' ? 'selected' : '' }}>Cutting</option>
                                <option value="Crimping" {{ $produk->stasiun == 'Crimping' ? 'selected' : '' }}>Crimping</option>
                                <option value="Line" {{ $produk->stasiun == 'Line' ? 'selected' : '' }}>Line</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Target Standar</label>
                            <input type="number" name="target_standar" class="form-control" value="{{ $produk->target_standar }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="Aktif" {{ $produk->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Tidak Aktif" {{ $produk->status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3">{{ $produk->deskripsi }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('produk.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection