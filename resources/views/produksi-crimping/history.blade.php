@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>
                        <i class="fas fa-history me-2"></i> 
                        Riwayat Perubahan - Produksi Crimping
                    </h4>
                    <small class="text-muted">
                        Data ID: {{ $produksiCrimping->id }} | 
                        Tanggal: {{ \Carbon\Carbon::parse($produksiCrimping->tanggal)->format('d-m-Y') }} |
                        Operator: {{ $produksiCrimping->nama_operator }}
                    </small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead style="background: linear-gradient(135deg, #1a237e, #283593); color: white;">
                                <tr>
                                    <th>No</th>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Aksi</th>
                                    <th>Data Lama</th>
                                    <th>Data Baru</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities as $index => $activity)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($activity->created_at)->format('d-m-Y H:i:s') }}</td>
                                    <td>
                                        <strong>{{ $activity->user_name }}</strong>
                                        @if($activity->user)
                                            <br><small class="text-muted">{{ $activity->user->email }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badgeColor = match($activity->action) {
                                                'CREATE' => 'success',
                                                'UPDATE' => 'warning',
                                                'DELETE' => 'danger',
                                                default => 'secondary'
                                            };
                                            $icon = match($activity->action) {
                                                'CREATE' => 'fa-plus-circle',
                                                'UPDATE' => 'fa-edit',
                                                'DELETE' => 'fa-trash-alt',
                                                default => 'fa-info-circle'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }}">
                                            <i class="fas {{ $icon }}"></i> {{ $activity->action }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($activity->old_data)
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#oldData{{ $index }}">
                                                <i class="fas fa-eye"></i> Lihat
                                            </button>
                                            <div class="collapse mt-2" id="oldData{{ $index }}">
                                                <pre class="bg-light p-2 rounded" style="font-size: 11px; max-height: 200px; overflow-y: auto;">
                                                    {{ json_encode(json_decode($activity->old_data), JSON_PRETTY_PRINT) }}
                                                </pre>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($activity->new_data)
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#newData{{ $index }}">
                                                <i class="fas fa-eye"></i> Lihat
                                            </button>
                                            <div class="collapse mt-2" id="newData{{ $index }}">
                                                <pre class="bg-light p-2 rounded" style="font-size: 11px; max-height: 200px; overflow-y: auto;">
                                                    {{ json_encode(json_decode($activity->new_data), JSON_PRETTY_PRINT) }}
                                                </pre>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td><code>{{ $activity->ip_address ?? '-' }}</code></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-info-circle me-2"></i> 
                                        Belum ada riwayat perubahan untuk data ini
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        {{ $activities->links() }}
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('produksi-crimping.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection