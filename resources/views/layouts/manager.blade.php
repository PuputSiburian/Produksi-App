<!DOCTYPE html>
<html>
<head>
    <title>Manager Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #eef2f7;
        }
        .sidebar {
            position: fixed;
            width: 250px;
            height: 100vh;
            background: #0f172a;
            padding: 20px;
            color: white;
            z-index: 1000;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
        }
        .g {
            color: gold;
        }
        .ronics {
            color: red;
        }
        .menu {
            display: block;
            padding: 12px;
            margin: 10px 0;
            color: white;
            text-decoration: none;
            border-radius: 10px;
        }
        .menu:hover {
            background: #1e293b;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
            min-height: 100vh;
        }
        /* TOPBAR STYLE */
        .topbar {
            background: white;
            padding: 15px 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .topbar h4 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
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
        }
        .btn-dark {
            background: #1e293b;
            border: none;
            padding: 8px 16px;
            border-radius: 30px;
        }
        .dropdown-toggle::after {
            display: none;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <span class="g">G</span><span class="ronics">etronics</span>
        </div>
        <hr>
        <h5>{{ auth()->user()->name }}</h5>
        <small>Manager</small>
        <hr>

        <a href="{{ route('manager.dashboard') }}" class="menu">📊 Dashboard</a>
        <a href="{{ route('produksi-cutting.index') }}" class="menu">📄 Laporan Cutting</a>
        <a href="{{ route('produksi-crimping.index') }}" class="menu">📄 Laporan Crimping</a>
        <a href="{{ route('produksi-line.index') }}" class="menu">📄 Laporan Line</a>
        <a href="{{ route('mesin.index') }}" class="menu">🖥️ Data Mesin</a>
        <a href="{{ route('profile.edit') }}" class="menu">👤 Profile</a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="menu border-0 bg-transparent w-100 text-start">🚪 Logout</button>
        </form>
    </div>

    <div class="content">
        <!-- TOPBAR -->
        <div class="topbar">
            <div>
                <h4 class="mb-0">Sistem Informasi Manajemen Produksi</h4>
                <small>PT Getronics Batam</small>
            </div>
            <div class="dropdown">
                <button class="btn btn-dark dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="user-img me-2">
                    {{ auth()->user()->name }}
                    <i class="fas fa-chevron-down ms-2" style="font-size: 12px;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
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

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a8e9a8c9f6.js" crossorigin="anonymous"></script>
</body>
</html>