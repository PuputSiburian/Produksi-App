@extends('layouts.app')

@section('title', 'Kelola Produk')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Kelola Data Produk</h3>
            <a href="{{ route('produk.create') }}" class="btn btn-primary btn-sm float-right">
                + Tambah Produk
            </a>
        </div>
        <div class="card-body">
            <!-- Form Filter -->
            <form method="GET" action="{{ route('produk.index') }}" class="row mb-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="stasiun" class="form-control">
                        <option value="semua" {{ request('stasiun') == 'semua' ? 'selected' : '' }}>Semua Stasiun</option>
                        <option value="Cutting" {{ request('stasiun') == 'Cutting' ? 'selected' : '' }}>Cutting</option>
                        <option value="Crimping" {{ request('stasiun') == 'Crimping' ? 'selected' : '' }}>Crimping</option>
                        <option value="Line" {{ request('stasiun') == 'Line' ? 'selected' : '' }}>Line</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary">Filter</button>
                    <a href="{{ route('produk.index') }}" class="btn btn-default">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode Produk</th>
                            <th>Nama Produk</th>
                            <th>Part Number</th>
                            <th>Stasiun</th>
                            <th class="text-right">Target Standar</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produk as $index => $item)
                        <tr>
                            <td>{{ $produk->firstItem() + $index }}</td>
                            <td>{{ $item->kode_produk }}</td>
                            <td>{{ $item->nama_produk }}</td>
                            <td>{{ $item->part_number ?? '-' }}</td>
                            <td>
                                @if($item->stasiun == 'Cutting')
                                    <span class="badge bg-primary">Cutting</span>
                                @elseif($item->stasiun == 'Crimping')
                                    <span class="badge bg-info">Crimping</span>
                                @else
                                    <span class="badge bg-success">Line</span>
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($item->target_standar) }}</td>
                            <td>
                                @if($item->status == 'Aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('produk.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('produk.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin hapus produk ini?')" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data produk. Silakan tambah produk baru.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $produk->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection