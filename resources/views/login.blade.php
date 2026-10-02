<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Đăng nhập vào hệ thống THE BARBER CLUB — Salon tóc nam đẳng cấp.">
    <title>Đăng Nhập — THE BARBER CLUB</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="auth-page">

    <div class="auth-card">

        <!-- Back to home -->
        <a href="/" class="auth-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Về trang chủ
        </a>

        <!-- Logo -->
        <div class="auth-logo">
            <span class="logo-text">⚔ THE BARBER CLUB</span>
            <span class="logo-sub">Salon Management System</span>
        </div>

        <p class="auth-title">Đăng nhập vào tài khoản của bạn</p>

        <!-- Flash Messages -->
        @if(session('error'))
            <div class="alert alert-error" role="alert">
                <span class="alert-icon">✕</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success" role="alert">
                <span class="alert-icon">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error" role="alert">
                <span class="alert-icon">✕</span>
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="/login" id="login-form" novalidate>
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-icon-wrap">
                    <span class="input-icon">✉</span>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="email@example.com"
                           required autocomplete="email">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <div class="input-icon-wrap">
                    <span class="input-icon">🔒</span>
                    <input type="password" id="password" name="password"
                           placeholder="Nhập mật khẩu"
                           required autocomplete="current-password">
                    <button type="button" id="toggle-pw"
                            style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-dim);font-size:.85rem;padding:0;"
                            onclick="togglePw()" aria-label="Hiện/ẩn mật khẩu">👁</button>
                </div>
            </div>

            <button type="submit" id="login-btn" class="btn btn-primary btn-full"
                    style="margin-top:8px;"
                    onclick="this.classList.add('btn-loading'); this.disabled=true; this.form.submit();">
                ✓ Vào Hệ Thống
            </button>
        </form>

        <div class="auth-divider">hoặc</div>

        <a href="/register" class="btn btn-outline btn-full">
            ✦ Tạo tài khoản mới
        </a>

        <p class="auth-footer-link" style="margin-top:16px;">
            Chưa có tài khoản? <a href="/register">Đăng ký ngay</a>
        </p>

        <!-- Demo accounts hint -->
        <div style="margin-top:24px; padding:14px; background:var(--gold-dim); border:1px solid var(--gold-border); border-radius:var(--radius-md);">
            <p style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--gold); margin-bottom:8px;">Tài khoản demo</p>
            <p style="font-size:.78rem; color:var(--text-muted); line-height:1.6;">
                🛡 Admin: admin@salon.com<br>
                ✂ Barber: barber@salon.com<br>
                👤 Khách: customer@salon.com
            </p>
        </div>

    </div>

<script>
function togglePw() {
    const inp = document.getElementById('password');
    inp.type = inp.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>