@extends(
Auth::user()->role=='manager'
? 'layouts.manager'
: 'layouts.app'
)

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-chart-line me-2 text-primary"></i> Data Produksi Line
                    </h5>
                    <!-- TOMBOL TAMBAH DATA DI BAWAH TULISAN -->
                    <div class="mt-2">
                        @if(Auth::user()->role == 'admin')
                        <a href="{{ route('produksi-line.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                            <i class="fas fa-plus me-1"></i> Tambah Data
                        </a>
                        @endif
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <!-- Export PDF Semua Data -->
                    <a href="{{ route('produksi-line.export.pdf') }}" class="btn btn-danger btn-sm rounded-pill px-3">
                        <i class="fas fa-file-pdf me-1"></i> Export PDF
                    </a>
                    
                    <!-- Export PDF Mingguan (Tombol Buka Modal) -->
                    <button type="button" class="btn btn-info btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalMingguan">
                        <i class="fas fa-calendar-week me-1"></i> Export Mingguan
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- STATISTIK CARD - HANYA UNTUK MANAGER -->
            @if(Auth::user()->role == 'manager')
            <div class="row mb-4 g-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Total Produksi</h6>
                            <h3 class="mb-0">{{ $produksiLines->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Total Target</h6>
                            <h3 class="mb-0">{{ number_format($produksiLines->sum('target')) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Total QTY</h6>
                            <h3 class="mb-0">{{ number_format($produksiLines->sum('qty')) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Total Reject</h6>
                            <h3 class="mb-0">{{ number_format($produksiLines->sum('reject')) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- TABEL DATA -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px">No</th>
                            <th>Tanggal</th>
                            <th>Proses</th>
                            <th>Line</th>
                            <th>Operator</th>
                            <th>Produk</th>
                            <th>Part Number</th>
                            <th>Lot Produk</th>
                            <th class="text-end">Target</th>
                            <th class="text-end">QTY</th>
                            <th class="text-end">Reject</th>
                            <th class="text-center" style="width: 150px">Aksi</th>
                        </td>
                    </thead>
                    <tbody>
                        @forelse($produksiLines as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                            <td>{{ $item->proses }}
                            <td>{{ $item->nama_line }}
                            <td>{{ $item->nama_operator ?? '-' }}
                            <td>{{ $item->produk }}
                            <td>{{ $item->part_number ?? '-' }}
                            <td>{{ $item->lot_produk ?? '-' }}
                            <td class="text-end">{{ number_format($item->target) }}
                            <td class="text-end">{{ number_format($item->qty) }}
                            <td class="text-end">
                                @if($item->reject > 0)
                                    <span class="badge bg-danger rounded-pill">{{ number_format($item->reject) }}</span>
                                @else
                                    {{ number_format($item->reject) }}
                                @endif
                            
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    @if(Auth::user()->role == 'admin')
                                        <a href="{{ route('produksi-line.edit', $item->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('produksi-line.destroy', $item->id) }}" method="POST" class="d-inline">
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
                                    <a href="{{ route('produksi-line.history', $item->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-2 py-1" title="Riwayat Perubahan">
                                        <i class="fas fa-history"></i> Riwayat
                                    </a>
                                </div>
                            
                        </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-5">
                                    <i class="fas fa-database fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">Belum ada data produksi line</p>
                                
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="8" class="text-end">Total:
                            <td class="text-end">{{ number_format($produksiLines->sum('target')) }}
                            <td class="text-end">{{ number_format($produksiLines->sum('qty')) }}
                            <td class="text-end">{{ number_format($produksiLines->sum('reject')) }}
                            <td class="text-end">
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="mt-4 pt-2 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <i class="fas fa-table me-1"></i> 
                        Menampilkan {{ $produksiLines->firstItem() ?? 0 }} - {{ $produksiLines->lastItem() ?? 0 }} 
                        dari {{ $produksiLines->total() ?? 0 }} data
                    </div>
                    <div class="d-flex justify-content-center">
                        {{ $produksiLines->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EXPORT PDF MINGGUAN -->
<div class="modal fade" id="modalMingguan" tabindex="-1" aria-labelledby="modalMingguanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalMingguanLabel">
                    <i class="fas fa-calendar-week me-2 text-info"></i> Export Laporan Mingguan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('produksi-line.mingguan.download') }}" method="POST" id="formMingguan">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info alert-dismissible fade show">
                        <i class="fas fa-info-circle me-2"></i> Pilih tahun, bulan, dan minggu untuk mengekspor laporan produksi line.
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
                        <small>Laporan akan menampilkan data produksi line dalam periode minggu yang dipilih.</small>
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
    // Optional: Menampilkan loading saat export
    document.getElementById('formMingguan')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnExportMingguan');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';
    });
</script>
@endsection