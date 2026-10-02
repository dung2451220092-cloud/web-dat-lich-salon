<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán QR — THE BARBER CLUB</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .qr-page { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px;
                   background:radial-gradient(ellipse 60% 50% at 50% 50%, rgba(201,168,76,.07) 0%, transparent 70%), var(--bg-main); }
        .qr-card { background:var(--bg-card); border:1px solid rgba(201,168,76,.3); border-radius:14px;
                   padding:44px 40px; max-width:460px; width:100%; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,.6); }
        .qr-card h2 { color:var(--gold, #c9a84c); font-family:'Cinzel', serif; font-size:1.3rem; margin-bottom:8px; }
        .qr-card .amount { font-size:2rem; font-weight:700; color:#fff; margin:16px 0; font-family:'Cinzel',serif; }
        .qr-img { max-width:220px; border-radius:10px; margin:16px auto; display:block;
                  border:2px solid rgba(201,168,76,.3); box-shadow:0 0 20px rgba(201,168,76,.2); }
        .qr-info { font-size:.85rem; color:var(--text-muted,#7a8094); margin-top:12px; line-height:1.7; }
        .qr-info strong { color:#fff; }
        .qr-steps { text-align:left; margin:20px 0; padding:16px 20px;
                    background:rgba(201,168,76,.06); border-radius:8px; border:1px solid rgba(201,168,76,.15); }
        .qr-steps li { font-size:.85rem; color:var(--text-muted,#7a8094); margin-bottom:8px; padding-left:4px; }
        .qr-steps li strong { color:#fff; }
    </style>
</head>
<body class="qr-page">
    <div class="qr-card">
        <div style="font-size:2.5rem; margin-bottom:8px;">📱</div>
        <h2>THANH TOÁN CHUYỂN KHOẢN</h2>
        <p style="color:var(--text-muted); font-size:.85rem;">Mã lịch hẹn: <strong style="color:var(--gold)">#{{ str_pad($appointmentId, 5, '0', STR_PAD_LEFT) }}</strong></p>

        <div class="amount">{{ number_format($amount, 0, ',', '.') }} ₫</div>

        {{-- Ảnh QR VietQR (MB Bank) --}}
        @php
            $addInfo = 'BILL' . str_pad($appointmentId, 5, '0', STR_PAD_LEFT);
            $qrUrl = "https://img.vietqr.io/image/MB-0987654321-compact2.png?amount={$amount}&addInfo={$addInfo}";
        @endphp
        <img src="{{ $qrUrl }}" alt="Mã QR thanh toán" class="qr-img" loading="eager">

        <div class="qr-info">
            <strong>Ngân hàng:</strong> MB Bank<br>
            <strong>Số tài khoản:</strong> 0987654321<br>
            <strong>Nội dung CK:</strong> {{ $addInfo }}
        </div>

        <ol class="qr-steps">
            <li>Mở app ngân hàng → <strong>Quét mã QR</strong></li>
            <li>Kiểm tra số tiền: <strong>{{ number_format($amount, 0, ',', '.') }} ₫</strong></li>
            <li>Nội dung chuyển khoản: <strong>{{ $addInfo }}</strong></li>
            <li>Xác nhận và chuyển tiền</li>
        </ol>

        <div style="margin-top:24px; display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
            <a href="/customer" class="btn btn-primary">← Về trang lịch hẹn</a>
            <a href="/" class="btn btn-outline">Trang chủ</a>
        </div>
        <p style="font-size:.75rem; color:var(--text-dim,#4a5068); margin-top:16px;">
            Thanh toán sẽ được xác nhận sau khi barber hoàn tất dịch vụ.
        </p>
    </div>
</body>
</html>
