@extends(
Auth::user()->role=='manager'
? 'layouts.manager'
: 'layouts.app'
)

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-header bg-white py-3" style="background: rgba(255,255,255,0.9) !important; border-bottom: none;">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-cut me-2 text-primary"></i> Data Produksi Cutting
                    </h5>
                </div>
                <div class="card-body" style="background: rgba(255,255,255,0.95);">
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

                    <!-- TOMBOL DI ATAS TABEL -->
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            @if(Auth::user()->role == 'admin')
                            <a href="{{ route('produksi-cutting.create') }}" class="btn btn-primary rounded-pill px-3" style="background: linear-gradient(45deg, #007bff, #00c6ff); border: none;">
                                <i class="fas fa-plus me-1"></i> Tambah Data
                            </a>
                            @endif
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            {{-- 🔥 TOMBOL EXPORT LAPORAN (HARIAN & MINGGUAN) --}}
                            <a href="{{ route('produksi-cutting.export.page') }}" class="btn btn-success rounded-pill px-3" style="background: linear-gradient(45deg, #28a745, #20c997); border: none;">
                                <i class="fas fa-file-export me-1"></i> Export Laporan
                            </a>
                            {{-- 🔥 TOMBOL EXPORT SEMUA DATA --}}
                            <a href="{{ route('produksi-cutting.export.pdf') }}" class="btn btn-danger rounded-pill px-3" style="background: linear-gradient(45deg, #dc3545, #ff6b6b); border: none;">
                                <i class="fas fa-file-pdf me-1"></i> Export Semua
                            </a>
                            {{-- 🔥 HAPUS TOMBOL EXPORT MINGGUAN (SUDAH DIGABUNG DI EXPORT LAPORAN) --}}
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle" style="background: white; border-radius: 12px; overflow: hidden;">
                            <thead style="background: linear-gradient(135deg, #1a237e, #283593); color: white;">
                                <tr>
                                    <th class="text-center" style="width: 50px">No</th>
                                    <th>Tanggal</th>
                                    <th>Line Cutting</th>
                                    <th>Operator</th>
                                    <th>Produk</th>
                                    <th>Part Number</th>
                                    <th>Lot Produk</th>
                                    <th>Warna</th>
                                    <th class="text-end">Target</th>
                                    <th class="text-end">QTY</th>
                                    <th class="text-end">Reject</th>
                                    <th class="text-center" style="width: 180px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produksiCuttings as $key => $item)
                                <tr style="background-color: {{ $loop->iteration % 2 == 0 ? '#f0f4ff' : '#ffffff' }}; transition: all 0.3s;">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                                    <td>{{ $item->line_cutting }}</td>
                                    <td>{{ $item->nama_operator }}</td>
                                    <td>{{ $item->produk }}</td>
                                    <td>{{ $item->part_number ?? '-' }}</td>
                                    <td>{{ $item->lot_produk ?? '-' }}</td>
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
                                    </td>
                                    <td class="text-end">{{ number_format($item->target) }}</td>
                                    <td class="text-end">{{ number_format($item->qty) }}</td>
                                    <td class="text-end">
                                        @if($item->reject > 0)
                                            <span class="badge bg-danger">{{ number_format($item->reject) }}</span>
                                        @else
                                            {{ number_format($item->reject) }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                                            @if(Auth::user()->role == 'admin')
                                                <a href="{{ route('produksi-cutting.edit', $item) }}" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('produksi-cutting.destroy', $item) }}" method="POST" class="d-inline">
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
                                            <a href="{{ route('produksi-cutting.history', $item->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-2 py-1" title="Riwayat Perubahan">
                                                <i class="fas fa-history"></i> Riwayat
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="text-center py-4 text-muted">
                                        <i class="fas fa-database me-2"></i> Belum ada data produksi cutting
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot style="background: linear-gradient(135deg, #e8eaf6, #c5cae9); font-weight: bold;">
                                <tr>
                                    <td colspan="8" class="text-end">Total:</td>
                                    <td class="text-end">{{ number_format($produksiCuttings->sum('target')) }}</td>
                                    <td class="text-end">{{ number_format($produksiCuttings->sum('qty')) }}</td>
                                    <td class="text-end">{{ number_format($produksiCuttings->sum('reject')) }}</td>
                                    <td class="text-end"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        {{ $produksiCuttings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection