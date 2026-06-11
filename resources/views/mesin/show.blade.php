@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Detail Mesin</h3>
            <p class="text-muted mb-0">Informasi lengkap mesin produksi</p>
        </div>
        <div>
            <a href="{{ route('mesin.edit', $mesin->id) }}" class="btn btn-warning rounded-pill">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('mesin.index') }}" class="btn btn-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr><th width="35%">Kode Mesin</th><td>: <span class="fw-bold">{{ $mesin->kode_mesin }}</span></td></tr>
                        <tr><th>Nama Mesin</th><td>: {{ $mesin->nama_mesin }}</td></tr>
                        <tr><th>Jenis Mesin</th><td>: {{ $mesin->jenis_mesin ?? '-' }}</td></tr>
                        <tr><th>Lokasi</th><td>: {{ $mesin->lokasi ?? '-' }}</td></tr>
                        <tr><th>Status</th><td>: 
                            @if($mesin->status == 'Beroperasi')
                                <span class="badge bg-success">✓ Beroperasi</span>
                            @elseif($mesin->status == 'Perbaikan')
                                <span class="badge bg-warning text-dark">🔧 Perbaikan</span>
                            @elseif($mesin->status == 'Rusak')
                                <span class="badge bg-danger">❌ Rusak</span>
                            @elseif($mesin->status == 'Maintenance')
                                <span class="badge bg-info">⚙ Maintenance</span>
                            @else
                                <span class="badge bg-secondary">⏸ Idle</span>
                            @endif
                        </td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr><th width="35%">Prioritas</th><td>: 
                            @if($mesin->prioritas == 'Darurat')
                                <span class="badge bg-danger">Darurat</span>
                            @elseif($mesin->prioritas == 'Tinggi')
                                <span class="badge bg-warning">Tinggi</span>
                            @elseif($mesin->prioritas == 'Sedang')
                                <span class="badge bg-primary">Sedang</span>
                            @else
                                <span class="badge bg-secondary">Rendah</span>
                            @endif
                        </td></tr>
                        <tr><th>Tanggal Gangguan</th><td>: {{ $mesin->tanggal_gangguan ?? '-' }}</td></tr>
                        <tr><th>Teknisi</th><td>: {{ $mesin->teknisi ?? '-' }}</td></tr>
                        <tr><th>Durasi Gangguan</th><td>: {{ $mesin->durasi_gangguan ?? 0 }} jam</td></tr>
                        <tr><th>Dibuat Oleh</th><td>: {{ $mesin->user->name ?? '-' }}</td></tr>
                        <tr><th>Terakhir Update</th><td>: {{ $mesin->updated_at->format('d/m/Y H:i') }}</td></tr>
                    </table>
                </div>
                <div class="col-12">
                    <hr>
                    <h6 class="fw-bold">Deskripsi Gangguan:</h6>
                    <p>{{ $mesin->gangguan ?? '-' }}</p>
                    
                    <h6 class="fw-bold mt-3">Keterangan:</h6>
                    <p>{{ $mesin->keterangan ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection