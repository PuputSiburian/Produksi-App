@extends(
Auth::user()->role=='manager'
? 'layouts.manager'
: 'layouts.app'
)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Tambah Data Produksi Crimping</h4>
                </div>
                <div class="card-body">
                    
                    {{-- 🔥 TAMPILKAN ERROR --}}
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i> Terjadi Kesalahan!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- 🔥 INFORMASI KOLOM WAJIB --}}
                    <div class="alert alert-secondary">
                        <i class="fas fa-info-circle me-2"></i>
                        Kolom dengan tanda <span class="text-danger">*</span> wajib diisi.
                    </div>

                    <form action="{{ route('produksi-crimping.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            {{-- TANGGAL --}}
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label">
                                    Tanggal <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="tanggal" id="tanggal" 
                                       class="form-control @error('tanggal') is-invalid @enderror" 
                                       value="{{ old('tanggal') }}" required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- LINE CRIMPING --}}
                            <div class="col-md-6 mb-3">
                                <label for="line_crimping" class="form-label">
                                    Line Crimping <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="line_crimping" id="line_crimping" 
                                       class="form-control @error('line_crimping') is-invalid @enderror" 
                                       value="{{ old('line_crimping') }}" required>
                                @error('line_crimping')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- NAMA OPERATOR --}}
                            <div class="col-md-6 mb-3">
                                <label for="nama_operator" class="form-label">
                                    Nama Operator <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nama_operator" id="nama_operator" 
                                       class="form-control @error('nama_operator') is-invalid @enderror" 
                                       value="{{ old('nama_operator') }}" required>
                                @error('nama_operator')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- PROSES --}}
                            <div class="col-md-6 mb-3">
                                <label for="proses" class="form-label">Proses</label>
                                <input type="text" name="proses" id="proses" 
                                       class="form-control @error('proses') is-invalid @enderror" 
                                       value="{{ old('proses', 'CRIMPING') }}">
                                @error('proses')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- PRODUK --}}
                            <div class="col-md-6 mb-3">
                                <label for="produk" class="form-label">
                                    Produk <span class="text-danger">*</span>
                                </label>
                                <select name="produk" id="produk" 
                                        class="form-control @error('produk') is-invalid @enderror" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($produk as $item)
                                        <option value="{{ $item->nama_produk }}" 
                                            data-part="{{ $item->part_number }}" 
                                            data-target="{{ $item->target_standar }}"
                                            data-lot="{{ $item->lot_produk ?? '' }}"
                                            {{ old('produk') == $item->nama_produk ? 'selected' : '' }}>
                                            {{ $item->kode_produk }} - {{ $item->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('produk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 🔥 LOT PRODUK - WAJIB DENGAN BINTANG --}}
                            <div class="col-md-6 mb-3">
                                <label for="lot_produk" class="form-label">
                                    Lot Produk <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="lot_produk" id="lot_produk" 
                                       class="form-control @error('lot_produk') is-invalid @enderror" 
                                       value="{{ old('lot_produk') }}" 
                                       placeholder="Masukkan nomor lot produk" required>
                                <small class="text-muted">Wajib diisi</small>
                                @error('lot_produk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- PART NUMBER --}}
                            <div class="col-md-6 mb-3">
                                <label for="part_number" class="form-label">
                                    Part Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="part_number" id="part_number" 
                                       class="form-control @error('part_number') is-invalid @enderror" 
                                       value="{{ old('part_number') }}" required readonly 
                                       style="background-color:#e9ecef">
                                <small class="text-muted">Akan terisi otomatis setelah memilih produk</small>
                                @error('part_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- WARNA --}}
                            <div class="col-md-6 mb-3">
                                <label for="warna" class="form-label">
                                    Warna <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="warna" id="warna" 
                                       class="form-control @error('warna') is-invalid @enderror" 
                                       value="{{ old('warna') }}" required>
                                @error('warna')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- TARGET --}}
                            <div class="col-md-4 mb-3">
                                <label for="target" class="form-label">
                                    Target <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="target" id="target" 
                                       class="form-control @error('target') is-invalid @enderror" 
                                       value="{{ old('target') }}" required readonly 
                                       style="background-color:#e9ecef">
                                <small class="text-muted">Akan terisi otomatis setelah memilih produk</small>
                                @error('target')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- QTY --}}
                            <div class="col-md-4 mb-3">
                                <label for="qty" class="form-label">
                                    Quantity (Qty) <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="qty" id="qty" 
                                       class="form-control @error('qty') is-invalid @enderror" 
                                       value="{{ old('qty') }}" required min="0">
                                <small class="text-muted">Jumlah produksi aktual</small>
                                @error('qty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- REJECT --}}
                            <div class="col-md-4 mb-3">
                                <label for="reject" class="form-label">Reject</label>
                                <input type="number" name="reject" id="reject" 
                                       class="form-control @error('reject') is-invalid @enderror" 
                                       value="{{ old('reject', 0) }}" min="0">
                                <small class="text-danger">
                                    <i class="fas fa-info-circle me-1"></i> Reject tidak boleh lebih besar dari QTY
                                </small>
                                @error('reject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 🔥 LEADER NAME - PILIHAN CUTTING, CRIMPING, LINE --}}
                            <div class="col-md-6 mb-3">
                                <label for="leader_name" class="form-label">
                                    Nama Leader <span class="text-danger">*</span>
                                </label>
                                <select name="leader_name" id="leader_name" 
                                        class="form-control @error('leader_name') is-invalid @enderror" required>
                                    <option value="">-- Pilih Leader --</option>
                                    <option value="Leader Cutting" {{ old('leader_name') == 'Leader Cutting' ? 'selected' : '' }}>Leader Cutting</option>
                                    <option value="Leader Crimping" {{ old('leader_name') == 'Leader Crimping' ? 'selected' : '' }}>Leader Crimping</option>
                                    <option value="Leader Line" {{ old('leader_name') == 'Leader Line' ? 'selected' : '' }}>Leader Line</option>
                                </select>
                                <small class="text-muted">Pilih Leader yang bertanggung jawab</small>
                                @error('leader_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- KETERANGAN --}}
                            <div class="col-12 mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" 
                                          class="form-control @error('keterangan') is-invalid @enderror" 
                                          rows="3" placeholder="Catatan tambahan jika ada">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan
                            </button>
                            <a href="{{ route('produksi-crimping.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT UNTUK AUTOFILL --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const produkSelect = document.getElementById('produk');
        const partNumberInput = document.getElementById('part_number');
        const targetInput = document.getElementById('target');
        const lotInput = document.getElementById('lot_produk');
        
        if (produkSelect) {
            produkSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const partNumber = selectedOption.getAttribute('data-part') || '';
                const target = selectedOption.getAttribute('data-target') || 0;
                const lot = selectedOption.getAttribute('data-lot') || '';
                
                partNumberInput.value = partNumber;
                targetInput.value = target;
                
                // 🔥 Jika lot ada di database, isi otomatis
                if (lot) {
                    lotInput.value = lot;
                    lotInput.readOnly = true;
                    lotInput.style.backgroundColor = '#e9ecef';
                } else {
                    lotInput.value = '';
                    lotInput.readOnly = false;
                    lotInput.style.backgroundColor = '#ffffff';
                    lotInput.placeholder = 'Masukkan nomor lot produk';
                }
            });
        }
    });
</script>
@endsection