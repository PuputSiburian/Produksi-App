@extends(
Auth::user()->role=='manager'
? 'layouts.manager'
: 'layouts.app'
)

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-plug me-2 text-primary"></i> Data Produksi Crimping
                        </h5>
                    </div>
                </div>
                <div class="card-body">
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

                    <!-- TOMBOL: Tambah Data di KIRI, Export PDF di KANAN -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            @if(Auth::user()->role == 'admin')
                            <a href="{{ route('produksi-crimping.create') }}" class="btn btn-primary rounded-pill px-3">
                                <i class="fas fa-plus me-1"></i> Tambah Data
                            </a>
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('produksi-crimping.export.pdf') }}" class="btn btn-danger rounded-pill px-3">
                                <i class="fas fa-file-pdf me-1"></i> Export PDF
                            </a>
                            <!-- TOMBOL EXPORT MINGGUAN -->
                            <button type="button" class="btn btn-info rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalMingguanCrimping">
                                <i class="fas fa-calendar-week me-1"></i> Export Mingguan
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light" style="background: linear-gradient(135deg, #1a237e, #283593); color: white;">
                                <tr>
                                    <th class="text-center" style="width: 50px">No</th>
                                    <th>Tanggal</th>
                                    <th>Line Crimping</th>
                                    <th>Operator</th>
                                    <th>Produk</th>
                                    <th>Part Number</th>
                                    <th>Lot</th>
                                    <th>Warna</th>
                                    <th class="text-end">Target</th>
                                    <th class="text-end">QTY</th>
                                    <th class="text-end">Reject</th>
                                    <th class="text-center" style="width: 120px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produksiCrimpings as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                                    <td>{{ $item->line_crimping }}
                                    <td>{{ $item->nama_operator }}
                                    <td>{{ $item->produk }}
                                    <td>{{ $item->part_number ?? '-' }}
                                    <td>{{ $item->lot_produk ?? '-' }}
                                    <td>
                                        @php
                                            $warna = strtolower($item->warna ?? '');
                                            $bgColor = '#6c757d';
                                            $textColor = 'white';
                                            
                                            if($warna == 'merah') {
                                                $bgColor = '#dc3545';
                                            } elseif($warna == 'hitam') {
                                                $bgColor = '#343a40';
                                            } elseif($warna == 'biru') {
                                                $bgColor = '#007bff';
                                            } elseif($warna == 'hijau') {
                                                $bgColor = '#28a745';
                                            } elseif($warna == 'kuning') {
                                                $bgColor = '#ffc107';
                                                $textColor = '#212529';
                                            } elseif($warna == 'ungu') {
                                                $bgColor = '#6f42c1';
                                            } elseif($warna == 'orange') {
                                                $bgColor = '#fd7e14';
                                            } elseif($warna == 'pink') {
                                                $bgColor = '#e83e8c';
                                            } elseif($warna == 'coklat') {
                                                $bgColor = '#795548';
                                            }
                                        @endphp
                                        <span class="badge" style="background-color: {{ $bgColor }}; color: {{ $textColor }}; padding: 6px 12px; border-radius: 20px;">
                                            {{ $item->warna ?? '-' }}
                                        </span>
                                    
                                    <td class="text-end">{{ number_format($item->target) }}
                                    <td class="text-end">{{ number_format($item->qty) }}
                                    <td class="text-end">
                                        @if($item->reject > 0)
                                            <span class="badge bg-danger">{{ number_format($item->reject) }}</span>
                                        @else
                                            {{ number_format($item->reject) }}
                                        @endif
                                    
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            @if(Auth::user()->role == 'admin')
                                                <a href="{{ route('produksi-crimping.edit', $item) }}" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('produksi-crimping.destroy', $item) }}" method="POST" class="d-inline">
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
                                            <a href="{{ route('produksi-crimping.history', $item->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-2 py-1" title="Riwayat Perubahan">
                                                <i class="fas fa-history"></i> Riwayat
                                            </a>
                                        </div>
                                    
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="text-center py-4 text-muted">
                                        <i class="fas fa-database me-2"></i> Belum ada data produksi crimping
                                    
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light" style="background: linear-gradient(135deg, #e8eaf6, #c5cae9); font-weight: bold;">
                                <tr>
                                    <td colspan="8" class="text-end">Total:
                                    <td class="text-end">{{ number_format($produksiCrimpings->sum('target')) }}
                                    <td class="text-end">{{ number_format($produksiCrimpings->sum('qty')) }}
                                    <td class="text-end">{{ number_format($produksiCrimpings->sum('reject')) }}
                                    <td class="text-end">
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        {{ $produksiCrimpings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EXPORT PDF MINGGUAN - CRIMPING -->
<div class="modal fade" id="modalMingguanCrimping" tabindex="-1" aria-labelledby="modalMingguanCrimpingLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalMingguanCrimpingLabel">
                    <i class="fas fa-calendar-week me-2 text-info"></i> Export Laporan Mingguan - Crimping
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('produksi-crimping.mingguan.download') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Pilih tahun, bulan, dan minggu untuk mengekspor laporan produksi crimping.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Tahun</label>
                        <select name="tahun" class="form-control" required>
                            @for($i = date('Y')-2; $i <= date('Y')+1; $i++)
                                <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Bulan</label>
                        <select name="bulan" class="form-control" required>
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6" selected>Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Minggu ke-</label>
                        <select name="minggu" class="form-control" required>
                            <option value="1">Minggu 1 (1-7)</option>
                            <option value="2">Minggu 2 (8-14)</option>
                            <option value="3">Minggu 3 (15-21)</option>
                            <option value="4">Minggu 4 (22-28)</option>
                            <option value="5">Minggu 5 (29-31)</option>
                        </select>
                    </div>
                    
                    <div class="alert alert-secondary mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Laporan akan menampilkan data produksi crimping dalam periode minggu yang dipilih.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-info rounded-pill" id="btnExportMingguan">
                        <i class="fas fa-file-pdf me-1"></i> Export PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Menampilkan loading saat export
    document.getElementById('btnExportMingguan')?.addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';
    });
</script>
@endsection