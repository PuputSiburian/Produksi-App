<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | PT Getronics Batam</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 (free) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        /* Main card container */
        .login-container {
            max-width: 1200px;
            width: 100%;
            background: #ffffff;
            border-radius: 2rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            display: flex;
            flex-wrap: wrap;
            transition: transform 0.3s ease;
        }

        /* LEFT PANEL - Branding & Illustration */
        .brand-panel {
            flex: 1.2;
            background: linear-gradient(145deg, #0f172a, #0b1120);
            padding: 2.5rem;
            color: white;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-panel::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" opacity="0.08"><path fill="%23FFD700" d="M45.3,-67.9C59.4,-59.5,72.5,-46.2,78.2,-30.1C83.9,-14,82.2,5.1,75.2,21.7C68.2,38.3,55.9,52.5,41.6,62C27.3,71.5,11.1,76.4,-5.5,78.8C-22.1,81.2,-39.2,81,-52.4,72.8C-65.6,64.6,-75,48.6,-79.1,31.4C-83.2,14.2,-82.1,-4.1,-75.4,-19.8C-68.7,-35.5,-56.3,-48.5,-42.6,-57.6C-28.9,-66.7,-14.4,-71.9,1.1,-73.5C16.6,-75.1,33.2,-73.1,45.3,-67.9Z" transform="translate(100 100)" /></svg>');
            background-repeat: no-repeat;
            background-position: bottom right;
            background-size: 280px;
            pointer-events: none;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 2rem;
            z-index: 2;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: #FFD700;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }

        .logo-text h2 {
            font-weight: 800;
            margin: 0;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        .logo-text span {
            font-size: 14px;
            opacity: 0.8;
            font-weight: 400;
        }

        .illustration-text {
            margin-top: 2rem;
            z-index: 2;
        }

        .illustration-text h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .illustration-text p {
            opacity: 0.8;
            font-size: 0.95rem;
            max-width: 85%;
        }

        .feature-list {
            margin-top: 2rem;
            list-style: none;
            padding-left: 0;
        }

        .feature-list li {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
        }

        .feature-list i {
            color: #FFD700;
            font-size: 1.2rem;
            width: 24px;
        }

        .brand-footer {
            font-size: 0.75rem;
            opacity: 0.5;
            margin-top: 2rem;
            z-index: 2;
        }

        /* RIGHT PANEL - Form */
        .form-panel {
            flex: 1;
            padding: 2.5rem;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 2rem;
        }

        .form-header h3 {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #5b677b;
            font-size: 0.9rem;
        }

        .input-group-custom {
            margin-bottom: 1.5rem;
        }

        .input-group-custom label {
            font-weight: 500;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
            color: #1e293b;
            display: block;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
        }

        .input-icon input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            background: #f8fafc;
        }

        .input-icon input:focus {
            border-color: #FFD700;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
            background: white;
        }

        .options-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.8rem;
            font-size: 0.85rem;
        }

        .checkbox-custom {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #334155;
        }

        .checkbox-custom input {
            width: 16px;
            height: 16px;
            accent-color: #FFD700;
            cursor: pointer;
        }

        .forgot-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
            transition: 0.2s;
        }

        .forgot-link:hover {
            text-decoration: underline;
            color: #1e40af;
        }

        .btn-login {
            background: #0f172a;
            border: none;
            padding: 12px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 1rem;
            color: white;
            width: 100%;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
        }

        .btn-login:hover {
            background: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.2);
        }

        .demo-note {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.7rem;
            color: #94a3b8;
            border-top: 1px solid #eef2ff;
            padding-top: 1.5rem;
        }

        .alert-custom {
            border-radius: 16px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            border-left: 5px solid #FFD700;
        }

        /* Responsive */
        @media (max-width: 800px) {
            .brand-panel {
                display: none;
            }
            .form-panel {
                flex: 1;
                padding: 2rem;
            }
            .login-container {
                max-width: 500px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- LEFT BRAND SIDE -->
    <div class="brand-panel">
        <div>
            <div class="logo-area">
                <div class="logo-icon">G</div>
                <div class="logo-text">
                    <h2>Getronics</h2>
                    <span>Batam · Industry 4.0</span>
                </div>
            </div>
            <div class="illustration-text">
                <h1>Manufacturing Intelligence</h1>
                <p>Real-time production monitoring & quality management system for cable assembly.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-chart-line"></i> Live production dashboard</li>
                    <li><i class="fas fa-cut"></i> Cutting & Crimping traceability</li>
                    <li><i class="fas fa-clipboard-list"></i> Line performance analytics</li>
                </ul>
            </div>
        </div>
        <div class="brand-footer">
            © 2025 PT Getronics Batam · Enterprise Edition
        </div>
    </div>

    <!-- RIGHT FORM SIDE -->
    <div class="form-panel">
        <div class="form-header">
            <h3>Welcome back</h3>
            <p>Sign in to your account to continue production management</p>
        </div>

        <!-- Session Status (success like password reset) -->
        @if(session('status'))
            <div class="alert alert-success alert-custom d-flex align-items-center" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <div>{{ session('status') }}</div>
            </div>
        @endif

        <!-- Error messages from validation -->
        @if($errors->any())
            <div class="alert alert-danger alert-custom" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="input-group-custom">
                <label for="email">Email address</label>
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="john.doe@getronics.com">
                </div>
            </div>

            <!-- Password -->
            <div class="input-group-custom">
                <label for="password">Password</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                </div>
            </div>

            <!-- Remember me & Forgot -->
            <div class="options-row">
                <label class="checkbox-custom">
                    <input type="checkbox" name="remember" id="remember_me">
                    <span>Remember me</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                @endif
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-login">
                <i class="fas fa-arrow-right-to-bracket"></i> Sign In
            </button>

            <div class="demo-note">
                <i class="fas fa-shield-alt"></i> Secure login · SSL encrypted
            </div>
        </form>

        <!-- Additional demo hint for production (optional for test) -->
        <div class="text-center mt-3 small text-muted">
            ⚡ PT Getronics Batam - Production Excellence
        </div>
    </div>
</div>

<!-- Optional bootstrap bundle for any interactive component -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>