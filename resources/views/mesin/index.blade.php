@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Data Mesin</h3>
        @if(Auth::user()->role == 'admin')
            <a href="{{ route('mesin.create') }}" class="btn btn-primary rounded-pill">
                <i class="fas fa-plus me-2"></i>Tambah Mesin
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistik -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total</h5>
                    <h3>{{ $statistik['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Beroperasi</h5>
                    <h3>{{ $statistik['beroperasi'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Perbaikan</h5>
                    <h3>{{ $statistik['perbaikan'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Rusak</h5>
                    <h3>{{ $statistik['rusak'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Maintenance</h5>
                    <h3>{{ $statistik['maintenance'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Idle</h5>
                    <h3>{{ $statistik['idle'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px">No</th>
                            <th>Kode Mesin</th>
                            <th>Nama Mesin</th>
                            <th>Jenis Mesin</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Prioritas</th>
                            <th>Teknisi</th>
                            <th class="text-center" style="width: 100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mesins as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->kode_mesin }}</td>
                            <td>{{ $item->nama_mesin }}</td>
                            <td>{{ $item->jenis_mesin ?? '-' }}</td>
                            <td>{{ $item->lokasi ?? '-' }}</td>
                            <td>
                                @if($item->status == 'Beroperasi')
                                    <span class="badge bg-success">Beroperasi</span>
                                @elseif($item->status == 'Perbaikan')
                                    <span class="badge bg-warning">Perbaikan</span>
                                @elseif($item->status == 'Rusak')
                                    <span class="badge bg-danger">Rusak</span>
                                @elseif($item->status == 'Maintenance')
                                    <span class="badge bg-info">Maintenance</span>
                                @else
                                    <span class="badge bg-secondary">Idle</span>
                                @endif
                            </td>
                            <td>
                                @if($item->prioritas)
                                    @if($item->prioritas == 'Darurat')
                                        <span class="badge bg-danger">Darurat</span>
                                    @elseif($item->prioritas == 'Tinggi')
                                        <span class="badge bg-warning">Tinggi</span>
                                    @elseif($item->prioritas == 'Sedang')
                                        <span class="badge bg-primary">Sedang</span>
                                    @else
                                        <span class="badge bg-success">Rendah</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                            <td>{{ $item->teknisi ?? '-' }}</td>
                            <td class="text-center">
                                @if(Auth::user()->role == 'admin')
                                    <a href="{{ route('mesin.edit', $item) }}" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('mesin.destroy', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" onclick="return confirm('Yakin hapus data ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">
                                        <i class="fas fa-eye me-1"></i> Hanya Lihat
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fas fa-database me-2"></i> Belum ada data mesin
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $mesins->links() }}
            </div>
        </div>
    </div>
</div>
@endsection