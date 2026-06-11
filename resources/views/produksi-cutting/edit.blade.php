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
                    <h4>Edit Data Produksi Cutting</h4>
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
                            <strong>Terjadi Kesalahan!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('produksi-cutting.update', $produksiCutting) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal', $produksiCutting->tanggal) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="line_cutting" class="form-label">Line Cutting <span class="text-danger">*</span></label>
                                <input type="text" name="line_cutting" id="line_cutting" class="form-control" value="{{ old('line_cutting', $produksiCutting->line_cutting) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nama_operator" class="form-label">Nama Operator <span class="text-danger">*</span></label>
                                <input type="text" name="nama_operator" id="nama_operator" class="form-control" value="{{ old('nama_operator', $produksiCutting->nama_operator) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="proses" class="form-label">Proses</label>
                                <input type="text" name="proses" id="proses" class="form-control" value="{{ old('proses', $produksiCutting->proses) }}">
                            </div>

                            <!-- PRODUK - DROPDOWN -->
                            <div class="col-md-6 mb-3">
                                <label for="produk" class="form-label">Produk <span class="text-danger">*</span></label>
                                <select name="produk" id="produk" class="form-control" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($produk as $item)
                                        <option value="{{ $item->nama_produk }}" 
                                            data-part="{{ $item->part_number }}" 
                                            data-target="{{ $item->target_standar }}"
                                            {{ old('produk', $produksiCutting->produk) == $item->nama_produk ? 'selected' : '' }}>
                                            {{ $item->kode_produk }} - {{ $item->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="lot_produk" class="form-label">Lot Produk</label>
                                <input type="text" name="lot_produk" id="lot_produk" class="form-control" value="{{ old('lot_produk', $produksiCutting->lot_produk) }}">
                            </div>

                            <!-- PART NUMBER - READONLY -->
                            <div class="col-md-6 mb-3">
                                <label for="part_number" class="form-label">Part Number <span class="text-danger">*</span></label>
                                <input type="text" name="part_number" id="part_number" class="form-control" value="{{ old('part_number', $produksiCutting->part_number) }}" required readonly style="background-color:#e9ecef">
                                <small class="text-muted">Akan terisi otomatis setelah memilih produk</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="warna" class="form-label">Warna <span class="text-danger">*</span></label>
                                <input type="text" name="warna" id="warna" class="form-control" value="{{ old('warna', $produksiCutting->warna) }}" required>
                            </div>

                            <!-- TARGET - READONLY -->
                            <div class="col-md-4 mb-3">
                                <label for="target" class="form-label">Target <span class="text-danger">*</span></label>
                                <input type="number" name="target" id="target" class="form-control" value="{{ old('target', $produksiCutting->target) }}" required readonly style="background-color:#e9ecef">
                                <small class="text-muted">Akan terisi otomatis setelah memilih produk</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="qty" class="form-label">Quantity (Qty) <span class="text-danger">*</span></label>
                                <input type="number" name="qty" id="qty" class="form-control" value="{{ old('qty', $produksiCutting->qty) }}" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="reject" class="form-label">Reject</label>
                                <input type="number" name="reject" id="reject" class="form-control" value="{{ old('reject', $produksiCutting->reject) }}">
                                <small class="text-danger">Reject tidak boleh lebih besar dari QTY</small>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan', $produksiCutting->keterangan) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('produksi-cutting.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>
@endsection