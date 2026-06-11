@extends(
Auth::user()->role=='manager'
? 'layouts.manager'
: 'layouts.app'
)

@section('title', 'Riwayat Perubahan Data')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-history me-2 text-info"></i> Riwayat Perubahan Data
                </h5>
                <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Informasi Record -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Informasi Record:</strong><br>
                ID: {{ $produksiCutting->id }} | 
                Tanggal: {{ \Carbon\Carbon::parse($produksiCutting->tanggal)->format('d/m/Y') }} | 
                Line Cutting: {{ $produksiCutting->line_cutting }}
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Waktu</th>
                            <th width="15%">User</th>
                            <th width="10%">Aksi</th>
                            <th width="35%">Perubahan</th>
                            <th width="15%">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $index => $activity)
                        <tr>
                            <td>{{ $activities->firstItem() + $index }}</td>
                            <td>{{ \Carbon\Carbon::parse($activity->created_at)->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $activity->user->name ?? 'Unknown' }}</td>
                            <td>
                                @if($activity->action == 'CREATE')
                                    <span class="badge bg-success">TAMBAH</span>
                                @elseif($activity->action == 'UPDATE')
                                    <span class="badge bg-warning">EDIT</span>
                                @elseif($activity->action == 'DELETE')
                                    <span class="badge bg-danger">HAPUS</span>
                                @endif
                            </td>
                            <td>
                                @if($activity->action == 'UPDATE')
                                    @php
                                        $old = json_decode($activity->old_data, true);
                                        $new = json_decode($activity->new_data, true);
                                        $changes = [];
                                        if($old && $new) {
                                            foreach($new as $key => $value) {
                                                if(isset($old[$key]) && $old[$key] != $value && !in_array($key, ['id', 'user_id', 'created_at', 'updated_at'])) {
                                                    $changes[] = "<strong>{$key}</strong>: {$old[$key]} → {$value}";
                                                }
                                            }
                                        }
                                    @endphp
                                    @if(!empty($changes))
                                        {!! implode('<br>', $changes) !!}
                                    @else
                                        Tidak ada perubahan signifikan
                                    @endif
                                @elseif($activity->action == 'CREATE')
                                    Data baru ditambahkan
                                @elseif($activity->action == 'DELETE')
                                    Data dihapus
                                @endif
                            </td>
                            <td>{{ $activity->ip_address ?? '-' }}</td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-database fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">Belum ada riwayat perubahan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
</div>
@endsection