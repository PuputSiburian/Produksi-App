<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Getronics Batam - Sistem Informasi Manajemen Produksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #eef2f5;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, #0f172a, #0b1120);
            padding: 20px;
            overflow-y: auto;
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 5px 0 20px rgba(0, 0, 0, 0.2);
        }

        /* Custom Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #1e293b;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #FFD700;
            border-radius: 10px;
        }

        /* LOGO */
        .logo {
            font-size: 38px;
            font-weight: 900;
            margin-bottom: 25px;
            text-align: center;
            line-height: 1;
        }

        .g-logo {
            color: #FFD700;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, .4);
        }

        .text-logo {
            color: #dc2626;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, .4);
            margin-left: -3px;
        }

        /* USER CARD */
        .user-card {
            text-align: center;
            margin-bottom: 25px;
        }

        .user-card img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #FFD700;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .user-card h5 {
            margin-top: 12px;
            margin-bottom: 5px;
            font-size: 16px;
            color: #FFD700;
        }

        .user-card small {
            font-size: 12px;
            opacity: 0.7;
        }

        hr {
            border-color: rgba(255, 255, 255, 0.1);
            margin: 15px 0;
        }

        /* MENU */
        .sidebar a, .sidebar button.menu-item {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #e2e8f0;
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
            width: 100%;
            background: none;
            border: none;
            cursor: pointer;
        }

        .sidebar a i, .sidebar button.menu-item i {
            width: 24px;
            font-size: 16px;
            margin-right: 12px;
            text-align: center;
        }

        .sidebar a:hover, .sidebar button.menu-item:hover {
            background: rgba(255, 215, 0, 0.2);
            color: #FFD700;
            transform: translateX(5px);
        }

        .sidebar a.active, .sidebar button.menu-item.active {
            background: #FFD700;
            color: #0f172a;
        }

        /* CONTENT */
        .content {
            margin-left: 280px;
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* TOPBAR */
        .topbar {
            background: linear-gradient(135deg, #ffffff, #f8f9ff);
            padding: 15px 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border-left: 5px solid #FFD700;
        }

        .topbar h4 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            background: linear-gradient(45deg, #1a237e, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .topbar small {
            font-size: 12px;
            color: #6c757d;
        }

        .user-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #FFD700;
        }

        .dropdown-toggle::after {
            display: none;
        }

        .btn-dark {
            background: linear-gradient(45deg, #1e293b, #0f172a);
            border: none;
            padding: 8px 16px;
            border-radius: 30px;
        }

        .btn-dark:hover {
            background: linear-gradient(45deg, #0f172a, #1e293b);
            transform: translateY(-2px);
        }

        /* Alert */
        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        /* CARD */
        .card {
            border-radius: 16px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #f8f9ff, #ffffff) !important;
            border-bottom: 2px solid #e0e0e0;
        }

        /* TABLE */
        .table {
            border-radius: 12px;
            overflow: hidden;
        }

        .table thead th {
            background: linear-gradient(135deg, #1a237e, #283593);
            color: white;
            border: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 250px;
                transform: translateX(-100%);
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .content {
                margin-left: 0;
            }
            
            .topbar {
                padding: 12px 15px;
            }
        }

        .menu-item {
            display: block;
            padding: 12px 18px;
            margin: 8px 0;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            transition: 0.3s;
        }

        .menu-item:hover {
            background: rgba(255, 215, 0, 0.2);
            padding-left: 24px;
            color: #ffd700;
        }

        .logout-btn {
            width: 100%;
            border: none;
            background: none;
            text-align: left;
        }
        
        /* Pagination */
        .pagination .page-link {
            border-radius: 10px;
            margin: 0 3px;
            color: #1a237e;
        }
        
        .pagination .active .page-link {
            background: linear-gradient(45deg, #1a237e, #764ba2);
            border-color: #1a237e;
            color: white;
        }
    </style>
</head>

<body>
    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <span class="g-logo">G</span>
            <span class="text-logo">etronics</span>
        </div>
        <hr>

        <div class="user-card">
            @if(Auth::user()->foto)
                <img src="{{ asset('storage/'.Auth::user()->foto) }}" alt="Foto Profile">
            @else
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Default Avatar">
            @endif
            <h5>{{ Auth::user()->name }}</h5>
            <small>{{ Auth::user()->role }}</small>
        </div>
        <hr>

        <!-- MENU UNTUK ROLE ADMIN -->
        @if(auth()->user()->role == 'admin')
            <a href="{{ route('dashboard') }}" class="menu-item">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="{{ route('produksi-cutting.index') }}" class="menu-item">
                <i class="fas fa-cut"></i> Produksi Cutting
            </a>
            <a href="{{ route('produksi-crimping.index') }}" class="menu-item">
                <i class="fas fa-cogs"></i> Produksi Crimping
            </a>
            <a href="{{ route('produksi-line.index') }}" class="menu-item">
                <i class="fas fa-industry"></i> Produksi Line
            </a>
            <a href="{{ route('mesin.index') }}" class="menu-item">
                <i class="fas fa-microchip"></i> Mesin
            </a>
            <a href="{{ route('produk.index') }}" class="menu-item">
                <i class="fas fa-boxes"></i> Kelola Produk
            </a>
            <!-- MENU KELOLA USER (BARU) -->
            <a href="{{ route('users.index') }}" class="menu-item">
                <i class="fas fa-users"></i> Kelola User
            </a>
        @endif

        <!-- MENU UNTUK ROLE MANAGER -->
        @if(auth()->user()->role == 'manager')
            <a href="{{ route('manager.dashboard') }}" class="menu-item">
                <i class="fas fa-chart-line"></i> Dashboard Manager
            </a>
            <a href="{{ route('produksi-cutting.index') }}" class="menu-item">
                <i class="fas fa-file-alt"></i> Laporan Cutting
            </a>
            <a href="{{ route('produksi-crimping.index') }}" class="menu-item">
                <i class="fas fa-file-alt"></i> Laporan Crimping
            </a>
            <a href="{{ route('produksi-line.index') }}" class="menu-item">
                <i class="fas fa-file-alt"></i> Laporan Line
            </a>
            <a href="{{ route('mesin.index') }}" class="menu-item">
                <i class="fas fa-microchip"></i> Data Mesin
            </a>
        @endif

        <!-- MENU UNTUK ROLE OPERATOR -->
        @if(auth()->user()->role == 'operator')
            <a href="{{ route('dashboard') }}" class="menu-item">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="{{ route('produksi-cutting.index') }}" class="menu-item">
                <i class="fas fa-cut"></i> Produksi Cutting
            </a>
            <a href="{{ route('produksi-crimping.index') }}" class="menu-item">
                <i class="fas fa-cogs"></i> Produksi Crimping
            </a>
            <a href="{{ route('produksi-line.index') }}" class="menu-item">
                <i class="fas fa-industry"></i> Produksi Line
            </a>
        @endif

        <hr>
        <a href="{{ route('profile.edit') }}" class="menu-item">
            <i class="fas fa-user-circle"></i> Profile
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="menu-item logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>

    <!-- CONTENT -->
    <div class="content">
        <div class="topbar">
            <div>
                <h4 class="mb-0">Sistem Informasi Manajemen Produksi</h4>
                <small>PT Getronics Batam</small>
            </div>
            <div class="dropdown">
                <button class="btn btn-dark dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                    @if(Auth::user()->foto)
                        <img src="{{ asset('storage/'.Auth::user()->foto) }}" class="user-img me-2">
                    @else
                        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="user-img me-2">
                    @endif
                    {{ Auth::user()->name }}
                    <i class="fas fa-chevron-down ms-2" style="font-size: 12px;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                    <li>
                        <a class="dropdown-item" href="{{ route('dashboard') }}">
                            <i class="fas fa-chart-line me-2"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user-circle me-2"></i> My Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <!-- ALERT MESSAGES -->
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

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Yield Content -->
        @yield('content')
    </div>

    <!-- Mobile Menu Toggle Button -->
    <button class="btn btn-dark position-fixed bottom-0 start-0 m-3 rounded-circle d-md-none" id="menuToggle" style="z-index: 1100; width: 50px; height: 50px; background: linear-gradient(45deg, #FFD700, #ff8c00); border: none;">
        <i class="fas fa-bars"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a8e9a8c9f6.js" crossorigin="anonymous"></script>
    
    <script>
        // Mobile menu toggle
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        
        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('open');
            });
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });
        
        // Active menu highlight
        document.querySelectorAll('.sidebar a').forEach(link => {
            if (link.href === window.location.href) {
                link.classList.add('active');
            }
        });
    </script>
</body>

</html>