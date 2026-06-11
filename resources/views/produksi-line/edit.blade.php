@extends(
Auth::user()->role=='manager'
? 'layouts.manager'
: 'layouts.app'
)

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Edit Data Produksi Line</h3>
        </div>
        <div class="card-body">
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

            <form method="POST" action="{{ route('produksi-line.update', $produksiLine) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $produksiLine->tanggal) }}" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Proses <span class="text-danger">*</span></label>
                        <select name="proses" class="form-control" required>
                            <option value="">-- Pilih Proses --</option>
                            <option value="TWISTING" {{ old('proses', $produksiLine->proses) == 'TWISTING' ? 'selected' : '' }}>TWISTING</option>
                            <option value="TUBING" {{ old('proses', $produksiLine->proses) == 'TUBING' ? 'selected' : '' }}>TUBING</option>
                            <option value="TAPING" {{ old('proses', $produksiLine->proses) == 'TAPING' ? 'selected' : '' }}>TAPING</option>
                            <option value="SOLDER" {{ old('proses', $produksiLine->proses) == 'SOLDER' ? 'selected' : '' }}>SOLDER</option>
                            <option value="OC TEST" {{ old('proses', $produksiLine->proses) == 'OC TEST' ? 'selected' : '' }}>OC TEST</option>
                            <option value="INSERTING" {{ old('proses', $produksiLine->proses) == 'INSERTING' ? 'selected' : '' }}>INSERTING</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Line <span class="text-danger">*</span></label>
                        <input type="text" name="nama_line" class="form-control" value="{{ old('nama_line', $produksiLine->nama_line) }}" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Operator</label>
                        <input type="text" name="nama_operator" class="form-control" value="{{ old('nama_operator', $produksiLine->nama_operator) }}">
                    </div>
                    
                    <!-- PRODUK - DROPDOWN -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Produk <span class="text-danger">*</span></label>
                        <select name="produk" id="produk" class="form-control" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($produk as $item)
                                <option value="{{ $item->nama_produk }}" 
                                    data-part="{{ $item->part_number }}" 
                                    data-target="{{ $item->target_standar }}"
                                    {{ old('produk', $produksiLine->produk) == $item->nama_produk ? 'selected' : '' }}>
                                    {{ $item->kode_produk }} - {{ $item->nama_produk }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- PART NUMBER - OTOMATIS (READONLY) -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Part Number <span class="text-danger">*</span></label>
                        <input type="text" name="part_number" id="part_number" class="form-control" value="{{ old('part_number', $produksiLine->part_number) }}" required readonly style="background-color:#e9ecef">
                        <small class="text-muted">Akan terisi otomatis setelah memilih produk</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Lot Produk</label>
                        <input type="text" name="lot_produk" class="form-control" value="{{ old('lot_produk', $produksiLine->lot_produk) }}">
                    </div>
                    
                    <!-- TARGET - OTOMATIS (READONLY) -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Target <span class="text-danger">*</span></label>
                        <input type="number" name="target" id="target" class="form-control" value="{{ old('target', $produksiLine->target) }}" required readonly style="background-color:#e9ecef">
                        <small class="text-muted">Akan terisi otomatis setelah memilih produk</small>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">QTY <span class="text-danger">*</span></label>
                        <input type="number" name="qty" id="qty" class="form-control" value="{{ old('qty', $produksiLine->qty) }}" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Reject</label>
                        <input type="number" name="reject" id="reject" class="form-control" value="{{ old('reject', $produksiLine->reject) }}">
                        <small class="text-danger" id="reject-warning" style="display: none;">
                            <i class="fas fa-exclamation-circle me-1"></i> Reject tidak boleh lebih besar dari QTY!
                        </small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Downtime (menit)</label>
                        <input type="number" name="downtime" class="form-control" value="{{ old('downtime', $produksiLine->downtime) }}">
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $produksiLine->keterangan) }}</textarea>
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary" id="btn-submit">
                        <i class="fas fa-save"></i> Update
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
    
    // Validasi reject tidak boleh lebih dari qty
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