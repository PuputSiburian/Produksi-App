@extends(
Auth::user()->role=='manager'
? 'layouts.manager'
: 'layouts.app'
)

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Tambah Data Produksi Line</h3>
        </div>
        <div class="card-body">
            
            {{-- 🔥 INFORMASI KOLOM WAJIB --}}
            <div class="alert alert-secondary">
                <i class="fas fa-info-circle me-2"></i>
                Kolom dengan tanda <span class="text-danger">*</span> wajib diisi.
            </div>

            <!-- TAMPILKAN ERROR SESSION -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- TAMPILKAN ERROR VALIDASI -->
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

            <form method="POST" action="{{ route('produksi-line.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Tanggal <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" 
                               value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Proses <span class="text-danger">*</span>
                        </label>
                        <select name="proses" class="form-control @error('proses') is-invalid @enderror" required>
                            <option value="">-- Pilih Proses --</option>
                            <option value="TWISTING" {{ old('proses') == 'TWISTING' ? 'selected' : '' }}>TWISTING</option>
                            <option value="TUBING" {{ old('proses') == 'TUBING' ? 'selected' : '' }}>TUBING</option>
                            <option value="TAPING" {{ old('proses') == 'TAPING' ? 'selected' : '' }}>TAPING</option>
                            <option value="SOLDER" {{ old('proses') == 'SOLDER' ? 'selected' : '' }}>SOLDER</option>
                            <option value="OC TEST" {{ old('proses') == 'OC TEST' ? 'selected' : '' }}>OC TEST</option>
                            <option value="INSERTING" {{ old('proses') == 'INSERTING' ? 'selected' : '' }}>INSERTING</option>
                        </select>
                        @error('proses')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Nama Line <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_line" class="form-control @error('nama_line') is-invalid @enderror" 
                               placeholder="LINE 1 / LINE 2 / LINE 3" value="{{ old('nama_line') }}" required>
                        @error('nama_line')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Operator</label>
                        <input type="text" name="nama_operator" class="form-control @error('nama_operator') is-invalid @enderror" 
                               placeholder="Nama operator" value="{{ old('nama_operator') }}">
                        @error('nama_operator')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- PRODUK - DROPDOWN -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Produk <span class="text-danger">*</span>
                        </label>
                        <select name="produk" id="produk" class="form-control @error('produk') is-invalid @enderror" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($produk as $item)
                                <option value="{{ $item->nama_produk }}" 
                                    data-part="{{ $item->part_number }}" 
                                    data-target="{{ $item->target_standar }}"
                                    {{ old('produk') == $item->nama_produk ? 'selected' : '' }}>
                                    {{ $item->kode_produk }} - {{ $item->nama_produk }}
                                </option>
                            @endforeach
                        </select>
                        @error('produk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- PART NUMBER - OTOMATIS (READONLY) -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Part Number <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="part_number" id="part_number" 
                               class="form-control @error('part_number') is-invalid @enderror" 
                               placeholder="Part number" value="{{ old('part_number') }}" 
                               required readonly style="background-color:#e9ecef">
                        <small class="text-muted">Akan terisi otomatis setelah memilih produk</small>
                        @error('part_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- 🔥 LOT PRODUK - WAJIB DENGAN BINTANG -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Lot Produk <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="lot_produk" id="lot_produk" 
                               class="form-control @error('lot_produk') is-invalid @enderror" 
                               placeholder="Masukkan nomor lot produk" value="{{ old('lot_produk') }}" required>
                        <small class="text-muted">Wajib diisi</small>
                        @error('lot_produk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- TARGET - OTOMATIS (READONLY) -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            Target <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="target" id="target" 
                               class="form-control @error('target') is-invalid @enderror" 
                               placeholder="Jumlah target" value="{{ old('target') }}" 
                               required readonly style="background-color:#e9ecef">
                        <small class="text-muted">Akan terisi otomatis setelah memilih produk</small>
                        @error('target')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            QTY <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="qty" id="qty" 
                               class="form-control @error('qty') is-invalid @enderror" 
                               placeholder="Jumlah actual" value="{{ old('qty') }}" required min="0">
                        <small class="text-muted">Jumlah produksi aktual</small>
                        @error('qty')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Reject</label>
                        <input type="number" name="reject" id="reject" 
                               class="form-control @error('reject') is-invalid @enderror" 
                               placeholder="Jumlah reject" value="{{ old('reject', 0) }}" min="0">
                        <small class="text-danger" id="reject-warning" style="display: none;">
                            <i class="fas fa-exclamation-circle me-1"></i> Reject tidak boleh lebih besar dari QTY!
                        </small>
                        @error('reject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- 🔥 LEADER NAME - PILIHAN CUTTING, CRIMPING, LINE -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
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
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Downtime (menit)</label>
                        <input type="number" name="downtime" class="form-control @error('downtime') is-invalid @enderror" 
                               placeholder="Downtime dalam menit" value="{{ old('downtime', 0) }}" min="0">
                        @error('downtime')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                  rows="3" placeholder="Catatan tambahan">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary" id="btn-submit">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                    <a href="{{ route('produksi-line.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-fill Part Number dan Target dari dropdown produk
        const produkSelect = document.getElementById('produk');
        const partNumberInput = document.getElementById('part_number');
        const targetInput = document.getElementById('target');
        
        if (produkSelect) {
            produkSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const partNumber = selectedOption.getAttribute('data-part') || '';
                const target = selectedOption.getAttribute('data-target') || 0;
                
                partNumberInput.value = partNumber;
                targetInput.value = target;
            });
        }
        
        // Validasi real-time reject tidak boleh lebih dari qty
        const qtyInput = document.getElementById('qty');
        const rejectInput = document.getElementById('reject');
        const rejectWarning = document.getElementById('reject-warning');
        const submitBtn = document.getElementById('btn-submit');

        function validateReject() {
            const qty = parseInt(qtyInput.value) || 0;
            const reject = parseInt(rejectInput.value) || 0;
            
            if (reject > qty) {
                rejectWarning.style.display = 'inline-block';
                rejectInput.classList.add('is-invalid');
                submitBtn.disabled = true;
                return false;
            } else {
                rejectWarning.style.display = 'none';
                rejectInput.classList.remove('is-invalid');
                submitBtn.disabled = false;
                return true;
            }
        }

        if (qtyInput && rejectInput) {
            qtyInput.addEventListener('input', validateReject);
            rejectInput.addEventListener('input', validateReject);
        }
    });
</script>
@endsection