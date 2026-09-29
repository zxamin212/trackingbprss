<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Login — {{ config('app.name', 'Tracking Berkas Kredit') }}</title>

    <link rel="icon" href="{{ asset('assets/img/logo-bpr2.png') }}" type="image/x-icon">

    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands"],
                urls: ["{{ asset('assets/css/fonts.min.css') }}"],
            },
        });
    </script>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f4f9;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }
        .login-logo {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: #eef1f7;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem auto;
        }
        .login-logo i {
            font-size: 40px;
            color: #5e72e4;
        }
    </style>
</head>
<body>

    <div class="card card-round login-card shadow-sm">
        <div class="card-body">

            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-shield-alt fa-lg text-primary me-2"></i>
                <div>
                    <h6 class="fw-bold mb-0">Sahabat Sejati</h6>
                    <p class="text-muted mb-0" style="font-size: 13px;">Login ke sistem tracking berkas kredit.</p>
                </div>
            </div>

            <div class="login-logo">
                <img src="{{ asset('assets/img/logo-bpr2.png') }}" alt="Logo" style="width:96px;height:96px;border-radius:50%;display:block;margin:0 auto 1.25rem auto;">
            </div>

            <h5 class="text-center fw-bold mb-4">Login</h5>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Alamat Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label for="remember" class="form-check-label">Ingat saya</label>
                </div>

                <button type="submit" class="btn btn-primary btn-round w-100">
                    <i class="fas fa-sign-in-alt me-1"></i> Masuk
                </button>

                @if (Route::has('password.request'))
                    <div class="text-center mt-3">
                        <a href="{{ route('password.request') }}" class="text-decoration-none" style="font-size: 13px;">
                            Lupa password?
                        </a>
                    </div>
                @endif
            </form>

        </div>
    </div>

    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>

</body>
</html>