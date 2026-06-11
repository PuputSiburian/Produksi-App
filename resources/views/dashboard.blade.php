@extends(
Auth::user()->role=='manager'
? 'layouts.manager'
: 'layouts.app'
)

@section('content')
<div class="container-fluid px-4" style="background: #eef2f5; min-height: 100vh;">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-dark">Dashboard Produksi</h2>
            <p class="text-muted mb-0">PT Getronics Batam</p>
        </div>
        <div class="text-end">
            <span class="badge bg-secondary px-3 py-2">
                <i class="fas fa-calendar-alt me-1"></i> {{ date('d F Y') }}
            </span>
        </div>
    </div>

    <!-- Statistik Cards Utama -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-success bg-opacity-10">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Cutting</p>
                            <h2 class="fw-bold mb-0 text-success">{{ $cutting ?? 0 }}</h2>
                            <small class="text-success">Record Produksi</small>
                        </div>
                        <div class="bg-success rounded-3 p-3 d-flex align-items-center">
                            <i class="fas fa-cut fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-danger bg-opacity-10">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Crimping</p>
                            <h2 class="fw-bold mb-0 text-danger">{{ $crimping ?? 0 }}</h2>
                            <small class="text-danger">Record Produksi</small>
                        </div>
                        <div class="bg-danger rounded-3 p-3">
                            <i class="fas fa-microchip fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Line</p>
                            <h2 class="fw-bold mb-0 text-primary">{{ $line ?? 0 }}</h2>
                            <small class="text-primary">Record Produksi</small>
                        </div>
                        <div class="bg-primary rounded-3 p-3">
                            <i class="fas fa-industry fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-warning bg-opacity-10">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Produksi Hari Ini</p>
                            <h2 class="fw-bold mb-0 text-warning">{{ $hari_ini ?? 0 }}</h2>
                            <small class="text-warning">Unit Hari Ini</small>
                        </div>
                        <div class="bg-warning rounded-3 p-3">
                            <i class="fas fa-calendar-day fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Produksi - Ukuran Proporsional -->
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-3 px-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Grafik Produksi</h5>
                    <p class="text-muted small mb-0">Perbandingan total produksi per stasiun kerja</p>
                </div>
                <div class="card-body py-2 px-3">
                    <canvas id="barChart" height="250" style="max-height: 250px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-3 px-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Persentase Produksi</h5>
                    <p class="text-muted small mb-0">Distribusi produksi per stasiun</p>
                </div>
                <div class="card-body py-2 px-3">
                    <canvas id="pieChart" height="200" style="max-height: 200px; width: 100%;"></canvas>
                    
                    <!-- Legend tambahan di bawah pie chart -->
                    <div class="row mt-3 text-center">
                        <div class="col-4">
                            <div class="small">
                                <span class="badge bg-success mb-1" style="width: 12px; height: 12px; display: inline-block;"></span>
                                <span class="text-muted">Cutting</span>
                                <br>
                                <strong class="text-success">{{ number_format($chartData['cutting'] ?? 0, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="small">
                                <span class="badge bg-danger mb-1" style="width: 12px; height: 12px; display: inline-block;"></span>
                                <span class="text-muted">Crimping</span>
                                <br>
                                <strong class="text-danger">{{ number_format($chartData['crimping'] ?? 0, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="small">
                                <span class="badge bg-primary mb-1" style="width: 12px; height: 12px; display: inline-block;"></span>
                                <span class="text-muted">Line</span>
                                <br>
                                <strong class="text-primary">{{ number_format($chartData['line'] ?? 0, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STATISTIK REJECT -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-times-circle me-2 text-danger"></i>
                        Statistik Reject Produksi
                    </h5>
                    <p class="text-muted small mb-0">Analisis reject per stasiun kerja</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-danger bg-opacity-10 border-0 h-100">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Total Reject</h6>
                                    <h2 class="text-danger mb-0">{{ number_format($rejectStats['total_semua'] ?? 0) }}</h2>
                                    <small class="text-muted">Rate: {{ $rejectRate ?? 0 }}%</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success bg-opacity-10 border-0 h-100">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Reject Cutting</h6>
                                    <h3 class="text-success mb-0">{{ number_format($rejectStats['cutting']['total'] ?? 0) }}</h3>
                                    <small class="text-muted">{{ $rejectStats['cutting']['persen'] ?? 0 }}% dari total reject</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-danger bg-opacity-10 border-0 h-100">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Reject Crimping</h6>
                                    <h3 class="text-danger mb-0">{{ number_format($rejectStats['crimping']['total'] ?? 0) }}</h3>
                                    <small class="text-muted">{{ $rejectStats['crimping']['persen'] ?? 0 }}% dari total reject</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary bg-opacity-10 border-0 h-100">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Reject Line</h6>
                                    <h3 class="text-primary mb-0">{{ number_format($rejectStats['line']['total'] ?? 0) }}</h3>
                                    <small class="text-muted">{{ $rejectStats['line']['persen'] ?? 0 }}% dari total reject</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STATUS MESIN PRODUKSI -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-microchip me-2 text-info"></i>
                            Status Mesin Produksi
                        </h5>
                        <p class="text-muted small mb-0">Monitoring kondisi mesin secara real-time</p>
                    </div>
                    <a href="{{ route('mesin.index') }}" class="btn btn-sm btn-outline-primary mt-2 mt-sm-0">
                        <i class="fas fa-cogs me-1"></i> Kelola Mesin
                    </a>
                </div>
                <div class="card-body">
                    @if(($mesinStats['total'] ?? 0) > 0)
                    <div class="row">
                        <div class="col-md-2 col-6 mb-3">
                            <div class="text-center">
                                <div class="bg-secondary bg-opacity-10 rounded-circle p-3 mx-auto" style="width: 70px; height: 70px;">
                                    <i class="fas fa-industry fa-2x text-secondary mt-2"></i>
                                </div>
                                <h5 class="mt-2 mb-0">{{ $mesinStats['total'] ?? 0 }}</h5>
                                <small class="text-muted">Total Mesin</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="text-center">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3 mx-auto" style="width: 70px; height: 70px;">
                                    <i class="fas fa-check-circle fa-2x text-success mt-2"></i>
                                </div>
                                <h5 class="mt-2 mb-0 text-success">{{ $mesinStats['beroperasi'] ?? 0 }}</h5>
                                <small class="text-muted">Beroperasi</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="text-center">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3 mx-auto" style="width: 70px; height: 70px;">
                                    <i class="fas fa-tools fa-2x text-warning mt-2"></i>
                                </div>
                                <h5 class="mt-2 mb-0 text-warning">{{ $mesinStats['perbaikan'] ?? 0 }}</h5>
                                <small class="text-muted">Perbaikan</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="text-center">
                                <div class="bg-danger bg-opacity-10 rounded-circle p-3 mx-auto" style="width: 70px; height: 70px;">
                                    <i class="fas fa-exclamation-triangle fa-2x text-danger mt-2"></i>
                                </div>
                                <h5 class="mt-2 mb-0 text-danger">{{ $mesinStats['rusak'] ?? 0 }}</h5>
                                <small class="text-muted">Rusak</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="text-center">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3 mx-auto" style="width: 70px; height: 70px;">
                                    <i class="fas fa-sync-alt fa-2x text-info mt-2"></i>
                                </div>
                                <h5 class="mt-2 mb-0 text-info">{{ $mesinStats['maintenance'] ?? 0 }}</h5>
                                <small class="text-muted">Maintenance</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="text-center">
                                <div class="bg-secondary bg-opacity-10 rounded-circle p-3 mx-auto" style="width: 70px; height: 70px;">
                                    <i class="fas fa-pause-circle fa-2x text-secondary mt-2"></i>
                                </div>
                                <h5 class="mt-2 mb-0">{{ $mesinStats['idle'] ?? 0 }}</h5>
                                <small class="text-muted">Idle</small>
                            </div>
                        </div>
                    </div>

                    <!-- Mesin Bermasalah -->
                    @if(isset($mesinBermasalah) && $mesinBermasalah->count() > 0)
                    <hr>
                    <div class="mt-3">
                        <h6 class="fw-bold text-danger">
                            <i class="fas fa-bell me-1"></i> Mesin Bermasalah
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Kode Mesin</th>
                                        <th>Nama Mesin</th>
                                        <th>Status</th>
                                        <th>Gangguan</th>
                                        <th>Prioritas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mesinBermasalah as $mesin)
                                    <tr>
                                        <td>{{ $mesin->kode_mesin }}</td>
                                        <td>{{ $mesin->nama_mesin }}</td>
                                        <td>
                                            @if($mesin->status == 'Perbaikan')
                                                <span class="badge bg-warning text-dark">🔧 Perbaikan</span>
                                            @else
                                                <span class="badge bg-danger">❌ Rusak</span>
                                            @endif
                                        </td>
                                        <td>{{ $mesin->gangguan ?? '-' }}</td>
                                        <td>
                                            @if($mesin->prioritas == 'Darurat')
                                                <span class="badge bg-danger">Darurat</span>
                                            @elseif($mesin->prioritas == 'Tinggi')
                                                <span class="badge bg-warning">Tinggi</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $mesin->prioritas ?? 'Sedang' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-microchip fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Belum ada data mesin</p>
                        <a href="{{ route('mesin.create') }}" class="btn btn-sm btn-primary mt-2">
                            <i class="fas fa-plus"></i> Tambah Mesin
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Aktivitas Produksi Terbaru -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-history me-2 text-secondary"></i>
                        Aktivitas Produksi Terbaru
                    </h5>
                    <p class="text-muted small mb-0">Data real-time dari database produksi</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Tanggal</th>
                                    <th>Stasiun / Proses</th>
                                    <th>Operator</th>
                                    <th>Produk</th>
                                    <th>Part Number</th>
                                    <th>Lot Produk</th>
                                    <th>Target</th>
                                    <th>Actual</th>
                                    <th>Reject</th>
                                    <th class="pe-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $activities = $recentActivities ?? collect();
                                @endphp
                                
                                @forelse($activities as $activity)
                                @php
                                    $waktu = isset($activity->waktu) ? $activity->waktu : now();
                                    $stasiun = $activity->stasiun ?? '-';
                                    $badgeWarna = $activity->badge_warna ?? 'secondary';
                                    $type = $activity->type ?? 'line';
                                    $line = $activity->line ?? '';
                                    $operator = $activity->operator ?? '-';
                                    $produk = $activity->produk ?? '-';
                                    $partNumber = $activity->part_number ?? '-';
                                    $lotProduk = $activity->lot_produk ?? '-';
                                    $target = $activity->target ?? 0;
                                    $actual = $activity->actual ?? 0;
                                    $reject = $activity->reject ?? 0;
                                    $iconStatus = $activity->icon_status ?? 'fa-check-circle';
                                    $status = $activity->status ?? 'Selesai';
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        {{ \Carbon\Carbon::parse($waktu)->format('d/m/Y') }}
                                        <small class="text-muted d-block">{{ \Carbon\Carbon::parse($waktu)->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $badgeWarna }}-subtle text-{{ $badgeWarna }} px-3 py-2 rounded-pill">
                                            <i class="fas {{ $type == 'cutting' ? 'fa-cut' : ($type == 'crimping' ? 'fa-microchip' : 'fa-industry') }} me-1"></i>
                                            {{ $stasiun }}
                                        </span>
                                        @if(!empty($line))
                                            <small class="d-block text-muted">{{ $line }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <i class="fas fa-user-circle me-1 text-muted"></i>
                                        {{ $operator }}
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $produk }}</span>
                                    </td>
                                    <td>{{ $partNumber }}</td>
                                    <td>
                                        @if($lotProduk != '-')
                                            <span class="badge bg-secondary bg-opacity-25">{{ $lotProduk }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ number_format($target, 0, ',', '.') }}
                                    <td class="fw-semibold text-success">{{ number_format($actual, 0, ',', '.') }}
                                    <td class="text-{{ $reject > 0 ? 'danger' : 'muted' }}">
                                        {{ number_format($reject, 0, ',', '.') }}
                                    
                                    <td class="pe-4">
                                        <i class="fas {{ $iconStatus }} text-{{ $badgeWarna }} me-1"></i>
                                        {{ $status }}
                                    
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="fas fa-database fa-3x mb-3 d-block"></i>
                                        Belum ada data produksi
                                        <div class="mt-2">
                                            <a href="{{ route('produksi-cutting.create') }}" class="btn btn-primary btn-sm">Tambah Data Cutting</a>
                                            <a href="{{ route('produksi-crimping.create') }}" class="btn btn-danger btn-sm">Tambah Data Crimping</a>
                                            <a href="{{ route('produksi-line.create') }}" class="btn btn-primary btn-sm">Tambah Data Line</a>
                                        </div>
                                    
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(isset($recentActivities) && count($recentActivities) > 0)
                <div class="card-footer bg-white border-0 py-3 text-center">
                    <small class="text-muted">
                        <i class="fas fa-database me-1"></i> 
                        Menampilkan {{ count($recentActivities) }} aktivitas terbaru
                    </small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://kit.fontawesome.com/a8e9a8c9f6.js" crossorigin="anonymous"></script>
<script>
    // Data dari controller
    const actualCutting = {{ $chartData['cutting'] ?? 0 }};
    const actualCrimping = {{ $chartData['crimping'] ?? 0 }};
    const actualLine = {{ $chartData['line'] ?? 0 }};
    
    // Bar Chart
    const ctxBar = document.getElementById('barChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['Cutting', 'Crimping', 'Line'],
            datasets: [{
                label: 'Total Produksi (Unit)',
                data: [actualCutting, actualCrimping, actualLine],
                backgroundColor: ['#28a745', '#dc3545', '#0d6efd'],
                borderRadius: 8,
                barPercentage: 0.65,
                categoryPercentage: 0.8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw.toLocaleString('id-ID') + ' unit';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#e9ecef', lineWidth: 0.5 },
                    title: {
                        display: true,
                        text: 'Jumlah Unit',
                        font: { size: 11 }
                    },
                    ticks: {
                        font: { size: 10 }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 11, weight: 'bold' }
                    }
                }
            },
            layout: {
                padding: {
                    top: 5,
                    bottom: 5,
                    left: 5,
                    right: 5
                }
            }
        }
    });
    
    // Pie Chart
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: ['Cutting', 'Crimping', 'Line'],
            datasets: [{
                data: [actualCutting, actualCrimping, actualLine],
                backgroundColor: ['#28a745', '#dc3545', '#0d6efd'],
                borderWidth: 0,
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        font: { size: 10 },
                        padding: 8
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            const total = actualCutting + actualCrimping + actualLine;
                            const value = tooltipItem.raw;
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${tooltipItem.label}: ${value.toLocaleString('id-ID')} unit (${percentage}%)`;
                        }
                    }
                }
            },
            layout: {
                padding: {
                    top: 5,
                    bottom: 5,
                    left: 5,
                    right: 5
                }
            }
        }
    });
</script>

<style>
    .rounded-4 {
        border-radius: 1rem !important;
    }
    .bg-success-subtle {
        background-color: rgba(40, 167, 69, 0.1) !important;
    }
    .bg-danger-subtle {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }
    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
    .bg-secondary-subtle {
        background-color: rgba(108, 117, 125, 0.1) !important;
    }
    .table > :not(caption) > * > * {
        padding: 1rem 0.75rem;
        vertical-align: middle;
    }
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 1rem 2rem rgba(0,0,0,.1) !important;
    }
</style>
@endsection