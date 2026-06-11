@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Tambah Mesin Baru</h3>
            <p class="text-muted mb-0">Input data mesin produksi baru</p>
        </div>
        <a href="{{ route('mesin.index') }}" class="btn btn-secondary rounded-pill">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('mesin.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Kode Mesin <span class="text-danger">*</span></label>
                        <input type="text" name="kode_mesin" class="form-control @error('kode_mesin') is-invalid @enderror" 
                               value="{{ old('kode_mesin') }}" placeholder="Contoh: M-001, MC-01" required>
                        @error('kode_mesin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Nama Mesin <span class="text-danger">*</span></label>
                        <input type="text" name="nama_mesin" class="form-control @error('nama_mesin') is-invalid @enderror" 
                               value="{{ old('nama_mesin') }}" placeholder="Contoh: Mesin Cutting 1" required>
                        @error('nama_mesin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Jenis Mesin</label>
                        <select name="jenis_mesin" class="form-select">
                            <option value="">Pilih Jenis Mesin</option>
                            <option value="Cutting Machine" {{ old('jenis_mesin') == 'Cutting Machine' ? 'selected' : '' }}>Cutting Machine</option>
                            <option value="Crimping Machine" {{ old('jenis_mesin') == 'Crimping Machine' ? 'selected' : '' }}>Crimping Machine</option>
                            <option value="Twisting Machine" {{ old('jenis_mesin') == 'Twisting Machine' ? 'selected' : '' }}>Twisting Machine</option>
                            <option value="Assembly Machine" {{ old('jenis_mesin') == 'Assembly Machine' ? 'selected' : '' }}>Assembly Machine</option>
                            <option value="Testing Machine" {{ old('jenis_mesin') == 'Testing Machine' ? 'selected' : '' }}>Testing Machine</option>
                            <option value="Packing Machine" {{ old('jenis_mesin') == 'Packing Machine' ? 'selected' : '' }}>Packing Machine</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi') }}" placeholder="Contoh: Line A, Gedung 1">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="Beroperasi" {{ old('status') == 'Beroperasi' ? 'selected' : '' }}>Beroperasi</option>
                            <option value="Perbaikan" {{ old('status') == 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                            <option value="Rusak" {{ old('status') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="Maintenance" {{ old('status') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="Idle" {{ old('status') == 'Idle' ? 'selected' : '' }}>Idle</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Prioritas Gangguan</label>
                        <select name="prioritas" class="form-select">
                            <option value="Rendah" {{ old('prioritas') == 'Rendah' ? 'selected' : '' }}>Rendah</option>
                            <option value="Sedang" {{ old('prioritas') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="Tinggi" {{ old('prioritas') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                            <option value="Darurat" {{ old('prioritas') == 'Darurat' ? 'selected' : '' }}>Darurat</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tanggal Gangguan</label>
                        <input type="date" name="tanggal_gangguan" class="form-control" value="{{ old('tanggal_gangguan') }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Teknisi</label>
                        <input type="text" name="teknisi" class="form-control" value="{{ old('teknisi') }}" placeholder="Nama teknisi">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Durasi Gangguan (Jam)</label>
                        <input type="number" name="durasi_gangguan" class="form-control" value="{{ old('durasi_gangguan', 0) }}" placeholder="Lama gangguan dalam jam">
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Deskripsi Gangguan</label>
                        <textarea name="gangguan" class="form-control" rows="3" placeholder="Jelaskan gangguan yang terjadi">{{ old('gangguan') }}</textarea>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Informasi tambahan jika ada">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('mesin.index') }}" class="btn btn-secondary rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection