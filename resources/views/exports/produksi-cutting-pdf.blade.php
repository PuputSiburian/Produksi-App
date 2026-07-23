@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-file-export me-2"></i> Export Laporan - Cutting
                    </h4>
                </div>
                <div class="card-body">
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <!-- ============================================ -->
                    <!-- TAB PILIHAN EXPORT -->
                    <!-- ============================================ -->
                    <ul class="nav nav-tabs nav-fill mb-4" id="exportTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="harian-tab" data-bs-toggle="tab" data-bs-target="#harian" type="button" role="tab">
                                <i class="fas fa-calendar-day me-2"></i> Export Harian
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="mingguan-tab" data-bs-toggle="tab" data-bs-target="#mingguan" type="button" role="tab">
                                <i class="fas fa-calendar-week me-2"></i> Export Mingguan
                            </button>
                        </li>
                    </ul>

                    <!-- ============================================ -->
                    <!-- TAB HARIAN -->
                    <!-- ============================================ -->
                    <div class="tab-content" id="exportTabContent">
                        
                        <!-- HARIAN -->
                        <div class="tab-pane fade show active" id="harian" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Pilih tanggal untuk mengekspor laporan harian.
                            </div>
                            
                            <form action="{{ route('produksi-cutting.export.harian') }}" method="GET" id="formHarian">
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label fw-bold">📅 Pilih Tanggal</label>
                                        <input type="date" name="tanggal" class="form-control" 
                                               value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-4 mb-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100" id="btnHarian">
                                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-database me-1"></i>
                                    Data tersedia: 
                                    <span class="badge bg-info">{{ $availableDates->count() }}</span> hari
                                </small>
                            </div>
                        </div>

                        <!-- MINGGUAN -->
                        <div class="tab-pane fade" id="mingguan" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Pilih tahun, bulan, dan minggu untuk mengekspor laporan mingguan.
                                <br>
                                <small class="text-muted">
                                    * Minggu yang tersedia berdasarkan data yang ada di database
                                </small>
                            </div>
                            
                            <form action="{{ route('produksi-cutting.export.mingguan') }}" method="GET" id="formMingguan">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">📅 Tahun</label>
                                        <select name="tahun" id="tahunMingguan" class="form-control" required>
                                            @if($availableYears->count() > 0)
                                                @foreach($availableYears as $year)
                                                    <option value="{{ $year }}" {{ $year == $tahun ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endforeach
                                            @else
                                                @for($i = date('Y'); $i >= 2020; $i--)
                                                    <option value="{{ $i }}" {{ $i == $tahun ? 'selected' : '' }}>
                                                        {{ $i }}
                                                    </option>
                                                @endfor
                                            @endif
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">📆 Bulan</label>
                                        <select name="bulan" id="bulanMingguan" class="form-control" required>
                                            @foreach(['Januari','Februari','Maret','April','Mei','Juni',
                                                      'Juli','Agustus','September','Oktober','November','Desember'] as $key => $bulanName)
                                                <option value="{{ $key+1 }}" {{ ($key+1) == $bulan ? 'selected' : '' }}>
                                                    {{ $bulanName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">📊 Pilih Minggu</label>
                                        <select name="week_label" id="weekSelect" class="form-control" required>
                                            <option value="">-- Pilih Minggu --</option>
                                            @foreach($weeks as $index => $week)
                                                @php
                                                    $start = \Carbon\Carbon::parse($week['start']);
                                                    $end = \Carbon\Carbon::parse($week['end']);
                                                    $label = 'Minggu ' . ($index + 1) . ' (' . $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y') . ')';
                                                @endphp
                                                <option value="{{ $label }}" 
                                                    data-start="{{ $start->format('Y-m-d') }}" 
                                                    data-end="{{ $end->format('Y-m-d') }}">
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="start_date" id="start_date">
                                        <input type="hidden" name="end_date" id="end_date">
                                        <small class="text-muted" id="weekInfo">
                                            @if(count($weeks) > 0)
                                                <i class="fas fa-check-circle text-success"></i> 
                                                {{ count($weeks) }} minggu tersedia
                                            @else
                                                <i class="fas fa-exclamation-circle text-warning"></i> 
                                                Tidak ada data untuk periode ini
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-2">
                                    <button type="submit" class="btn btn-primary" id="btnMingguan">
                                        <i class="fas fa-file-pdf me-1"></i> Export PDF Mingguan
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                    </div>

                    <!-- TOMBOL KEMBALI -->
                    <div class="mt-4 text-center">
                        <a href="{{ route('produksi-cutting.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // =========================================================
        // MINGGUAN - SET DATA TANGGAL
        // =========================================================
        const weekSelect = document.getElementById('weekSelect');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const formMingguan = document.getElementById('formMingguan');
        const btnMingguan = document.getElementById('btnMingguan');
        const tahunMingguan = document.getElementById('tahunMingguan');
        const bulanMingguan = document.getElementById('bulanMingguan');
        const weekInfo = document.getElementById('weekInfo');
        
        // Set tanggal saat pilih minggu
        if (weekSelect) {
            weekSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const startDate = selectedOption.getAttribute('data-start') || '';
                const endDate = selectedOption.getAttribute('data-end') || '';
                
                startDateInput.value = startDate;
                endDateInput.value = endDate;
            });
        }
        
        // Reload saat tahun atau bulan berubah
        function reloadWeeks() {
            const tahun = tahunMingguan.value;
            const bulan = bulanMingguan.value;
            window.location.href = '{{ route("produksi-cutting.export.page") }}?tahun=' + tahun + '&bulan=' + bulan;
        }
        
        if (tahunMingguan) {
            tahunMingguan.addEventListener('change', reloadWeeks);
        }
        if (bulanMingguan) {
            bulanMingguan.addEventListener('change', reloadWeeks);
        }
        
        // =========================================================
        // LOADING SAAT SUBMIT
        // =========================================================
        // Form Harian
        const formHarian = document.getElementById('formHarian');
        if (formHarian) {
            formHarian.addEventListener('submit', function(e) {
                const btn = document.getElementById('btnHarian');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';
            });
        }
        
        // Form Mingguan
        if (formMingguan) {
            formMingguan.addEventListener('submit', function(e) {
                if (!startDateInput.value || !endDateInput.value) {
                    e.preventDefault();
                    alert('Silakan pilih minggu terlebih dahulu!');
                    return;
                }
                
                btnMingguan.disabled = true;
                btnMingguan.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';
            });
        }
        
        // =========================================================
        // SET DEFAULT TANGGAL UNTUK HARIAN
        // =========================================================
        const dateInput = document.querySelector('input[name="tanggal"]');
        if (dateInput) {
            dateInput.max = new Date().toISOString().split('T')[0];
        }
    });
</script>
@endsection