@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold">
                    <i class="fas fa-history me-2 text-primary"></i> Riwayat Perubahan Data
                </h4>
                <a href="{{ route('produksi-cutting.index') }}" class="btn btn-secondary btn-sm rounded-pill">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-3">
                <strong>Informasi Record:</strong><br>
                ID: {{ $produksiCutting->id }} | 
                Tanggal: {{ $produksiCutting->tanggal }} | 
                Line Cutting: {{ $produksiCutting->line_cutting }} |
                <strong>Leader: {{ $produksiCutting->leader_name ?? '-' }}</strong>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>IP Address</th>
                            <th>Aksi</th>
                            <th>Perubahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                        <tr>
                            <td>{{ $activity->created_at->format('d-m-Y H:i:s') }}</td>
                            <td>{{ $activity->user_name }}</td>
                            <td>{{ $activity->ip_address ?? '-' }}</td>
                            <td>
                                @if($activity->action == 'UPDATE')
                                    <span class="badge bg-warning">UPDATE</span>
                                @elseif($activity->action == 'DELETE')
                                    <span class="badge bg-danger">DELETE</span>
                                @else
                                    <span class="badge bg-info">{{ $activity->action }}</span>
                                @endif
                            </td>
                            <td>
                                @if($activity->action == 'UPDATE' && $activity->old_data && $activity->new_data)
                                    @php
                                        $old = json_decode($activity->old_data, true);
                                        $new = json_decode($activity->new_data, true);
                                        $changes = [];
                                        if(is_array($old) && is_array($new)) {
                                            foreach($new as $key => $value) {
                                                if(isset($old[$key]) && $old[$key] != $value) {
                                                    $changes[] = [
                                                        'field' => ucfirst(str_replace('_', ' ', $key)),
                                                        'old' => $old[$key] ?? '-',
                                                        'new' => $value ?? '-'
                                                    ];
                                                }
                                            }
                                        }
                                    @endphp
                                    @if(count($changes) > 0)
                                        <table class="table table-sm table-borderless mb-0">
                                            @foreach($changes as $change)
                                                <tr>
                                                    <td width="30%"><strong>{{ $change['field'] }}</strong></td>
                                                    <td width="35%"><span class="text-danger">{{ $change['old'] }}</span></td>
                                                    <td width="5%" class="text-center">→</td>
                                                    <td width="30%"><span class="text-success">{{ $change['new'] }}</span></td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    @else
                                        <span class="text-muted">Tidak ada perubahan signifikan</span>
                                    @endif
                                @elseif($activity->action == 'CREATE')
                                    @php
                                        $new = json_decode($activity->new_data, true);
                                    @endphp
                                    <span class="text-success">Data baru ditambahkan</span>
                                    @if($new)
                                        <br><small class="text-muted">
                                            <strong>Leader:</strong> {{ $new['leader_name'] ?? '-' }} |
                                            <strong>Operator:</strong> {{ $new['nama_operator'] ?? '-' }} |
                                            <strong>Produk:</strong> {{ $new['produk'] ?? '-' }}
                                        </small>
                                    @endif
                                @elseif($activity->action == 'DELETE')
                                    @php
                                        $old = json_decode($activity->old_data, true);
                                    @endphp
                                    <span class="text-danger">Data dihapus</span>
                                    @if($old)
                                        <br><small class="text-muted">
                                            <strong>Leader:</strong> {{ $old['leader_name'] ?? '-' }} |
                                            <strong>Operator:</strong> {{ $old['nama_operator'] ?? '-' }} |
                                            <strong>Produk:</strong> {{ $old['produk'] ?? '-' }}
                                        </small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-database me-2"></i> Belum ada riwayat perubahan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
</div>
@endsection