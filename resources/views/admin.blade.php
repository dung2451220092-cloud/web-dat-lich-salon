<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Quản trị trung tâm — THE BARBER CLUB Admin Dashboard.">
    <title>Admin Dashboard — THE BARBER CLUB</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        /* ── Mini Chart Bars (CSS only) ── */
        .chart-bar-wrap {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            height: 48px;
            margin-top: 14px;
        }
        .chart-bar {
            flex: 1;
            background: linear-gradient(180deg, var(--gold) 0%, var(--gold-light) 100%);
            border-radius: 3px 3px 0 0;
            opacity: .7;
            transition: opacity .2s;
        }
        .chart-bar:hover { opacity: 1; }

        /* ── Revenue card special ── */
        .revenue-card {
            background: linear-gradient(135deg, rgba(212,175,55,.15) 0%, var(--bg-card) 60%);
            border-color: var(--gold-border) !important;
            box-shadow: var(--shadow-md), var(--shadow-gold) !important;
        }

        /* ── Alert card (Inventory) ── */
        .alert-card {
            background: linear-gradient(135deg, rgba(224,82,82,.08) 0%, var(--bg-card) 60%);
            border-color: rgba(224,82,82,.25) !important;
        }
        .alert-card .stat-icon { filter: drop-shadow(0 0 6px rgba(224,82,82,.5)); }

        /* ── Inventory / Service table row highlight ── */
        .row-critical { background: rgba(224,82,82,.06) !important; }
        .row-critical:hover { background: rgba(224,82,82,.12) !important; }

        /* ── Staff list compact ── */
        .staff-list-compact { padding: 4px 0; }

        /* ── Admin main grid ── */
        .admin-two-col {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 28px;
            align-items: start;
        }

        /* Progress bar for inventory */
        .inv-progress-wrap {
            background: var(--border);
            border-radius: 3px;
            height: 5px;
            margin-top: 4px;
            overflow: hidden;
        }
        .inv-progress-fill {
            height: 100%;
            border-radius: 3px;
            transition: width .5s;
        }
        .inv-critical .inv-progress-fill { background: var(--red); }
        .inv-warning  .inv-progress-fill { background: var(--amber); }
        .inv-ok       .inv-progress-fill { background: var(--green); }

        /* ── Tab navigation ── */
        .admin-tabs {
            display: flex;
            gap: 4px;
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 4px;
            margin-bottom: 28px;
            width: fit-content;
        }
        .admin-tab {
            padding: 9px 20px;
            border-radius: var(--radius-md);
            font-size: .8rem;
            font-weight: 600;
            letter-spacing: .04em;
            color: var(--text-muted);
            cursor: pointer;
            border: none;
            background: transparent;
            font-family: var(--font-body);
            transition: all .2s var(--ease);
            white-space: nowrap;
        }
        .admin-tab:hover { color: var(--text-white); background: var(--bg-card); }
        .admin-tab.active {
            background: linear-gradient(135deg, rgba(212,175,55,.2), rgba(212,175,55,.08));
            color: var(--gold);
            border: 1px solid var(--gold-border);
        }

        /* ── Tab panels ── */
        .tab-panel { display: none; }
        .tab-panel.active { display: block; animation: alertIn .3s ease; }

        /* ── Service table actions ── */
        .svc-edit-form { display: none; }
        .svc-edit-form.open { display: table-row; }

        /* Inline edit row */
        .edit-row td {
            background: rgba(212,175,55,.05);
            border-top: 1px solid var(--gold-border);
            border-bottom: 1px solid var(--gold-border);
            padding: 12px 20px !important;
        }
        .edit-row-inner {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .edit-row-inner .form-group {
            margin-bottom: 0;
            flex: 1;
            min-width: 120px;
        }
        .edit-row-inner label {
            font-size: .68rem;
            margin-bottom: 4px;
        }
        .edit-row-inner input {
            padding: 8px 10px;
            font-size: .82rem;
        }

        /* Restock inline form */
        .restock-form {
            display: none;
            margin-top: 8px;
            padding: 12px;
            background: var(--gold-dim);
            border: 1px solid var(--gold-border);
            border-radius: var(--radius-md);
            animation: alertIn .25s ease;
        }
        .restock-form.open { display: block; }
        .restock-row {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .restock-row input {
            width: 90px;
            padding: 8px 10px;
            font-size: .82rem;
        }

        /* Confirm delete modal trigger */
        .delete-form { display: inline; }

        @media (max-width: 1100px) {
            .admin-two-col { grid-template-columns: 1fr; }
        }
        @media (max-width: 700px) {
            .admin-tabs { flex-wrap: wrap; width: 100%; }
            .admin-tab  { flex: 1; text-align: center; }
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
            <span class="brand-role">Admin Panel</span>
        </div>
        <div class="sidebar-user">
            <div class="sidebar-avatar">👑</div>
            <div class="sidebar-user-info">
                <span class="user-name">{{ session('name') }}</span>
                <span class="user-role">Administrator</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <span class="sidebar-nav-label">Quản Trị</span>
            <a href="#tab-overview"   class="sidebar-tab-link active" data-tab="tab-overview">
                <span class="nav-icon">📊</span> Tổng Quan
            </a>
            <a href="#tab-services"   class="sidebar-tab-link" data-tab="tab-services">
                <span class="nav-icon">✂️</span> Dịch Vụ
            </a>
            <a href="#tab-inventory"  class="sidebar-tab-link" data-tab="tab-inventory">
                <span class="nav-icon">📦</span> Kho Hàng
                @if(count($inventoryAlerts) > 0)
                    <span class="badge badge-danger" style="font-size:.6rem; padding:2px 6px; margin-left:auto;">{{ count($inventoryAlerts) }}</span>
                @endif
            </a>
            <a href="#tab-staff"      class="sidebar-tab-link" data-tab="tab-staff">
                <span class="nav-icon">👥</span> Nhân Sự
            </a>
            <span class="sidebar-nav-label" style="margin-top:16px;">Hệ Thống</span>
            <a href="/">
                <span class="nav-icon">🏠</span> Trang Chủ
            </a>
        </nav>
        <div style="padding:14px 18px; margin:12px; background:var(--gold-dim); border:1px solid var(--gold-border); border-radius:var(--radius-lg);">
            <div style="font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--gold); margin-bottom:6px;">Hệ thống</div>
            <div style="font-size:.78rem; color:var(--text-muted);">{{ count($staffs) }} Barbers hoạt động</div>
            <div style="font-size:.78rem; color:var(--text-muted);">{{ $todayAppointments }} lịch hôm nay</div>
            <div style="font-size:.78rem; color:var(--text-muted);">{{ count($services) }} dịch vụ</div>
        </div>
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
                    <h1>Quản Trị Trung Tâm</h1>
                    <p class="page-subtitle">Tổng quan hoạt động kinh doanh · {{ now()->format('d/m/Y H:i') }}</p>
                </div>
                <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                    @if(count($inventoryAlerts) > 0)
                        <span class="badge badge-danger" style="font-size:.8rem; padding:6px 14px; animation: pulse 2s infinite; cursor:pointer;"
                              onclick="switchTab('tab-inventory')">
                            ⚠ {{ count($inventoryAlerts) }} cảnh báo kho
                        </span>
                    @else
                        <span class="badge badge-success" style="font-size:.8rem; padding:6px 14px;">✓ Kho ổn định</span>
                    @endif
                    <span class="badge badge-gold" style="font-size:.8rem; padding:6px 14px;">
                        {{ count($services) }} dịch vụ
                    </span>
                    <!-- Nút đổi mật khẩu -->
                    <button onclick="openChangePwModal()"
                            class="btn btn-ghost btn-sm"
                            style="gap:6px;"
                            title="Đổi mật khẩu">
                        🔑 Đổi MK
                    </button>
                </div>
            </div>
        </div>

        <div class="content-area">

            <!-- ── Flash Messages ── -->
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    <span class="alert-icon">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error" role="alert">
                    <span class="alert-icon">✕</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-error" role="alert">
                    <span class="alert-icon">✕</span>
                    <ul>
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <!-- ══════════════════════════════════════
                 TAB NAVIGATION (inline buttons)
            ══════════════════════════════════════ -->
            <div class="admin-tabs" id="admin-tabs-bar">
                <button class="admin-tab active" onclick="switchTab('tab-overview')"  id="btn-tab-overview">📊 Tổng Quan</button>
                <button class="admin-tab"        onclick="switchTab('tab-services')"  id="btn-tab-services">✂ Dịch Vụ <span class="badge badge-gold" style="font-size:.6rem; padding:2px 7px; margin-left:4px;">{{ count($services) }}</span></button>
                <button class="admin-tab"        onclick="switchTab('tab-inventory')" id="btn-tab-inventory">
                    📦 Kho Hàng
                    @if(count($inventoryAlerts) > 0)
                        <span class="badge badge-danger" style="font-size:.6rem; padding:2px 7px; margin-left:4px;">{{ count($inventoryAlerts) }}</span>
                    @endif
                </button>
                <button class="admin-tab"        onclick="switchTab('tab-staff')"     id="btn-tab-staff">👥 Nhân Sự</button>
            </div>

            <!-- ══════════════════════════════════════
                 TAB 1: TỔNG QUAN (Overview)
            ══════════════════════════════════════ -->
            <div class="tab-panel active" id="tab-overview">

                <!-- Metric Cards -->
                <div class="stats-grid" style="margin-bottom:36px;">

                    <!-- Doanh thu -->
                    <div class="stat-card revenue-card">
                        <span class="stat-icon">💰</span>
                        <div class="stat-label">Doanh Thu Thực Tế</div>
                        <div class="stat-value" style="color:var(--gold); font-size:1.6rem;">
                            {{ number_format($totalRevenue, 0, ',', '.') }}<span style="font-size:.9rem; color:var(--gold); opacity:.7;"> ₫</span>
                        </div>
                        <div class="stat-change">↑ Các lịch đã hoàn thành</div>
                        <div class="chart-bar-wrap">
                            @php $bars = [35, 55, 45, 70, 50, 85, 60]; @endphp
                            @foreach($bars as $h)
                                <div class="chart-bar" style="height:{{ $h }}%;"></div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Khách hôm nay -->
                    <div class="stat-card">
                        <span class="stat-icon">👤</span>
                        <div class="stat-label">Khách Hôm Nay</div>
                        <div class="stat-value" style="color:var(--blue);">{{ $todayAppointments }}</div>
                        <div class="stat-change" style="color:var(--blue);">Tổng lịch hẹn ngày {{ date('d/m') }}</div>
                    </div>

                    <!-- Dịch vụ -->
                    <div class="stat-card" style="cursor:pointer;" onclick="switchTab('tab-services')">
                        <span class="stat-icon">✂️</span>
                        <div class="stat-label">Tổng Dịch Vụ</div>
                        <div class="stat-value" style="color:var(--gold);">{{ count($services) }}</div>
                        <div class="stat-change" style="color:var(--text-muted);">Click để quản lý</div>
                    </div>

                    <!-- Cảnh báo kho -->
                    <div class="stat-card {{ count($inventoryAlerts) > 0 ? 'alert-card' : '' }}" style="cursor:pointer;" onclick="switchTab('tab-inventory')">
                        <span class="stat-icon">📦</span>
                        <div class="stat-label">Vật Tư Cần Nhập</div>
                        <div class="stat-value" style="color:{{ count($inventoryAlerts) > 0 ? 'var(--red)' : 'var(--green)' }};">
                            {{ count($inventoryAlerts) }}
                        </div>
                        <div class="stat-change" style="color:{{ count($inventoryAlerts) > 0 ? 'var(--red)' : 'var(--green)' }};">
                            {{ count($inventoryAlerts) > 0 ? '⚠ Click để kiểm tra' : '✓ Kho đang ổn định' }}
                        </div>
                    </div>

                    <!-- Barbers -->
                    <div class="stat-card" style="cursor:pointer;" onclick="switchTab('tab-staff')">
                        <span class="stat-icon">💈</span>
                        <div class="stat-label">Tổng Barber</div>
                        <div class="stat-value" style="color:var(--gold);">{{ count($staffs) }}</div>
                        <div class="stat-change" style="color:var(--text-muted);">Nhân sự đang làm việc</div>
                    </div>

                </div>

                <!-- Quick links -->
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:14px;">
                    <button class="btn btn-outline" onclick="switchTab('tab-services')" style="justify-content:flex-start; gap:12px;">
                        <span style="font-size:1.3rem;">✂</span>
                        <span>Quản lý Dịch vụ</span>
                    </button>
                    <button class="btn btn-outline" onclick="switchTab('tab-inventory')" style="justify-content:flex-start; gap:12px;">
                        <span style="font-size:1.3rem;">📦</span>
                        <span>Nhập kho hàng</span>
                    </button>
                    <button class="btn btn-outline" onclick="switchTab('tab-staff')" style="justify-content:flex-start; gap:12px;">
                        <span style="font-size:1.3rem;">👥</span>
                        <span>Thêm Barber mới</span>
                    </button>
                </div>
            </div><!-- /tab-overview -->

            <!-- ══════════════════════════════════════
                 TAB 2: QUẢN LÝ DỊCH VỤ
            ══════════════════════════════════════ -->
            <div class="tab-panel" id="tab-services">
                <div class="admin-two-col">

                    <!-- Form thêm dịch vụ mới -->
                    <div class="card">
                        <div class="card-title">✦ THÊM DỊCH VỤ MỚI</div>
                        <form method="POST" action="{{ route('admin.services.add') }}" novalidate
                              onsubmit="this.querySelector('button[type=submit]').classList.add('btn-loading'); this.querySelector('button[type=submit]').disabled=true;">
                            @csrf
                            <div class="form-group">
                                <label for="svc-name">Tên dịch vụ</label>
                                <div class="input-icon-wrap">
                                    <span class="input-icon">✂</span>
                                    <input type="text" id="svc-name" name="name"
                                           value="{{ old('name') }}"
                                           placeholder="VD: Cắt tóc Classic" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="svc-price">Giá (VNĐ)</label>
                                <div class="input-icon-wrap">
                                    <span class="input-icon">₫</span>
                                    <input type="number" id="svc-price" name="price"
                                           value="{{ old('price') }}"
                                           placeholder="VD: 150000"
                                           min="0" step="1000" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-full">
                                + Thêm Dịch Vụ
                            </button>
                        </form>
                    </div>

                    <!-- Danh sách dịch vụ -->
                    <div class="table-container">
                        <div class="table-header">
                            <h3>DANH SÁCH DỊCH VỤ</h3>
                            <span class="badge badge-gold">{{ count($services) }} dịch vụ</span>
                        </div>
                        <table id="services-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên Dịch Vụ</th>
                                    <th>Giá</th>
                                    <th>Thời Gian</th>
                                    <th style="text-align:center;">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $i => $svc)
                                <!-- Display row -->
                                <tr id="svc-row-{{ $svc->id }}">
                                    <td class="td-muted">{{ $i + 1 }}</td>
                                    <td>
                                        <strong style="color:var(--text-white);">{{ $svc->name }}</strong>
                                    </td>
                                    <td>
                                        <span style="font-family:var(--font-display); font-weight:700; color:var(--gold);">
                                            {{ number_format($svc->price, 0, ',', '.') }} ₫
                                        </span>
                                    </td>
                                    <td class="td-muted">
                                        @if(isset($svc->duration) && $svc->duration)
                                            ⏱ {{ $svc->duration }} phút
                                        @else
                                            <span style="color:var(--text-dim);">—</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        <div style="display:flex; gap:6px; justify-content:center; flex-wrap:wrap;">
                                            <!-- Nút sửa -->
                                            <button class="btn btn-outline btn-sm"
                                                    onclick="toggleEditRow({{ $svc->id }})"
                                                    id="edit-btn-{{ $svc->id }}"
                                                    title="Sửa dịch vụ">
                                                ✏ Sửa
                                            </button>
                                            <!-- Nút xóa → mở modal confirm -->
                                            <button class="btn btn-danger btn-sm"
                                                    onclick="openDeleteModal({{ $svc->id }}, '{{ addslashes($svc->name) }}')"
                                                    title="Xóa dịch vụ">
                                                ✕ Xóa
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Edit inline row (hidden by default) -->
                                <tr class="edit-row" id="edit-row-{{ $svc->id }}" style="display:none;">
                                    <td colspan="5">
                                        <form method="POST"
                                              action="{{ route('admin.services.update', $svc->id) }}"
                                              onsubmit="this.querySelector('button[type=submit]').classList.add('btn-loading'); this.querySelector('button[type=submit]').disabled=true;">
                                            @csrf
                                            <div class="edit-row-inner">
                                                <div class="form-group">
                                                    <label>Tên dịch vụ</label>
                                                    <input type="text" name="name"
                                                           value="{{ $svc->name }}"
                                                           placeholder="Tên dịch vụ" required>
                                                </div>
                                                <div class="form-group" style="max-width:160px;">
                                                    <label>Giá (VNĐ)</label>
                                                    <input type="number" name="price"
                                                           value="{{ $svc->price }}"
                                                           min="0" step="1000" required>
                                                </div>
                                                <div style="display:flex; gap:6px; padding-bottom:2px;">
                                                    <button type="submit" class="btn btn-primary btn-sm">✓ Lưu</button>
                                                    <button type="button" class="btn btn-ghost btn-sm"
                                                            onclick="toggleEditRow({{ $svc->id }})">✕</button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" style="text-align:center; color:var(--text-muted); padding:40px;">
                                        <div style="font-size:2rem; margin-bottom:10px; opacity:.4;">✂</div>
                                        Chưa có dịch vụ nào. Hãy thêm dịch vụ đầu tiên!
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div><!-- /tab-services -->

            <!-- ══════════════════════════════════════
                 TAB 3: KHO HÀNG (Inventory)
            ══════════════════════════════════════ -->
            <div class="tab-panel" id="tab-inventory">

                @if(count($inventoryAlerts) > 0)
                <div class="alert alert-warning" style="margin-bottom:20px;">
                    <span class="alert-icon">⚠</span>
                    <span>Có <strong>{{ count($inventoryAlerts) }}</strong> mặt hàng dưới ngưỡng tối thiểu. Vui lòng nhập thêm hàng ngay!</span>
                </div>
                @endif

                <div class="table-container">
                    <div class="table-header">
                        <h3>📦 TOÀN BỘ KHO VẬT TƯ</h3>
                        <span class="badge badge-info">{{ count($allInventory) }} mặt hàng</span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tên Vật Tư</th>
                                <th>Tồn Kho</th>
                                <th>Ngưỡng Min</th>
                                <th>Trạng Thái</th>
                                <th style="text-align:center; min-width:200px;">Nhập Hàng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allInventory as $i => $item)
                            @php
                                $qty = $item->quantity ?? 0;
                                $min = $item->min_threshold ?? 0;
                                $pct = $min > 0 ? min(100, round(($qty / $min) * 100)) : 100;
                                $cls = $qty == 0 ? 'inv-critical' : ($qty <= $min ? 'inv-warning' : 'inv-ok');
                                $itemName = $item->name ?? $item->item_name ?? "(Không tên)";
                            @endphp
                            <tr class="{{ $qty <= $min ? 'row-critical' : '' }}">
                                <td class="td-muted">{{ $i + 1 }}</td>
                                <td>
                                    <strong style="color:var(--text-white);">{{ $itemName }}</strong>
                                </td>
                                <td>
                                    <span style="font-size:1.05rem; font-weight:700; font-family:var(--font-display);
                                                 color:{{ $qty == 0 ? 'var(--red)' : ($qty <= $min ? 'var(--amber)' : 'var(--green)') }};">
                                        {{ $qty }}
                                    </span>
                                    <div class="inv-progress-wrap {{ $cls }}">
                                        <div class="inv-progress-fill" style="width:{{ $pct }}%;"></div>
                                    </div>
                                </td>
                                <td class="td-muted">{{ $min }}</td>
                                <td>
                                    @if($qty == 0)
                                        <span class="badge badge-danger">🔴 Hết hàng</span>
                                    @elseif($qty <= $min)
                                        <span class="badge badge-warning">🟡 Sắp hết</span>
                                    @else
                                        <span class="badge badge-success">🟢 Ổn định</span>
                                    @endif
                                </td>
                                <td style="text-align:center;">
                                    <!-- Toggle Restock Form -->
                                    <button class="btn btn-outline btn-sm"
                                            onclick="toggleRestockForm({{ $item->id }})"
                                            id="restock-btn-{{ $item->id }}">
                                        📥 Nhập hàng
                                    </button>

                                    <!-- Restock inline form -->
                                    <div class="restock-form" id="restock-form-{{ $item->id }}">
                                        <form method="POST"
                                              action="{{ route('admin.inventory.restock', $item->id) }}"
                                              onsubmit="this.querySelector('button[type=submit]').classList.add('btn-loading'); this.querySelector('button[type=submit]').disabled=true;">
                                            @csrf
                                            <div style="font-size:.72rem; color:var(--gold); font-weight:700; margin-bottom:8px; text-transform:uppercase; letter-spacing:.06em;">
                                                Nhập thêm vào kho
                                            </div>
                                            <div class="restock-row">
                                                <span style="font-size:.82rem; color:var(--text-muted);">+</span>
                                                <input type="number" name="restock_amount"
                                                       value="10" min="1" max="9999"
                                                       placeholder="Số lượng"
                                                       title="Số lượng cần nhập thêm">
                                                <span style="font-size:.78rem; color:var(--text-dim);">đơn vị</span>
                                                <button type="submit" class="btn btn-primary btn-sm">✓ Nhập</button>
                                                <button type="button" class="btn btn-ghost btn-sm"
                                                        onclick="toggleRestockForm({{ $item->id }})">✕</button>
                                            </div>
                                            <div style="font-size:.72rem; color:var(--text-dim); margin-top:6px;">
                                                Tồn kho hiện tại: <strong style="color:var(--text-white);">{{ $qty }}</strong> → sẽ thành: <strong style="color:var(--green);" id="new-qty-preview-{{ $item->id }}">{{ $qty + 10 }}</strong>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align:center; color:var(--text-muted); padding:40px;">
                                    <div style="font-size:2rem; margin-bottom:10px; opacity:.4;">📦</div>
                                    Chưa có mặt hàng nào trong kho.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(count($inventoryAlerts) > 0)
                <!-- Bảng cảnh báo riêng bên dưới -->
                <div class="table-container" style="margin-top:24px;">
                    <div class="table-header">
                        <h3>⚠ Danh Sách Cần Nhập Gấp</h3>
                        <span class="badge badge-danger">{{ count($inventoryAlerts) }} mặt hàng</span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Vật Tư</th>
                                <th>Tồn Kho</th>
                                <th>Ngưỡng Min</th>
                                <th>Mức Thiếu</th>
                                <th>Cảnh Báo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventoryAlerts as $item)
                            @php
                                $qty  = $item->quantity ?? 0;
                                $min  = $item->min_threshold ?? 1;
                                $lack = max(0, $min - $qty);
                            @endphp
                            <tr class="{{ $qty == 0 ? 'row-critical' : '' }}">
                                <td><strong style="color:var(--text-white);">{{ $item->name ?? $item->item_name ?? '(Không tên)' }}</strong></td>
                                <td style="color:{{ $qty == 0 ? 'var(--red)' : 'var(--amber)' }}; font-weight:700; font-family:var(--font-display);">{{ $qty }}</td>
                                <td class="td-muted">{{ $min }}</td>
                                <td>
                                    @if($lack > 0)
                                        <span style="color:var(--red); font-weight:600;">-{{ $lack }}</span>
                                    @else
                                        <span class="td-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($qty == 0)
                                        <span class="badge badge-danger">🔴 Hết hàng — Nhập ngay!</span>
                                    @else
                                        <span class="badge badge-warning">🟡 Sắp hết — Cần nhập thêm</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

            </div><!-- /tab-inventory -->

            <!-- ══════════════════════════════════════
                 TAB 4: NHÂN SỰ (Staff)
            ══════════════════════════════════════ -->
            <div class="tab-panel" id="tab-staff">
                <div class="admin-two-col">

                    <!-- Form cấp tài khoản Barber -->
                    <div class="card">
                        <div class="card-title">✦ CẤP TÀI KHOẢN BARBER</div>
                        <form method="POST" action="/admin/add-staff" novalidate
                              onsubmit="document.getElementById('add-staff-btn').classList.add('btn-loading'); document.getElementById('add-staff-btn').disabled=true;">
                            @csrf
                            <div class="form-group">
                                <label for="staff-name">Tên thợ</label>
                                <div class="input-icon-wrap">
                                    <span class="input-icon">✂</span>
                                    <input type="text" id="staff-name" name="name"
                                           value="{{ old('name') }}"
                                           placeholder="Họ tên thợ barber" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="staff-email">Email đăng nhập</label>
                                <div class="input-icon-wrap">
                                    <span class="input-icon">✉</span>
                                    <input type="email" id="staff-email" name="email"
                                           value="{{ old('email') }}"
                                           placeholder="barber@salon.com" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="staff-password">Mật khẩu khởi tạo</label>
                                <div class="input-icon-wrap">
                                    <span class="input-icon">🔒</span>
                                    <input type="password" id="staff-password" name="password"
                                           placeholder="Tối thiểu 6 ký tự" required>
                                </div>
                            </div>
                            <button type="submit" id="add-staff-btn" class="btn btn-primary btn-full">
                                + Cấp Tài Khoản Barber
                            </button>
                        </form>
                    </div>

                    <!-- Danh sách barbers — bảng đầy đủ + nút xóa -->
                    <div class="table-container">
                        <div class="table-header">
                            <h3>DANH SÁCH BARBERS</h3>
                            <span class="badge badge-gold">{{ count($staffs) }} người</span>
                        </div>
                        @if(count($staffs) > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên Barber</th>
                                    <th>Email</th>
                                    <th>Ngày tham gia</th>
                                    <th>Vai Trò</th>
                                    <th style="text-align:center;">Thảo Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staffs as $i => $s)
                                <tr>
                                    <td class="td-muted">{{ $i + 1 }}</td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <div class="staff-avatar-sm">✂️</div>
                                            <strong style="color:var(--text-white);">{{ $s->name }}</strong>
                                        </div>
                                    </td>
                                    <td class="td-muted">{{ $s->email }}</td>
                                    <td class="td-muted">
                                        @if($s->created_at)
                                            {{ \Carbon\Carbon::parse($s->created_at)->format('d/m/Y') }}
                                        @else
                                            <span style="color:var(--text-dim);">—</span>
                                        @endif
                                    </td>
                                    <td><span class="badge badge-gold">Barber</span></td>
                                    <td style="text-align:center;">
                                        <button class="btn btn-danger btn-sm"
                                                onclick="openDeleteStaffModal({{ $s->id }}, '{{ addslashes($s->name) }}')"
                                                title="Xóa barber này">
                                            ✕ Xóa
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div style="padding:48px; text-align:center; color:var(--text-muted);">
                            <div style="font-size:2.5rem; margin-bottom:12px; opacity:.4;">✂</div>
                            Chưa có barber nào. Thêm barber đầu tiên!
                        </div>
                        @endif
                    </div>

                </div>
            </div><!-- /tab-staff -->

        </div><!-- /content-area -->
    </div><!-- /main-content -->
</div><!-- /dashboard-layout -->

<!-- ══ Modal xác nhận xóa dịch vụ ══ -->
<div class="modal-overlay" id="delete-svc-modal" role="dialog" aria-modal="true">
    <div class="modal-box">
        <div class="modal-icon">🗑️</div>
        <h3>Xác Nhận Xóa Dịch Vụ</h3>
        <p>Bạn có chắc muốn xóa dịch vụ<br>
           <strong id="delete-svc-name" style="color:var(--gold);"></strong> không?<br>
           <span style="font-size:.82rem; color:var(--red);">Lưu ý: Không thể xóa nếu còn lịch hẹn đang chờ!</span>
        </p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeDeleteModal()">Hủy bỏ</button>
            <form id="delete-svc-form" method="POST" action="" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-danger">🗑 Xác nhận xóa</button>
            </form>
        </div>
    </div>
</div>

<!-- ══ Modal xác nhận xóa Barber ══ -->
<div class="modal-overlay" id="delete-staff-modal" role="dialog" aria-modal="true">
    <div class="modal-box">
        <div class="modal-icon">⚠️</div>
        <h3>Xác Nhận Xóa Barber</h3>
        <p>Bạn có chắc muốn xóa barber<br>
           <strong id="delete-staff-name" style="color:var(--gold);"></strong> khỏi hệ thống?<br>
           <span style="font-size:.82rem; color:var(--red);">Không thể xóa nếu còn lịch hẹn đang chờ.<br>Thao tác này không thể hoàn tác!</span>
        </p>
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="closeDeleteStaffModal()">Hủy bỏ</button>
            <form id="delete-staff-form" method="POST" action="" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-danger">✕ Xác nhận xóa</button>
            </form>
        </div>
    </div>
</div>

<!-- ══ Partial: Modal Đổi Mật Khẩu ══ -->
@include('partials.change_password_modal')

<style>
@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(224,82,82,.4); }
    50%       { box-shadow: 0 0 0 6px rgba(224,82,82,0); }
}
@keyframes alertIn {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>

<script>
// ══ Tab switching ══
function switchTab(tabId) {
    // Panels
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');

    // Top bar buttons
    document.querySelectorAll('.admin-tab').forEach(btn => btn.classList.remove('active'));
    const btnMap = {
        'tab-overview':  'btn-tab-overview',
        'tab-services':  'btn-tab-services',
        'tab-inventory': 'btn-tab-inventory',
        'tab-staff':     'btn-tab-staff',
    };
    if (btnMap[tabId]) document.getElementById(btnMap[tabId]).classList.add('active');

    // Sidebar links
    document.querySelectorAll('.sidebar-tab-link').forEach(a => a.classList.remove('active'));
    const sideLink = document.querySelector(`.sidebar-tab-link[data-tab="${tabId}"]`);
    if (sideLink) sideLink.classList.add('active');

    // Update URL hash without scroll
    history.replaceState(null, '', '#' + tabId);
}

// Sidebar tab links
document.querySelectorAll('.sidebar-tab-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        switchTab(this.dataset.tab);
        // Close sidebar on mobile
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebar-overlay').classList.remove('active');
    });
});

// Restore tab from URL hash
window.addEventListener('DOMContentLoaded', () => {
    const hash = window.location.hash.replace('#', '');
    const validTabs = ['tab-overview', 'tab-services', 'tab-inventory', 'tab-staff'];
    if (validTabs.includes(hash)) {
        switchTab(hash);
    }

    // If session has error/success about services → go to services tab
    @if(session('success') || session('error'))
        @php
            $msg = session('success') ?? session('error') ?? '';
        @endphp
        @if(str_contains($msg, 'dịch vụ') || str_contains($msg, 'Dịch vụ'))
            switchTab('tab-services');
        @elseif(str_contains($msg, 'kho') || str_contains($msg, 'nhập thêm'))
            switchTab('tab-inventory');
        @elseif(str_contains($msg, 'Barber') || str_contains($msg, 'barber'))
            switchTab('tab-staff');
        @endif
    @endif

    // Animate stat values
    document.querySelectorAll('.stat-value').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(10px)';
        setTimeout(() => {
            el.style.transition = 'opacity .5s ease, transform .5s ease';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 200);
    });
});

// ══ Service edit row toggle ══
function toggleEditRow(id) {
    const row = document.getElementById('edit-row-' + id);
    const btn = document.getElementById('edit-btn-' + id);
    const isOpen = row.style.display !== 'none';
    row.style.display = isOpen ? 'none' : 'table-row';
    btn.textContent = isOpen ? '✏ Sửa' : '✕ Đóng';
    btn.classList.toggle('btn-ghost', !isOpen);
    btn.classList.toggle('btn-outline', isOpen);
}

// ══ Delete service modal ══
function openDeleteModal(id, name) {
    document.getElementById('delete-svc-name').textContent = '"' + name + '"';
    document.getElementById('delete-svc-form').action = '/admin/services/' + id + '/delete';
    document.getElementById('delete-svc-modal').classList.add('active');
}
function closeDeleteModal() {
    document.getElementById('delete-svc-modal').classList.remove('active');
}
document.getElementById('delete-svc-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

// ══ Delete staff modal ══
function openDeleteStaffModal(id, name) {
    document.getElementById('delete-staff-name').textContent = '"' + name + '"';
    document.getElementById('delete-staff-form').action = '/admin/staff/' + id + '/delete';
    document.getElementById('delete-staff-modal').classList.add('active');
}
function closeDeleteStaffModal() {
    document.getElementById('delete-staff-modal').classList.remove('active');
}
document.getElementById('delete-staff-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteStaffModal();
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeDeleteModal(); closeDeleteStaffModal(); }
});

// ══ Restock form toggle + live preview ══
function toggleRestockForm(id) {
    const form = document.getElementById('restock-form-' + id);
    form.classList.toggle('open');
    // Scroll into view
    if (form.classList.contains('open')) {
        form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

// Live preview of new quantity
document.querySelectorAll('[id^="restock-form-"]').forEach(formWrapper => {
    const id = formWrapper.id.replace('restock-form-', '');
    const input = formWrapper.querySelector('input[name="restock_amount"]');
    const preview = document.getElementById('new-qty-preview-' + id);
    if (!input || !preview) return;

    const currentQty = parseInt(preview.textContent) - parseInt(input.value);
    input.addEventListener('input', function() {
        const add = parseInt(this.value) || 0;
        preview.textContent = currentQty + add;
    });
});

// ══ Sidebar mobile ══
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