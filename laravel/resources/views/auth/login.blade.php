<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - {{ config('app.name', 'Laravel') }}</title>
</head>
<body>

    <div class="login-page">

        {{-- Background blurred glow blobs --}}
        <div class="glow glow-blue"></div>
        <div class="glow glow-teal"></div>
        <div class="glow glow-pink"></div>

      

        <div class="login-card">

            {{-- Icon logo --}}
            <div> 
                <center><img src="{{ asset('assets/gambar/sti.png') }}" alt="Logo" style="width: 75%; height: auto; border-radius: 14px; display: flex; align-items: center; justify-content: center;" > 
                 </center>
            </div>
               
            <h1 class="login-title">PT. SISTEM TEKNOLOGI INTEGRATOR</h1>
            <p class="login-subtitle">selamat datang silahkan masuk ke akun Anda</p>

            {{-- Pesan error global (misal: kredensial salah) --}}
            @if ($errors->any())
                <div class="alert-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="login-form">
                @csrf

                <div class="form-group">
                    <label for="email">Email address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="name@company.com"
                        required
                        autofocus
                    >
                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="label-row">
                        <label for="password">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                        @endif
                    </div>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        required
                    >
                    @error('password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">Sign in to Dashboard</button>
                <div class = "creator-text">
                    <p>© 2026 | Developed by <a href="https://github.com/oneulyzaa" target="_blank">Aulyza Nova Ramadhani | Sistem Informasi 2270211020</a></p>
                </div>
                @if (Route::has('register'))
                    <p class="signup-text">
                        Don't have an account?
                        <a href="{{ route('register') }}">Sign up</a>
                    </p>
                @endif
            </form>
        </div>

    
    </div>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', 'Poppins', system-ui, -apple-system, sans-serif;
        }

        .login-page {
            position: relative;
            min-height: 100vh;
            width: 100%;
            background: url('{{ asset('assets/gambar/prd.jpg') }}');
            background-size: cover;
            background-position: center;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        /* Tambahkan ini tepat setelah .login-page */
        .login-page::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(53, 53, 53, 0.5); /* ubah 0.55 sesuai selera */
            z-index: 1;
}

        /* Blurred glow blobs */
        .glow {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
        }
        .glow-blue {
            width: 380px;
            height: 380px;
            top: -60px;
            left: -40px;
            background: radial-gradient(circle, rgba(59,90,246,0.55) 0%, rgba(59,90,246,0) 70%);
        }
        .glow-teal {
            width: 420px;
            height: 420px;
            top: 90px;
            left: 28%;
            background: radial-gradient(circle, rgba(20,184,166,0.35) 0%, rgba(20,184,166,0) 70%);
        }
        .glow-pink {
            width: 460px;
            height: 460px;
            bottom: -100px;
            right: -60px;
            background: radial-gradient(circle, rgba(217,70,219,0.45) 0%, rgba(217,70,219,0) 70%);
        }

        .crown-badge {
            position: absolute;
            top: 24px;
            left: 24px;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 400px;
            padding: 40px 36px;
            border-radius: 24px;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            text-align: center;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 20px;
            border-radius: 14px;
            background: linear-gradient(135deg, #818cf8, #c084fc);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(168,85,247,0.35);
        }

        .login-title {
            margin: 0 0 6px;
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
        }

        .login-subtitle {
            margin: 0 0 28px;
            font-size: 13.5px;
            color: rgba(255,255,255,0.55);
        }

        .login-form {
            text-align: left;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: rgba(255,255,255,0.85);
            margin-bottom: 8px;
        }

        .label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .label-row label {
            margin-bottom: 0;
        }

        .forgot-link {
            font-size: 12.5px;
            color: #a78bfa;
            text-decoration: none;
        }
        .forgot-link:hover {
            text-decoration: underline;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
            color: #ffffff;
            font-size: 14px;
            outline: none;
            transition: border-color 0.15s ease, background 0.15s ease;
        }
        .form-group input::placeholder {
            color: rgba(255,255,255,0.35);
        }
        .form-group input:focus {
            border-color: #a78bfa;
            background: rgba(255,255,255,0.08);
        }

        .field-error {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: #fca5a5;
        }

        .alert-error {
            margin-bottom: 18px;
            padding: 10px 14px;
            border-radius: 10px;
            background: rgba(248,113,113,0.12);
            border: 1px solid rgba(248,113,113,0.3);
            color: #fca5a5;
            font-size: 13px;
            text-align: left;
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            margin-top: 8px;
            border: none;
            border-radius: 999px;
            background: linear-gradient(90deg, #01157cba, #01157cba);
            color: #ffffff;
            font-size: 14.5px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.15s ease, transform 0.15s ease;
        }
        .btn-submit:hover {
            opacity: 0.92;
        }
        .btn-submit:active {
            transform: scale(0.98);
        }

        .signup-text {
            margin: 20px 0 0;
            text-align: center;
            font-size: 13px;
            color: rgba(255,255,255,0.6);
        }
        .signup-text a {
            color: #a78bfa;
            font-weight: 600;
            text-decoration: none;
        }
        .signup-text a:hover {
            text-decoration: underline;
        }

        .dots {
            position: absolute;
            bottom: 24px;
            left: 24px;
            display: flex;
            gap: 6px;
            z-index: 2;
        }
        .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
        }
        .dot.active {
            background: rgba(255,255,255,0.7);
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 32px 24px;
            }
        }
        .creator-text{
        text-align: center;
        margin-top: 15px;
        font-size: 13px;
        color: rgba(255,255,255,0.8);
    }
    .creator-text a{
    color: white;
    text-decoration: none;
}

    .creator-text a:hover{
        color: white;
        text-decoration: underline;
    }
    </style>

</body>
</html>