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
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Proses <span class="text-danger">*</span></label>
                        <select name="proses" class="form-control" required>
                            <option value="">-- Pilih Proses --</option>
                            <option value="TWISTING">TWISTING</option>
                            <option value="TUBING">TUBING</option>
                            <option value="TAPING">TAPING</option>
                            <option value="SOLDER">SOLDER</option>
                            <option value="OC TEST">OC TEST</option>
                            <option value="INSERTING">INSERTING</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Line <span class="text-danger">*</span></label>
                        <input type="text" name="nama_line" class="form-control" placeholder="LINE 1 / LINE 2 / LINE 3" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Operator</label>
                        <input type="text" name="nama_operator" class="form-control" placeholder="Nama operator">
                    </div>
                    
                    <!-- PRODUK - DROPDOWN (DIUBAH) -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Produk <span class="text-danger">*</span></label>
                        <select name="produk" id="produk" class="form-control" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($produk as $item)
                                <option value="{{ $item->nama_produk }}" 
                                    data-part="{{ $item->part_number }}" 
                                    data-target="{{ $item->target_standar }}">
                                    {{ $item->kode_produk }} - {{ $item->nama_produk }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- PART NUMBER - OTOMATIS (READONLY) -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Part Number <span class="text-danger">*</span></label>
                        <input type="text" name="part_number" id="part_number" class="form-control" placeholder="Part number" required readonly style="background-color:#e9ecef">
                        <small class="text-muted">Akan terisi otomatis setelah memilih produk</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Lot Produk</label>
                        <input type="text" name="lot_produk" class="form-control" placeholder="Nomor lot produk">
                    </div>
                    
                    <!-- TARGET - OTOMATIS (READONLY) -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Target <span class="text-danger">*</span></label>
                        <input type="number" name="target" id="target" class="form-control" placeholder="Jumlah target" required readonly style="background-color:#e9ecef">
                        <small class="text-muted">Akan terisi otomatis setelah memilih produk</small>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">QTY <span class="text-danger">*</span></label>
                        <input type="number" name="qty" id="qty" class="form-control" placeholder="Jumlah actual" required>
                        <small class="text-muted">Jumlah produksi aktual</small>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Reject</label>
                        <input type="number" name="reject" id="reject" class="form-control" placeholder="Jumlah reject" value="0">
                        <small class="text-danger" id="reject-warning" style="display: none;">
                            <i class="fas fa-exclamation-circle me-1"></i> Reject tidak boleh lebih besar dari QTY!
                        </small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Downtime (menit)</label>
                        <input type="number" name="downtime" class="form-control" placeholder="Downtime dalam menit" value="0">
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan tambahan"></textarea>
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary" id="btn-submit">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('produksi-line.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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

    qtyInput.addEventListener('input', validateReject);
    rejectInput.addEventListener('input', validateReject);
</script>
@endsection