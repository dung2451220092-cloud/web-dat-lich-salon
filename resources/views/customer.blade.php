<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Đặt lịch hẹn cắt tóc — THE BARBER CLUB.">
    <title>Đặt Lịch Hẹn — THE BARBER CLUB</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        /* ── Stepper ── */
        .stepper {
            display: flex;
            align-items: center;
            margin-bottom: 28px;
            overflow-x: auto;
            padding-bottom: 4px;
        }
        .step {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        .step-circle {
            width: 34px; height: 34px;
            border-radius: 50%;
            border: 2px solid var(--border);
            background: var(--bg-elevated);
            color: var(--text-dim);
            font-size: .8rem;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            transition: all .3s;
            font-family: var(--font-display);
        }
        .step.active .step-circle   { border-color: var(--gold); background: var(--gold); color: #0F1115; }
        .step.done .step-circle     { border-color: var(--green); background: var(--green-dim); color: var(--green); }
        .step-label {
            font-size: .75rem;
            color: var(--text-dim);
            font-weight: 500;
            white-space: nowrap;
        }
        .step.active .step-label { color: var(--gold); font-weight: 600; }
        .step.done .step-label   { color: var(--green); }
        .step-line {
            flex: 1;
            height: 2px;
            background: var(--border);
            margin: 0 10px;
            min-width: 24px;
            transition: background .3s;
        }
        .step-line.done { background: var(--gold); }

        /* ── Timeline (History) ── */
        .timeline { position: relative; padding-left: 28px; }
        .timeline::before {
            content: '';
            position: absolute;
            left: 10px; top: 0; bottom: 0;
            width: 2px;
            background: linear-gradient(180deg, var(--gold), var(--border) 80%);
        }
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 18px 22px;
            transition: border-color .2s, box-shadow .2s;
        }
        .timeline-item:hover {
            border-color: var(--gold-border);
            box-shadow: var(--shadow-sm), var(--shadow-gold);
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -22px; top: 22px;
            width: 12px; height: 12px;
            border-radius: 50%;
            background: var(--gold);
            border: 2px solid var(--bg-base);
            box-shadow: 0 0 0 3px var(--gold-dim);
        }
        .timeline-item.status-completed::before { background: var(--green); box-shadow: 0 0 0 3px var(--green-dim); }
        .timeline-item.status-pending::before   { background: var(--amber); box-shadow: 0 0 0 3px var(--amber-dim); }
        .timeline-item.status-confirmed::before { background: var(--blue); box-shadow: 0 0 0 3px var(--blue-dim); }

        .timeline-meta {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }
        .timeline-time {
            font-size: .78rem;
            color: var(--text-dim);
            letter-spacing: .04em;
        }
        .timeline-title {
            font-family: var(--font-display);
            font-size: .95rem;
            font-weight: 700;
            color: var(--text-white);
            margin-bottom: 4px;
        }
        .timeline-detail {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            font-size: .8rem;
            color: var(--text-muted);
            margin-bottom: 10px;
        }
        .timeline-detail span { display: flex; align-items: center; gap: 4px; }
        .timeline-actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }

        /* Stepper form section */
        .form-section { display: none; }
        .form-section.active { display: block; animation: alertIn .3s ease; }
        .form-step-btns { display: flex; gap: 10px; margin-top: 20px; }
    </style>
</head>
<body>
<div class="dashboard-layout">

    <!-- Mobile Topbar -->
    <div class="mobile-topbar">
        <button id="sidebar-open" style="background:none;border:none;cursor:pointer;color:var(--text-white);font-size:1.4rem;" aria-label="Mở menu">☰</button>
        <span class="brand-name">⚔ Barber Club</span>
        <span style="width:28px;"></span>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- ══ Sidebar ══ -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <span class="brand-name">⚔ Barber Club</span>
            <span class="brand-role">Customer Portal</span>
        </div>
        <div class="sidebar-user">
            <div class="sidebar-avatar">👤</div>
            <div class="sidebar-user-info">
                <span class="user-name">{{ session('name') }}</span>
                <span class="user-role">Khách hàng</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <span class="sidebar-nav-label">Menu chính</span>
            <a href="/customer" class="active">
                <span class="nav-icon">📅</span> Đặt Lịch Hẹn
            </a>
            <a href="/">
                <span class="nav-icon">🏠</span> Trang Chủ
            </a>
        </nav>
        <div class="sidebar-footer">
            <button onclick="openChangePwModal()" class="btn btn-ghost btn-sm"
                    style="width:100%; justify-content:flex-start; gap:8px; margin-bottom:8px;">
                🔑 Đổi mật khẩu
            </button>
            <form method="POST" action="/logout">
                @csrf
                <button type="submit">🚣 Đăng Xuất</button>
            </form>
        </div>
    </aside>

    <!-- ══ Main ══ -->
    <div class="main-content">

        <div class="page-header">
            <div>
                <h1>Đặt Lịch Hẹn</h1>
                <p class="page-subtitle">Chào mừng, {{ session('name') }}! Chọn dịch vụ và thời gian phù hợp</p>
            </div>
        </div>

        <div class="content-area">

            <!-- ── Flash Messages ── -->
            @if(session('success'))
                <div class="alert alert-success"><span class="alert-icon">✓</span><span>{{ session('success') }}</span></div>
            @endif
            @if(session('error'))
                <div class="alert alert-error"><span class="alert-icon">✕</span><span>{{ session('error') }}</span></div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <span class="alert-icon">✕</span>
                    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="booking-layout">

                <!-- ────────────────────────────────
                     FORM ĐẶT LỊCH (Stepper)
                ──────────────────────────────── -->
                <div class="card">
                    <div class="card-title">ĐẶT LỊCH HẸN MỚI</div>

                    <!-- Stepper indicator -->
                    <div class="stepper" id="stepper">
                        <div class="step active" id="step-indicator-1">
                            <div class="step-circle">1</div>
                            <span class="step-label">Dịch vụ</span>
                        </div>
                        <div class="step-line" id="line-1"></div>
                        <div class="step" id="step-indicator-2">
                            <div class="step-circle">2</div>
                            <span class="step-label">Thợ</span>
                        </div>
                        <div class="step-line" id="line-2"></div>
                        <div class="step" id="step-indicator-3">
                            <div class="step-circle">3</div>
                            <span class="step-label">Giờ hẹn</span>
                        </div>
                        <div class="step-line" id="line-3"></div>
                        <div class="step" id="step-indicator-4">
                            <div class="step-circle">4</div>
                            <span class="step-label">Thanh toán</span>
                        </div>
                    </div>

                    <form method="POST" action="/customer/book" id="booking-form" novalidate>
                        @csrf

                        <!-- Step 1: Chọn dịch vụ -->
                        <div class="form-section active" id="form-step-1">
                            <div class="form-group">
                                <label for="service_id">Dịch vụ</label>
                                <select name="service_id" id="service_id" required onchange="updateAmount()">
                                    <option value="">-- Chọn dịch vụ --</option>
                                    @foreach($services as $s)
                                        <option value="{{ $s->id }}"
                                                data-price="{{ intval($s->price) }}"
                                                {{ old('service_id') == $s->id ? 'selected' : '' }}>
                                            {{ $s->name }} — {{ number_format($s->price, 0, ',', '.') }} ₫
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Preview selected service -->
                            <div id="service-preview" style="display:none; padding:14px; background:var(--gold-dim); border:1px solid var(--gold-border); border-radius:var(--radius-md); margin-bottom:16px;">
                                <div style="font-size:.75rem; color:var(--gold); font-weight:700; text-transform:uppercase; letter-spacing:.08em; margin-bottom:6px;">Dịch vụ đã chọn</div>
                                <div id="service-preview-name" style="font-weight:600; color:var(--text-white);"></div>
                                <div id="service-preview-price" style="font-family:var(--font-display); font-size:1.3rem; color:var(--gold); margin-top:4px;"></div>
                            </div>
                            <div class="form-step-btns">
                                <button type="button" class="btn btn-primary" onclick="nextStep(1)">Tiếp tục →</button>
                            </div>
                        </div>

                        <!-- Step 2: Chọn thợ barber -->
                        <div class="form-section" id="form-step-2">
                            <div class="form-group">
                                <label for="staff_id">Thợ Barber</label>
                                <select name="staff_id" id="staff_id" required>
                                    <option value="">-- Chọn thợ --</option>
                                    @foreach($barbers as $barber)
                                        <option value="{{ $barber->id }}" {{ old('staff_id') == $barber->id ? 'selected' : '' }}>
                                            ✂ {{ $barber->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-step-btns">
                                <button type="button" class="btn btn-ghost" onclick="prevStep(2)">← Quay lại</button>
                                <button type="button" class="btn btn-primary" onclick="nextStep(2)">Tiếp tục →</button>
                            </div>
                        </div>

                        <!-- Step 3: Ngày giờ hẹn -->
                        <div class="form-section" id="form-step-3">
                            <div class="form-group">
                                <label for="booking_time">Ngày &amp; Giờ hẹn</label>
                                <input type="datetime-local" id="booking_time" name="booking_time"
                                       value="{{ old('booking_time') }}"
                                       min="{{ now()->format('Y-m-d\TH:i') }}"
                                       required>
                            </div>
                            <div style="padding:12px; background:var(--amber-dim); border:1px solid rgba(245,166,35,.25); border-radius:var(--radius-md); font-size:.8rem; color:var(--amber);">
                                ⚠ Lưu ý: Vui lòng chọn giờ trong tương lai. Mỗi slot là 30 phút, tránh trùng lịch thợ.
                            </div>
                            <div class="form-step-btns">
                                <button type="button" class="btn btn-ghost" onclick="prevStep(3)">← Quay lại</button>
                                <button type="button" class="btn btn-primary" onclick="nextStep(3)">Tiếp tục →</button>
                            </div>
                        </div>

                        <!-- Step 4: Thanh toán -->
                        <div class="form-section" id="form-step-4">
                            <div class="form-group">
                                <label>Phương thức thanh toán</label>
                                <div class="payment-options">
                                    <label class="payment-option">
                                        <input type="radio" name="payment_method" value="cash"
                                               id="pay-cash"
                                               {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }}
                                               onchange="toggleQr()">
                                        <span class="payment-option-label">
                                            <span class="pay-icon">💵</span>
                                            <span>Tiền mặt</span>
                                        </span>
                                    </label>
                                    <label class="payment-option">
                                        <input type="radio" name="payment_method" value="qr"
                                               id="pay-qr"
                                               {{ old('payment_method') === 'qr' ? 'checked' : '' }}
                                               onchange="toggleQr()">
                                        <span class="payment-option-label">
                                            <span class="pay-icon">📱</span>
                                            <span>VietQR</span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <!-- QR preview box -->
                            <div class="qr-preview" id="qr-preview-box">
                                <p style="font-size:.8rem; color:var(--gold); font-weight:700; text-transform:uppercase; letter-spacing:1px;">
                                    Mã QR sẽ hiển thị sau khi đặt lịch
                                </p>
                                <p style="margin-top:8px;">Ngân hàng <strong style="color:var(--text-white);">MB Bank</strong> · STK: <strong style="color:var(--gold);">0987654321</strong></p>
                                <p>Số tiền sẽ khớp với dịch vụ bạn chọn.</p>
                                <div id="qr-img-preview" style="margin-top:14px;"></div>
                            </div>

                            <!-- Summary trước khi xác nhận -->
                            <div style="padding:16px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:var(--radius-md); margin-top:16px; font-size:.85rem;" id="booking-summary">
                                <div style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--text-muted); margin-bottom:10px;">Tóm tắt đặt lịch</div>
                                <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                                    <span style="color:var(--text-muted);">Dịch vụ</span>
                                    <span id="summary-service" style="color:var(--text-white); font-weight:600;">—</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                                    <span style="color:var(--text-muted);">Thợ</span>
                                    <span id="summary-staff" style="color:var(--text-white);">—</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                                    <span style="color:var(--text-muted);">Thời gian</span>
                                    <span id="summary-time" style="color:var(--text-white);">—</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; padding-top:10px; border-top:1px solid var(--border);">
                                    <span style="color:var(--text-muted);">Tổng tiền</span>
                                    <span id="summary-price" style="color:var(--gold); font-family:var(--font-display); font-size:1.15rem; font-weight:700;">—</span>
                                </div>
                            </div>

                            <div class="form-step-btns" style="margin-top:20px;">
                                <button type="button" class="btn btn-ghost" onclick="prevStep(4)">← Quay lại</button>
                                <button type="submit" id="book-btn" class="btn btn-primary" style="flex:1;"
                                        onclick="this.classList.add('btn-loading'); this.disabled=true; this.form.submit();">
                                    ✓ Xác Nhận Đặt Chỗ
                                </button>
                            </div>
                        </div>

                    </form>
                </div><!-- /card -->

                <!-- ────────────────────────────────
                     LỊCH SỬ HẸN (Timeline)
                ──────────────────────────────── -->
                <div>
                    <div class="flex-between" style="margin-bottom:20px;">
                        <h2 style="font-size:1rem; font-family:var(--font-display); letter-spacing:.08em; text-transform:uppercase; color:var(--text-white);">Lịch Hẹn Của Bạn</h2>
                        <span class="badge badge-gold">{{ count($myAppointments) }} lịch</span>
                    </div>

                    @if(count($myAppointments) === 0)
                    <div class="card" style="text-align:center; padding:48px 24px;">
                        <div style="font-size:3rem; margin-bottom:16px; opacity:.4;">📅</div>
                        <p style="color:var(--text-muted); font-size:.9rem;">Bạn chưa có lịch hẹn nào.</p>
                        <p style="color:var(--text-dim); font-size:.82rem; margin-top:6px;">Hãy đặt lịch ngay để trải nghiệm dịch vụ!</p>
                    </div>
                    @else
                    <div class="timeline">
                        @foreach($myAppointments as $app)
                        <div class="timeline-item status-{{ $app->status }}">
                            <div class="timeline-meta">
                                <div>
                                    <div class="timeline-time">
                                        📅 {{ \Carbon\Carbon::parse($app->booking_time)->format('d/m/Y') }}
                                        &nbsp;⏰ {{ \Carbon\Carbon::parse($app->booking_time)->format('H:i') }}
                                    </div>
                                    <div class="timeline-title">{{ $app->service_name }}</div>
                                </div>
                                <div style="display:flex; flex-direction:column; align-items:flex-end; gap:6px;">
                                    @if($app->status === 'completed')
                                        <span class="badge badge-completed">✓ Đã xong</span>
                                    @elseif($app->status === 'confirmed')
                                        <span class="badge badge-confirmed">✔ Đã xác nhận</span>
                                    @else
                                        <span class="badge badge-pending">⏳ Đang chờ</span>
                                    @endif

                                    @if(($app->payment_status ?? 'unpaid') === 'paid')
                                        <span class="badge badge-success">Đã TT</span>
                                    @else
                                        <span class="badge badge-warning">Chưa TT</span>
                                    @endif
                                </div>
                            </div>

                            <div class="timeline-detail">
                                <span>✂ {{ $app->staff_name }}</span>
                                <span>
                                    @if($app->payment_method === 'qr') 📱 QR
                                    @else 💵 Tiền mặt @endif
                                </span>
                                <span style="color:var(--gold); font-weight:600;">{{ number_format($app->service_price, 0, ',', '.') }} ₫</span>
                            </div>

                            <div class="timeline-actions">
                                @if($app->status === 'pending')
                                    <button class="btn btn-danger btn-sm"
                                            onclick="openCancelModal({{ $app->id }})">
                                        ✕ Hủy lịch
                                    </button>

                                @elseif($app->status === 'completed' && !in_array($app->id, $reviewedIds))
                                    <button class="btn btn-outline btn-sm"
                                            id="review-btn-{{ $app->id }}"
                                            onclick="toggleReviewForm({{ $app->id }})">
                                        ⭐ Đánh giá
                                    </button>

                                @elseif($app->status === 'completed' && in_array($app->id, $reviewedIds))
                                    <span style="color:var(--gold); font-size:.82rem; font-weight:600;">⭐ Đã đánh giá</span>
                                @endif
                            </div>

                            {{-- Review form inline --}}
                            @if($app->status === 'completed' && !in_array($app->id, $reviewedIds))
                            <div id="review-form-{{ $app->id }}" style="display:none; margin-top:14px;">
                                <form method="POST" action="{{ route('customer.review', $app->id) }}" class="review-form">
                                    @csrf
                                    <div style="font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--gold); margin-bottom:10px;">Chọn số sao</div>
                                    <div class="star-picker" title="Chọn số sao">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="star{{ $i }}-{{ $app->id }}"
                                                   name="rating" value="{{ $i }}" required>
                                            <label for="star{{ $i }}-{{ $app->id }}" title="{{ $i }} sao">★</label>
                                        @endfor
                                    </div>
                                    <textarea name="comment"
                                              placeholder="Nhận xét của bạn (tùy chọn)..."
                                              maxlength="500"></textarea>
                                    <button type="submit" class="btn btn-primary btn-sm btn-full">
                                        Gửi Đánh Giá
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

            </div><!-- /booking-layout -->
        </div><!-- /content-area -->
    </div><!-- /main-content -->
</div><!-- /dashboard-layout -->

<!-- ══ Modal hủy lịch ══ -->
<div class="modal-overlay" id="cancel-modal" role="dialog" aria-modal="true">
    <div class="modal-box">
        <div class="modal-icon">⚠️</div>
        <h3>Xác Nhận Hủy Lịch</h3>
        <p>Bạn có chắc muốn hủy lịch hẹn này không?<br>
           Hành động này <strong>không thể hoàn tác</strong>.</p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeCancelModal()">Quay lại</button>
            <form id="cancel-form" method="POST" action="" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-danger">✕ Xác nhận hủy</button>
            </form>
        </div>
    </div>
</div>

<script>
// ── Stepper logic ──
let currentStep = 1;
const totalSteps = 4;

function goToStep(step) {
    // Validate current before moving forward
    document.querySelectorAll('.form-section').forEach((s, i) => {
        s.classList.toggle('active', i + 1 === step);
    });

    for (let i = 1; i <= totalSteps; i++) {
        const indicator = document.getElementById('step-indicator-' + i);
        indicator.classList.remove('active', 'done');
        if (i < step)  indicator.classList.add('done');
        if (i === step) indicator.classList.add('active');

        const circle = indicator.querySelector('.step-circle');
        if (i < step) circle.textContent = '✓';
        else          circle.textContent = i;
    }
    for (let i = 1; i < totalSteps; i++) {
        const line = document.getElementById('line-' + i);
        if (line) line.classList.toggle('done', i < step);
    }

    currentStep = step;
    updateSummary();
}

function nextStep(from) {
    if (from === 1) {
        const sv = document.getElementById('service_id').value;
        if (!sv) { alert('Vui lòng chọn dịch vụ.'); return; }
    }
    if (from === 2) {
        const st = document.getElementById('staff_id').value;
        if (!st) { alert('Vui lòng chọn thợ barber.'); return; }
    }
    if (from === 3) {
        const bt = document.getElementById('booking_time').value;
        if (!bt) { alert('Vui lòng chọn thời gian.'); return; }
        const selected = new Date(bt);
        if (selected <= new Date()) { alert('Thời gian phải ở tương lai.'); return; }
    }
    if (from < totalSteps) goToStep(from + 1);
}

function prevStep(from) {
    if (from > 1) goToStep(from - 1);
}

// ── Service preview ──
function updateAmount() {
    const sel   = document.getElementById('service_id');
    const opt   = sel.options[sel.selectedIndex];
    const price = opt ? (parseInt(opt.getAttribute('data-price')) || 0) : 0;
    const name  = opt ? opt.text.split('—')[0].trim() : '';

    if (sel.value) {
        document.getElementById('service-preview').style.display = 'block';
        document.getElementById('service-preview-name').textContent  = name;
        document.getElementById('service-preview-price').textContent = price.toLocaleString('vi-VN') + ' ₫';
    } else {
        document.getElementById('service-preview').style.display = 'none';
    }

    const isQr = document.getElementById('pay-qr') && document.getElementById('pay-qr').checked;
    if (isQr && price > 0) renderQr(price);
    updateSummary();
}

function updateSummary() {
    const sel  = document.getElementById('service_id');
    const opt  = sel.options[sel.selectedIndex];
    const stSel = document.getElementById('staff_id');
    const stOpt = stSel.options[stSel.selectedIndex];
    const bt   = document.getElementById('booking_time').value;
    const price = opt ? (parseInt(opt.getAttribute('data-price')) || 0) : 0;

    document.getElementById('summary-service').textContent = sel.value ? opt.text.split('—')[0].trim() : '—';
    document.getElementById('summary-staff').textContent   = stSel.value ? stOpt.text.replace('✂ ', '') : '—';
    document.getElementById('summary-time').textContent    = bt ? bt.replace('T', ' ') : '—';
    document.getElementById('summary-price').textContent   = price > 0 ? price.toLocaleString('vi-VN') + ' ₫' : '—';
}

// ── QR ──
function renderQr(price) {
    const qrUrl = 'https://img.vietqr.io/image/MB-0987654321-compact2.png'
        + '?amount=' + price + '&addInfo=DATLICH%20THE%20BARBER%20CLUB';
    document.getElementById('qr-img-preview').innerHTML =
        '<img src="' + qrUrl + '" alt="QR thanh toán" loading="lazy" style="border-radius:10px;">';
}

function toggleQr() {
    const isQr = document.getElementById('pay-qr').checked;
    const box  = document.getElementById('qr-preview-box');
    box.classList.toggle('active', isQr);
    if (isQr) updateAmount();
}

// ── Modal hủy lịch ──
function openCancelModal(id) {
    document.getElementById('cancel-form').action = '/customer/cancel/' + id;
    document.getElementById('cancel-modal').classList.add('active');
}
function closeCancelModal() {
    document.getElementById('cancel-modal').classList.remove('active');
}
document.getElementById('cancel-modal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCancelModal(); });

// ── Toggle review form ──
function toggleReviewForm(id) {
    const el = document.getElementById('review-form-' + id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

// ── Sidebar mobile ──
const sidebarEl  = document.getElementById('sidebar');
const overlayEl  = document.getElementById('sidebar-overlay');
document.getElementById('sidebar-open').addEventListener('click', () => {
    sidebarEl.classList.add('open');
    overlayEl.classList.add('active');
});
overlayEl.addEventListener('click', () => {
    sidebarEl.classList.remove('open');
    overlayEl.classList.remove('active');
});

// If old() values exist — restore step
document.addEventListener('DOMContentLoaded', () => {
    const hasService = '{{ old("service_id") }}' !== '';
    const hasStaff   = '{{ old("staff_id") }}' !== '';
    const hasTime    = '{{ old("booking_time") }}' !== '';
    const hasPayment = '{{ old("payment_method") }}' !== '';

    if (hasPayment) goToStep(4);
    else if (hasTime) goToStep(3);
    else if (hasStaff) goToStep(2);

    toggleQr();
    updateAmount();
});
</script>

@include('partials.change_password_modal')

</body>
</html>