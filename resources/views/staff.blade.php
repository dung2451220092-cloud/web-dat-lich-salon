<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Bàn làm việc Barber — THE BARBER CLUB Staff Workspace.">
    <title>Barber Workspace — THE BARBER CLUB</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        /* ── Kanban-style appointment cards ── */
        .kanban-board {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 18px;
        }

        .appt-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            transition: all .3s var(--ease);
            position: relative;
        }
        .appt-card:hover {
            border-color: var(--gold-border);
            box-shadow: var(--shadow-md), var(--shadow-gold);
            transform: translateY(-3px);
        }

        /* Status color bar on top */
        .appt-card-bar {
            height: 4px;
            background: var(--border);
        }
        .appt-card-bar.pending   { background: linear-gradient(90deg, var(--amber), #F5D020); }
        .appt-card-bar.confirmed { background: linear-gradient(90deg, var(--blue), #8BC4F7); }
        .appt-card-bar.completed { background: linear-gradient(90deg, var(--green), #00C9A7); }

        .appt-card-body { padding: 20px; }

        .appt-time {
            font-family: var(--font-display);
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--gold);
            margin-bottom: 4px;
        }
        .appt-date {
            font-size: .78rem;
            color: var(--text-muted);
            margin-bottom: 14px;
        }

        .appt-customer {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
            padding: 12px;
            background: var(--bg-elevated);
            border-radius: var(--radius-md);
        }
        .appt-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--gold-dim);
            border: 2px solid var(--gold-border);
            display: flex; align-items: center; justify-content: center;
            font-size: .95rem;
            flex-shrink: 0;
        }
        .appt-customer-name {
            font-weight: 600;
            color: var(--text-white);
            font-size: .9rem;
        }
        .appt-service-detail {
            font-size: .78rem;
            color: var(--text-muted);
        }

        .appt-service-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-top: 1px solid var(--border-light);
            border-bottom: 1px solid var(--border-light);
            margin-bottom: 14px;
        }
        .appt-service-name { font-size: .85rem; color: var(--text-white); font-weight: 500; }
        .appt-price { font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--gold); }

        .payment-badges {
            display: flex;
            gap: 6px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        /* Complete form inside card */
        .complete-form-wrapper {
            border-top: 1px solid var(--border);
            padding-top: 14px;
        }
        .complete-form-wrapper label {
            font-size: .7rem;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 700;
            margin-bottom: 6px;
        }
        .inventory-row {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-bottom: 10px;
        }
        .inventory-row select { flex: 1; padding: 8px 10px; font-size: .8rem; }
        .inventory-row input  { width: 70px; padding: 8px 10px; font-size: .8rem; }

        /* Stats bar on top of page */
        .staff-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }
        .staff-stat {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 16px 18px;
            transition: all .2s;
        }
        .staff-stat:hover { border-color: var(--gold-border); }
        .staff-stat .ss-val {
            font-family: var(--font-display);
            font-size: 1.6rem;
            font-weight: 700;
        }
        .staff-stat .ss-lbl {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--text-muted);
            margin-top: 4px;
        }
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
            <span class="brand-role">Staff Workspace</span>
        </div>
        <div class="sidebar-user">
            <div class="sidebar-avatar">✂️</div>
            <div class="sidebar-user-info">
                <span class="user-name">{{ session('name') }}</span>
                <span class="user-role">Master Barber</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <span class="sidebar-nav-label">Workspace</span>
            <a href="/staff" class="active">
                <span class="nav-icon">📋</span> Danh Sách Ca Hẹn
            </a>
        </nav>
        <div class="sidebar-footer">
            <form method="POST" action="/logout">
                @csrf
                <button type="submit">🚪 Đăng Xuất</button>
            </form>
        </div>
    </aside>

    <!-- ══ Main ══ -->
    <div class="main-content">

        <div class="page-header">
            <div class="flex-between">
                <div>
                    <h1>Bàn Làm Việc Barber</h1>
                    <p class="page-subtitle">Chào {{ session('name') }} — Quản lý lịch hẹn và vật tư tiêu hao</p>
                </div>
                <div>
                    <span class="badge badge-gold" style="font-size:.8rem; padding:6px 14px;">
                        📅 {{ now()->format('d/m/Y') }}
                    </span>
                </div>
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

            <!-- ── Thống kê nhanh ── -->
            @php
                $pending   = $appointments->where('status', 'pending')->count();
                $completed = $appointments->where('status', 'completed')->count();
                $total     = count($appointments);
                $revenue   = $appointments->where('status', 'completed')->sum('service_price');
            @endphp
            <div class="staff-stats">
                <div class="staff-stat">
                    <div class="ss-val" style="color:var(--amber);">{{ $pending }}</div>
                    <div class="ss-lbl">⏳ Chờ phục vụ</div>
                </div>
                <div class="staff-stat">
                    <div class="ss-val" style="color:var(--green);">{{ $completed }}</div>
                    <div class="ss-lbl">✓ Hoàn thành</div>
                </div>
                <div class="staff-stat">
                    <div class="ss-val" style="color:var(--blue);">{{ $total }}</div>
                    <div class="ss-lbl">📋 Tổng lịch</div>
                </div>
                <div class="staff-stat">
                    <div class="ss-val" style="color:var(--gold); font-size:1.2rem;">{{ number_format($revenue, 0, ',', '.') }}<span style="font-size:.9rem;">₫</span></div>
                    <div class="ss-lbl">💰 Doanh thu</div>
                </div>
            </div>

            <!-- ── Header bảng ── -->
            <div class="flex-between" style="margin-bottom:20px;">
                <h2 style="font-size:1rem; font-family:var(--font-display); letter-spacing:.08em; text-transform:uppercase; color:var(--text-white);">
                    Danh Sách Ca Hẹn
                </h2>
                <div style="display:flex; gap:10px; align-items:center;">
                    <span class="badge badge-info">{{ count($appointments) }} lịch tổng</span>
                </div>
            </div>

            <!-- ── Kanban Board ── -->
            @if(count($appointments) === 0)
            <div class="card" style="text-align:center; padding:60px 24px;">
                <div style="font-size:3.5rem; margin-bottom:16px; opacity:.4;">✂️</div>
                <p style="color:var(--text-muted); font-size:.95rem; font-weight:600;">Không có lịch hẹn nào được giao.</p>
                <p style="color:var(--text-dim); font-size:.82rem; margin-top:6px;">Hãy chờ admin phân công lịch hẹn.</p>
            </div>
            @else
            <div class="kanban-board">
                @foreach($appointments as $app)
                <div class="appt-card">
                    <!-- Status bar -->
                    <div class="appt-card-bar {{ $app->status }}"></div>

                    <div class="appt-card-body">
                        <!-- Time -->
                        <div class="appt-time">⏰ {{ \Carbon\Carbon::parse($app->booking_time)->format('H:i') }}</div>
                        <div class="appt-date">📅 {{ \Carbon\Carbon::parse($app->booking_time)->format('l, d/m/Y') }}</div>

                        <!-- Customer info -->
                        <div class="appt-customer">
                            <div class="appt-avatar">👤</div>
                            <div>
                                <div class="appt-customer-name">{{ $app->customer_name }}</div>
                                <div class="appt-service-detail">#{{ $app->id }} · Khách hàng</div>
                            </div>
                        </div>

                        <!-- Service info -->
                        <div class="appt-service-info">
                            <span class="appt-service-name">{{ $app->service_name }}</span>
                            <span class="appt-price">{{ number_format($app->service_price, 0, ',', '.') }}₫</span>
                        </div>

                        <!-- Payment badges -->
                        <div class="payment-badges">
                            @if(($app->payment_method ?? 'cash') === 'qr')
                                <span class="badge badge-info">📱 QR</span>
                            @else
                                <span class="badge badge-gold">💵 Tiền mặt</span>
                            @endif

                            @if(($app->payment_status ?? 'unpaid') === 'paid')
                                <span class="badge badge-success">✓ Đã thanh toán</span>
                            @else
                                <span class="badge badge-warning">⏳ Chưa thanh toán</span>
                            @endif

                            @if($app->status === 'completed')
                                <span class="badge badge-completed">✓ Đã hoàn tất</span>
                            @elseif($app->status === 'confirmed')
                                <span class="badge badge-confirmed">Đã xác nhận</span>
                            @else
                                <span class="badge badge-pending">Chờ phục vụ</span>
                            @endif
                        </div>

                        <!-- Complete action -->
                        @if($app->status === 'pending')
                        <div class="complete-form-wrapper">
                            <form method="POST" action="/staff/complete/{{ $app->id }}"
                                  onsubmit="this.querySelector('button[type=submit]').classList.add('btn-loading'); this.querySelector('button[type=submit]').disabled=true;">
                                @csrf
                                <label>Vật tư tiêu hao (tùy chọn)</label>
                                <div class="inventory-row">
                                    <select name="item_id">
                                        <option value="">-- Chọn vật tư --</option>
                                        @foreach($inventory as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->display_name }} (Còn: {{ $item->quantity }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="quantity_used"
                                           value="1" min="1" max="999"
                                           title="Số lượng dùng">
                                </div>
                                <button type="submit" class="btn btn-primary btn-full btn-sm" style="padding:10px;">
                                    ✓ Hoàn Thành Ca &amp; Thu Tiền
                                </button>
                            </form>
                        </div>
                        @else
                        <div style="padding:10px; background:var(--green-dim); border-radius:var(--radius-sm); text-align:center; font-size:.8rem; color:var(--green); font-weight:600;">
                            ✓ Ca này đã được xử lý xong
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Also show list view for large screens -->
            <div style="margin-top:40px;">
                <div class="table-container">
                    <div class="table-header">
                        <h3>Danh Sách Đầy Đủ</h3>
                        <span class="badge badge-info">{{ count($appointments) }} lịch</span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Khách Hàng</th>
                                <th>Dịch Vụ</th>
                                <th>Thời Gian</th>
                                <th>Thanh Toán</th>
                                <th>Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $i => $app)
                            <tr>
                                <td class="td-muted">{{ $i + 1 }}</td>
                                <td><strong>{{ $app->customer_name }}</strong></td>
                                <td>
                                    {{ $app->service_name }}<br>
                                    <span class="td-muted">{{ number_format($app->service_price, 0, ',', '.') }} ₫</span>
                                </td>
                                <td>
                                    <strong>{{ \Carbon\Carbon::parse($app->booking_time)->format('d/m/Y') }}</strong><br>
                                    <span class="td-muted">{{ \Carbon\Carbon::parse($app->booking_time)->format('H:i') }}</span>
                                </td>
                                <td>
                                    @if(($app->payment_method ?? 'cash') === 'qr')
                                        <span style="font-size:.8rem;">📱 QR</span>
                                    @else
                                        <span style="font-size:.8rem;">💵 Tiền mặt</span>
                                    @endif<br>
                                    @if(($app->payment_status ?? 'unpaid') === 'paid')
                                        <span class="badge badge-success" style="margin-top:4px;">Đã TT</span>
                                    @else
                                        <span class="badge badge-warning" style="margin-top:4px;">Chưa TT</span>
                                    @endif
                                </td>
                                <td>
                                    @if($app->status === 'completed')
                                        <span class="badge badge-completed">✓ Hoàn tất</span>
                                    @elseif($app->status === 'confirmed')
                                        <span class="badge badge-confirmed">Đã xác nhận</span>
                                    @else
                                        <span class="badge badge-pending">Chờ phục vụ</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align:center; color:var(--text-muted); padding:32px;">
                                    Chưa có lịch hẹn nào được giao.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div><!-- /content-area -->
    </div><!-- /main-content -->
</div><!-- /dashboard-layout -->

<script>
// Sidebar mobile
const sidebarEl = document.getElementById('sidebar');
const overlayEl = document.getElementById('sidebar-overlay');
document.getElementById('sidebar-open').addEventListener('click', () => {
    sidebarEl.classList.add('open');
    overlayEl.classList.add('active');
});
overlayEl.addEventListener('click', () => {
    sidebarEl.classList.remove('open');
    overlayEl.classList.remove('active');
});
</script>

</body>
</html>