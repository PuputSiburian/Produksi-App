@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 border-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="fw-bold mb-0">
                        <i class="fas fa-users me-2 text-primary"></i> Kelola Data User
                    </h4>
                    <p class="text-muted small mb-0 mt-1">Mengelola akun Admin, Manager, dan Operator</p>
                </div>
                <a href="{{ route('users.create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-plus me-1"></i> Tambah User
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Alert Messages -->
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

            <!-- Filter Form -->
            <form method="GET" action="{{ route('users.index') }}" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-control">
                        <option value="semua" {{ request('role') == 'semua' ? 'selected' : '' }}>Semua Role</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="operator" {{ request('role') == 'operator' ? 'selected' : '' }}>Operator</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-sync-alt me-1"></i> Reset
                    </a>
                </div>
            </form>

            <!-- Tabel User -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Terdaftar</th>
                            <th width="25%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                        <tr>
                            <td>{{ $users->firstItem() + $index }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role == 'admin')
                                    <span class="badge bg-primary rounded-pill px-3 py-2">
                                        <i class="fas fa-user-shield me-1"></i> Admin
                                    </span>
                                @elseif($user->role == 'manager')
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                        <i class="fas fa-chart-line me-1"></i> Manager
                                    </span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">
                                        <i class="fas fa-user me-1"></i> Operator
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($user->id == auth()->id())
                                    <span class="badge bg-success">Aktif (Anda)</span>
                                @else
                                    <span class="badge bg-secondary">Aktif</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}点
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <!-- EDIT -->
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-warning rounded-pill">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    
                                    <!-- HAPUS (TIDAK untuk diri sendiri) -->
                                    @if($user->id != auth()->id())
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                    @endif
                                    
                                    <!-- RESET PASSWORD (BISA untuk semua user, Admin bisa reset password sendiri) -->
                                    <a href="{{ route('users.reset-password', $user->id) }}" class="btn btn-sm btn-outline-info rounded-pill" onclick="return confirm('Yakin ingin mereset password user {{ $user->name }}? Password baru: password123')">
                                        <i class="fas fa-key"></i> Reset PW
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                    <p>Belum ada data user</p>
                                    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">Tambah User</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection