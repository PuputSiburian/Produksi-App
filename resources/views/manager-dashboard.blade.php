@extends('layouts.manager')

@section('content')
<div class="container-fluid px-4">
    <!-- Welcome Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h4 class="mb-0">Selamat datang, {{ Auth::user()->name }}</h4>
                    <small>Dashboard Manager - PT Getronics Batam</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Cutting</h6>
                    <h2 class="fw-bold text-success">{{ $totalCutting ?? 0 }}</h2>
                    <small class="text-muted">Record Produksi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-danger bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Crimping</h6>
                    <h2 class="fw-bold text-danger">{{ $totalCrimping ?? 0 }}</h2>
                    <small class="text-muted">Record Produksi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Line</h6>
                    <h2 class="fw-bold text-primary">{{ $totalLine ?? 0 }}</h2>
                    <small class="text-muted">Record Produksi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="text-muted">Produksi Hari Ini</h6>
                    <h2 class="fw-bold text-warning">{{ $produksiHariIni['total'] ?? 0 }}</h2>
                    <small class="text-muted">Record</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Cards - Reject (TAMBAHAN) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 bg-danger bg-opacity-10">
                <div class="card-body text-center py-2">
                    <h6 class="text-muted small mb-1">Total Reject</h6>
                    <h3 class="fw-bold text-danger mb-0">{{ ($totalRejectCutting ?? 0) + ($totalRejectCrimping ?? 0) + ($totalRejectLine ?? 0) }}</h3>
                    <small class="text-muted">Unit</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 bg-danger bg-opacity-10">
                <div class="card-body text-center py-2">
                    <h6 class="text-muted small mb-1">Reject Rate</h6>
                    <h3 class="fw-bold text-danger mb-0">{{ $rejectRate ?? 0 }}%</h3>
                    <small class="text-muted">Dari total QTY</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 bg-warning bg-opacity-10">
                <div class="card-body text-center py-2">
                    <h6 class="text-muted small mb-1">Reject Cutting</h6>
                    <h3 class="fw-bold text-warning mb-0">{{ number_format($totalRejectCutting ?? 0) }}</h3>
                    <small class="text-muted">Unit</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 bg-warning bg-opacity-10">
                <div class="card-body text-center py-2">
                    <h6 class="text-muted small mb-1">Reject Crimping</h6>
                    <h3 class="fw-bold text-warning mb-0">{{ number_format($totalRejectCrimping ?? 0) }}</h3>
                    <small class="text-muted">Unit</small>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6 offset-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-warning bg-opacity-10">
                <div class="card-body text-center py-2">
                    <h6 class="text-muted small mb-1">Reject Line</h6>
                    <h3 class="fw-bold text-warning mb-0">{{ number_format($totalRejectLine ?? 0) }}</h3>
                    <small class="text-muted">Unit</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Section - Diperkecil -->
    <div class="row g-4 mb-4">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-bold">📊 Grafik Perbandingan Produksi</h6>
                </div>
                <div class="card-body">
                    <canvas id="produksiChart" height="150" style="max-height: 150px; width: 100%;"></canvas>
                </div>
                <div class="card-footer bg-white border-0 pt-0 pb-2 text-center">
                    <div class="row">
                        <div class="col-4">
                            <small class="text-success fw-bold">Cutting</small><br>
                            <small>{{ number_format($totalQtyCutting ?? 0) }} unit</small>
                        </div>
                        <div class="col-4">
                            <small class="text-danger fw-bold">Crimping</small><br>
                            <small>{{ number_format($totalQtyCrimping ?? 0) }} unit</small>
                        </div>
                        <div class="col-4">
                            <small class="text-primary fw-bold">Line</small><br>
                            <small>{{ number_format($totalQtyLine ?? 0) }} unit</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-bold">📊 Grafik Perbandingan Reject</h6>
                </div>
                <div class="card-body">
                    <canvas id="rejectChart" height="150" style="max-height: 150px; width: 100%;"></canvas>
                </div>
                <div class="card-footer bg-white border-0 pt-0 pb-2 text-center">
                    <div class="row">
                        <div class="col-4">
                            <small class="text-success fw-bold">Cutting</small><br>
                            <small>{{ number_format($totalRejectCutting ?? 0) }} unit</small>
                        </div>
                        <div class="col-4">
                            <small class="text-danger fw-bold">Crimping</small><br>
                            <small>{{ number_format($totalRejectCrimping ?? 0) }} unit</small>
                        </div>
                        <div class="col-4">
                            <small class="text-primary fw-bold">Line</small><br>
                            <small>{{ number_format($totalRejectLine ?? 0) }} unit</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Produksi -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-bold">📋 Ringkasan Produksi</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Departemen</th>
                                    <th class="text-end">Total Data</th>
                                    <th class="text-end">Total QTY</th>
                                    <th class="text-end">Target</th>
                                    <th class="text-end">Reject</th>
                                    <th class="text-end">Capaian</th>
                                    <th class="text-end">Reject Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Cutting
                                    <td class="text-end">{{ $totalCutting ?? 0 }}
                                    <td class="text-end">{{ number_format($totalQtyCutting ?? 0) }}
                                    <td class="text-end">{{ number_format($totalTargetCutting ?? 0) }}
                                    <td class="text-end text-danger">{{ number_format($totalRejectCutting ?? 0) }}
                                    <td class="text-end">{{ $persenCutting ?? 0 }}%
                                    <td class="text-end">{{ $totalQtyCutting > 0 ? round(($totalRejectCutting / $totalQtyCutting) * 100, 2) : 0 }}%
                                </tr>
                                <tr>
                                    <td>Crimping
                                    <td class="text-end">{{ $totalCrimping ?? 0 }}
                                    <td class="text-end">{{ number_format($totalQtyCrimping ?? 0) }}
                                    <td class="text-end">{{ number_format($totalTargetCrimping ?? 0) }}
                                    <td class="text-end text-danger">{{ number_format($totalRejectCrimping ?? 0) }}
                                    <td class="text-end">{{ $persenCrimping ?? 0 }}%
                                    <td class="text-end">{{ $totalQtyCrimping > 0 ? round(($totalRejectCrimping / $totalQtyCrimping) * 100, 2) : 0 }}%
                                </tr>
                                <tr>
                                    <td>Line
                                    <td class="text-end">{{ $totalLine ?? 0 }}
                                    <td class="text-end">{{ number_format($totalQtyLine ?? 0) }}
                                    <td class="text-end">{{ number_format($totalTargetLine ?? 0) }}
                                    <td class="text-end text-danger">{{ number_format($totalRejectLine ?? 0) }}
                                    <td class="text-end">{{ $persenLine ?? 0 }}%
                                    <td class="text-end">{{ $totalQtyLine > 0 ? round(($totalRejectLine / $totalQtyLine) * 100, 2) : 0 }}%
                                </tr>
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td><strong>TOTAL</strong>
                                    <td class="text-end">{{ ($totalCutting ?? 0) + ($totalCrimping ?? 0) + ($totalLine ?? 0) }}
                                    <td class="text-end">{{ number_format(($totalQtyCutting ?? 0) + ($totalQtyCrimping ?? 0) + ($totalQtyLine ?? 0)) }}
                                    <td class="text-end">{{ number_format(($totalTargetCutting ?? 0) + ($totalTargetCrimping ?? 0) + ($totalTargetLine ?? 0)) }}
                                    <td class="text-end text-danger">{{ number_format(($totalRejectCutting ?? 0) + ($totalRejectCrimping ?? 0) + ($totalRejectLine ?? 0)) }}
                                    <td class="text-end">-
                                    <td class="text-end">{{ $rejectRate ?? 0 }}%
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Mesin -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-bold">🖥️ Status Mesin</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2 col-6">
                            <div class="p-3 rounded-3 bg-secondary bg-opacity-10">
                                <h5 class="mb-0">{{ $mesinStats['total'] ?? 0 }}</h5>
                                <small class="text-muted">Total Mesin</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="p-3 rounded-3 bg-success bg-opacity-10">
                                <h5 class="mb-0 text-success">{{ $mesinStats['beroperasi'] ?? 0 }}</h5>
                                <small class="text-muted">Beroperasi</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="p-3 rounded-3 bg-warning bg-opacity-10">
                                <h5 class="mb-0 text-warning">{{ $mesinStats['perbaikan'] ?? 0 }}</h5>
                                <small class="text-muted">Perbaikan</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="p-3 rounded-3 bg-danger bg-opacity-10">
                                <h5 class="mb-0 text-danger">{{ $mesinStats['rusak'] ?? 0 }}</h5>
                                <small class="text-muted">Rusak</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="p-3 rounded-3 bg-info bg-opacity-10">
                                <h5 class="mb-0 text-info">{{ $mesinStats['maintenance'] ?? 0 }}</h5>
                                <small class="text-muted">Maintenance</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="p-3 rounded-3 bg-secondary bg-opacity-10">
                                <h5 class="mb-0">{{ $mesinStats['idle'] ?? 0 }}</h5>
                                <small class="text-muted">Idle</small>
                            </div>
                        </div>
                    </div>
                    
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
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Aktivitas Produksi Terbaru -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-bold">📋 Aktivitas Produksi Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Stasiun</th>
                                    <th>Operator</th>
                                    <th>Produk</th>
                                    <th class="text-end">QTY</th>
                                    <th class="text-end">Reject</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities ?? [] as $activity)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($activity->tanggal)->format('d-m-Y') }}
                                    <td>{{ $activity->stasiun }}
                                    <td>{{ $activity->operator }}
                                    <td>{{ $activity->produk }}
                                    <td class="text-end">{{ number_format($activity->qty) }}
                                    <td class="text-end">{{ number_format($activity->reject) }}
                                    <td>
                                        @if($activity->reject > 0)
                                            <span class="badge bg-danger">⚠️ Dengan Reject</span>
                                        @else
                                            <span class="badge bg-success">✅ Selesai</span>
                                        @endif
                                    
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada aktivitas produksi
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

  

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Grafik Produksi
    const ctx1 = document.getElementById('produksiChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Cutting', 'Crimping', 'Line'],
            datasets: [{
                label: 'Total Produksi (Unit)',
                data: [{{ $totalQtyCutting ?? 0 }}, {{ $totalQtyCrimping ?? 0 }}, {{ $totalQtyLine ?? 0 }}],
                backgroundColor: ['#28a745', '#dc3545', '#007bff'],
                borderRadius: 6,
                barPercentage: 0.65
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { 
                    position: 'top',
                    labels: { font: { size: 10 }, boxWidth: 10 }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: { font: { size: 9 } }
                },
                x: {
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });

    // Grafik Reject
    const ctx2 = document.getElementById('rejectChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Cutting', 'Crimping', 'Line'],
            datasets: [{
                label: 'Jumlah Reject (Unit)',
                data: [{{ $totalRejectCutting ?? 0 }}, {{ $totalRejectCrimping ?? 0 }}, {{ $totalRejectLine ?? 0 }}],
                backgroundColor: ['#28a745', '#dc3545', '#007bff'],
                borderRadius: 6,
                barPercentage: 0.65
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { 
                    position: 'top',
                    labels: { font: { size: 10 }, boxWidth: 10 }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: { font: { size: 9 }, stepSize: 1 }
                },
                x: {
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });
</script>
@endsection