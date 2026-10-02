{{--
    ╔═══════════════════════════════════════════════════════════╗
    ║  PARTIAL: Modal Đổi Mật Khẩu — dùng chung cho mọi role  ║
    ║  Include: @include('partials.change_password_modal')      ║
    ╚═══════════════════════════════════════════════════════════╝

    Cách dùng:
    1. @include('partials.change_password_modal') trước </body>
    2. Thêm nút trigger: onclick="openChangePwModal()"
    3. Route: POST /change-password (name: change.password)
--}}

<!-- ══ Modal Đổi Mật Khẩu ══ -->
<div class="modal-overlay" id="change-pw-modal" role="dialog" aria-modal="true" aria-labelledby="change-pw-title">
    <div class="modal-box" style="max-width:460px; text-align:left;">

        <!-- Header -->
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:40px; height:40px; border-radius:50%; background:var(--gold-dim);
                            border:2px solid var(--gold-border); display:flex; align-items:center;
                            justify-content:center; font-size:1.2rem;">🔑</div>
                <div>
                    <h3 id="change-pw-title" style="font-size:1.1rem; font-family:var(--font-display); color:var(--text-white); margin:0;">
                        Đổi Mật Khẩu
                    </h3>
                    <p style="font-size:.75rem; color:var(--text-muted); margin:0;">
                        Tài khoản: <strong style="color:var(--gold);">{{ session('name') }}</strong>
                    </p>
                </div>
            </div>
            <button onclick="closeChangePwModal()"
                    style="background:none; border:none; cursor:pointer; color:var(--text-dim);
                           font-size:1.3rem; padding:4px; line-height:1; transition:color .2s;"
                    onmouseover="this.style.color='var(--text-white)'"
                    onmouseout="this.style.color='var(--text-dim)'"
                    aria-label="Đóng">✕</button>
        </div>

        <!-- Flash: pw_error / pw_success -->
        @if(session('pw_error'))
            <div class="alert alert-error" role="alert" style="margin-bottom:16px;">
                <span class="alert-icon">✕</span>
                <span>{{ session('pw_error') }}</span>
            </div>
        @endif
        @if(session('pw_success'))
            <div class="alert alert-success" role="alert" style="margin-bottom:16px;">
                <span class="alert-icon">✓</span>
                <span>{{ session('pw_success') }}</span>
            </div>
        @endif

        <!-- Validation errors (from redirect back) -->
        @if($errors->hasAny(['old_password','new_password','confirm_password']))
            <div class="alert alert-error" role="alert" style="margin-bottom:16px;">
                <span class="alert-icon">✕</span>
                <ul style="margin:0; padding-left:16px;">
                    @foreach(['old_password','new_password','confirm_password'] as $field)
                        @if($errors->has($field))
                            <li style="list-style:disc;">{{ $errors->first($field) }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Gold divider -->
        <div style="width:100%; height:1px; background:linear-gradient(90deg,transparent,var(--gold-border),transparent); margin-bottom:20px;"></div>

        <!-- Form -->
        <form method="POST" action="{{ route('change.password') }}" id="change-pw-form" novalidate>
            @csrf

            <div class="form-group">
                <label for="cpw-old">Mật khẩu hiện tại</label>
                <div class="input-icon-wrap">
                    <span class="input-icon">🔒</span>
                    <input type="password" id="cpw-old" name="old_password"
                           placeholder="Nhập mật khẩu đang dùng"
                           autocomplete="current-password" required>
                    <button type="button" class="cpw-toggle-btn"
                            onclick="toggleCpwField('cpw-old')" aria-label="Hiện/ẩn">👁</button>
                </div>
            </div>

            <div class="form-group">
                <label for="cpw-new">Mật khẩu mới</label>
                <div class="input-icon-wrap">
                    <span class="input-icon">🔑</span>
                    <input type="password" id="cpw-new" name="new_password"
                           placeholder="Tối thiểu 6 ký tự"
                           autocomplete="new-password" required
                           oninput="updateCpwStrength(this.value); checkCpwMatch()">
                    <button type="button" class="cpw-toggle-btn"
                            onclick="toggleCpwField('cpw-new')" aria-label="Hiện/ẩn">👁</button>
                </div>
                <!-- Strength bar -->
                <div style="height:3px; border-radius:2px; margin-top:6px; background:var(--border); overflow:hidden;">
                    <div id="cpw-strength-bar" style="height:100%; width:0%; transition:width .3s, background .3s;"></div>
                </div>
                <span id="cpw-strength-label" style="font-size:.7rem; color:var(--text-dim); margin-top:3px; display:block;"></span>
            </div>

            <div class="form-group">
                <label for="cpw-confirm">Xác nhận mật khẩu mới</label>
                <div class="input-icon-wrap">
                    <span class="input-icon">🔐</span>
                    <input type="password" id="cpw-confirm" name="confirm_password"
                           placeholder="Nhập lại mật khẩu mới"
                           autocomplete="new-password" required
                           oninput="checkCpwMatch()">
                    <button type="button" class="cpw-toggle-btn"
                            onclick="toggleCpwField('cpw-confirm')" aria-label="Hiện/ẩn">👁</button>
                </div>
                <span id="cpw-match-label" style="font-size:.7rem; margin-top:3px; display:none;"></span>
            </div>

            <!-- Requirements hint -->
            <div style="padding:12px 14px; background:var(--bg-elevated); border-radius:var(--radius-md);
                        font-size:.76rem; color:var(--text-muted); margin-bottom:18px; line-height:1.65;
                        border:1px solid var(--border);">
                <div style="font-weight:700; color:var(--text-white); margin-bottom:4px;">Yêu cầu mật khẩu:</div>
                <div id="req-len"  class="pw-req">○ Ít nhất 6 ký tự</div>
                <div id="req-diff" class="pw-req">○ Khác mật khẩu hiện tại</div>
                <div id="req-match" class="pw-req">○ Xác nhận khớp nhau</div>
            </div>

            <div style="display:flex; gap:12px;">
                <button type="button" class="btn btn-ghost" style="flex:1;" onclick="closeChangePwModal()">
                    Hủy
                </button>
                <button type="submit" id="cpw-submit-btn" class="btn btn-primary" style="flex:2;"
                        onclick="this.classList.add('btn-loading'); this.disabled=true; this.form.submit();">
                    ✓ Đổi Mật Khẩu
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Toggle btn inside input */
.cpw-toggle-btn {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--text-dim);
    font-size: .85rem;
    padding: 0;
    transition: color .2s;
}
.cpw-toggle-btn:hover { color: var(--gold); }

/* Requirement items */
.pw-req { display: flex; align-items: center; gap: 6px; transition: color .2s; }
.pw-req.ok   { color: var(--green); }
.pw-req.ok::before   { content: '✓ '; color: var(--green); }
.pw-req.fail { color: var(--text-dim); }
</style>

<script>
// ── Open / Close ──
function openChangePwModal() {
    document.getElementById('change-pw-modal').classList.add('active');
    setTimeout(() => document.getElementById('cpw-old').focus(), 300);
}
function closeChangePwModal() {
    document.getElementById('change-pw-modal').classList.remove('active');
    document.getElementById('change-pw-form').reset();
    resetCpwUI();
}
document.getElementById('change-pw-modal').addEventListener('click', function(e) {
    if (e.target === this) closeChangePwModal();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && document.getElementById('change-pw-modal').classList.contains('active')) {
        closeChangePwModal();
    }
});

// ── Toggle show/hide password ──
function toggleCpwField(id) {
    const el = document.getElementById(id);
    el.type  = el.type === 'password' ? 'text' : 'password';
}

// ── Strength bar ──
function updateCpwStrength(v) {
    let score = 0;
    if (v.length >= 6)  score++;
    if (v.length >= 10) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^a-zA-Z0-9]/.test(v)) score++;

    const colors = ['','#E05252','#F5A623','#F5D020','#2ECC8A','#00C9A7'];
    const labels = ['','Rất yếu','Yếu','Trung bình','Mạnh','Rất mạnh'];
    const widths = ['0%','20%','40%','60%','80%','100%'];

    document.getElementById('cpw-strength-bar').style.width      = widths[score];
    document.getElementById('cpw-strength-bar').style.background = colors[score];
    document.getElementById('cpw-strength-label').textContent    = labels[score];
    document.getElementById('cpw-strength-label').style.color    = colors[score];

    // Requirements
    const reqLen = document.getElementById('req-len');
    reqLen.className = 'pw-req ' + (v.length >= 6 ? 'ok' : 'fail');
    reqLen.textContent = (v.length >= 6 ? '✓' : '○') + ' Ít nhất 6 ký tự';
}

// ── Match check ──
function checkCpwMatch() {
    const nv  = document.getElementById('cpw-new').value;
    const cv  = document.getElementById('cpw-confirm').value;
    const lbl = document.getElementById('cpw-match-label');
    const reqMatch = document.getElementById('req-match');

    if (!cv) { lbl.style.display = 'none'; return; }

    const match = nv === cv;
    lbl.style.display  = 'block';
    lbl.textContent    = match ? '✓ Mật khẩu khớp' : '✕ Chưa khớp';
    lbl.style.color    = match ? 'var(--green)' : 'var(--red)';
    reqMatch.className = 'pw-req ' + (match ? 'ok' : 'fail');
    reqMatch.textContent = (match ? '✓' : '○') + ' Xác nhận khớp nhau';
}

// ── Reset UI ──
function resetCpwUI() {
    ['cpw-strength-bar'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.style.width = '0%'; }
    });
    const sl = document.getElementById('cpw-strength-label');
    const ml = document.getElementById('cpw-match-label');
    if (sl) sl.textContent = '';
    if (ml) { ml.style.display = 'none'; ml.textContent = ''; }
    ['req-len','req-diff','req-match'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.className = 'pw-req';
    });
}

// Auto-open if redirected back with pw_error / pw_success or pw validation errors
document.addEventListener('DOMContentLoaded', () => {
    @if(session('pw_error') || session('pw_success') || $errors->hasAny(['old_password','new_password','confirm_password']))
        openChangePwModal();
    @endif
});
</script>
