<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Tạo tài khoản khách hàng tại THE BARBER CLUB — Salon tóc nam đẳng cấp.">
    <title>Đăng Ký — THE BARBER CLUB</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="auth-page">

    <div class="auth-card" style="max-width:480px;">

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
            <span class="logo-sub">Tạo tài khoản mới</span>
        </div>

        <!-- Validation errors -->
        @if($errors->any())
            <div class="alert alert-error" role="alert">
                <span class="alert-icon">✕</span>
                <ul>
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success" role="alert">
                <span class="alert-icon">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Register Form -->
        <form method="POST" action="/register" novalidate id="register-form">
            @csrf

            <div class="form-group">
                <label for="name">Họ và tên</label>
                <div class="input-icon-wrap">
                    <span class="input-icon">👤</span>
                    <input type="text" id="name" name="name"
                           value="{{ old('name') }}"
                           placeholder="Nguyễn Văn A"
                           required autocomplete="name">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-icon-wrap">
                    <span class="input-icon">✉</span>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="email@gmail.com"
                           required autocomplete="email">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <div class="input-icon-wrap">
                    <span class="input-icon">🔒</span>
                    <input type="password" id="password" name="password"
                           placeholder="Tối thiểu 6 ký tự"
                           required autocomplete="new-password">
                    <button type="button"
                            style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-dim);font-size:.85rem;padding:0;"
                            onclick="togglePw('password')" aria-label="Hiện/ẩn mật khẩu">👁</button>
                </div>
                <!-- Password strength bar -->
                <div id="pw-strength-bar" style="height:3px; border-radius:2px; margin-top:8px; background:var(--border); overflow:hidden;">
                    <div id="pw-strength-fill" style="height:100%; width:0%; transition:width .3s, background .3s;"></div>
                </div>
                <span id="pw-strength-label" style="font-size:.72rem; color:var(--text-dim); margin-top:4px; display:block;"></span>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Xác nhận mật khẩu</label>
                <div class="input-icon-wrap">
                    <span class="input-icon">🔐</span>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           placeholder="Nhập lại mật khẩu"
                           required autocomplete="new-password">
                    <button type="button"
                            style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-dim);font-size:.85rem;padding:0;"
                            onclick="togglePw('password_confirmation')" aria-label="Hiện/ẩn mật khẩu">👁</button>
                </div>
            </div>

            <!-- Terms -->
            <div style="display:flex; align-items:flex-start; gap:10px; margin-bottom:20px; font-size:.82rem; color:var(--text-muted);">
                <input type="checkbox" id="agree" required
                       style="width:auto; margin-top:2px; accent-color:var(--gold);">
                <label for="agree" style="text-transform:none; letter-spacing:0; color:var(--text-muted); font-weight:400;">
                    Tôi đồng ý với <a href="#" style="color:var(--gold);">Điều khoản sử dụng</a> và <a href="#" style="color:var(--gold);">Chính sách bảo mật</a>
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-full"
                    onclick="this.classList.add('btn-loading'); this.disabled=true; this.form.submit();">
                ✓ Tạo Tài Khoản
            </button>
        </form>

        <p class="auth-footer-link">
            Đã có tài khoản? <a href="/login">Đăng nhập ngay</a>
        </p>

    </div>

<script>
function togglePw(fieldId) {
    const inp = document.getElementById(fieldId);
    inp.type = inp.type === 'password' ? 'text' : 'password';
}

// Password strength
const pwInput = document.getElementById('password');
pwInput.addEventListener('input', function() {
    const v = this.value;
    let score = 0;
    if (v.length >= 6)  score++;
    if (v.length >= 10) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^a-zA-Z0-9]/.test(v)) score++;

    const labels = ['', 'Rất yếu', 'Yếu', 'Trung bình', 'Mạnh', 'Rất mạnh'];
    const colors = ['', '#E05252', '#F5A623', '#F5D020', '#2ECC8A', '#00C9A7'];
    const widths = ['0%', '20%', '40%', '60%', '80%', '100%'];

    document.getElementById('pw-strength-fill').style.width    = widths[score];
    document.getElementById('pw-strength-fill').style.background = colors[score];
    document.getElementById('pw-strength-label').textContent  = labels[score];
    document.getElementById('pw-strength-label').style.color  = colors[score];
});
</script>

</body>
</html>