<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="THE BARBER CLUB — Hệ thống đặt lịch cắt tóc & quản lý salon chuyên nghiệp. Trải nghiệm dịch vụ barbershop đẳng cấp.">
    <meta name="keywords" content="salon tóc, cắt tóc nam, barber, đặt lịch cắt tóc, the barber club">
    <title>THE BARBER CLUB — Classic & Premium Barbershop</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <!-- ══ Header ══ -->
    <header class="home-header" id="site-header">
        <div class="home-logo brand-font">⚔ THE <span>BARBER</span> CLUB</div>
        <button class="menu-toggle" id="menu-toggle" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
        <nav class="home-nav" id="main-nav">
            <a href="#services">Dịch Vụ</a>
            <a href="#barbers">Barbers</a>
            <a href="#contact">Liên hệ</a>
            <a href="/login" class="btn btn-ghost btn-sm">Đăng nhập</a>
            <a href="/register" class="btn btn-primary btn-sm">✂ Đặt Lịch Ngay</a>
        </nav>
    </header>

    <!-- ══ Hero Banner ══ -->
    <section class="hero-banner" id="home">
        <div class="hero-eyebrow">✦ Classic &amp; Premium Barbershop ✦</div>
        <h1>ĐẲNG CẤP <em>TẠO NÊN</em><br>KHÁC BIỆT</h1>
        <p>Hệ thống đặt lịch cắt tóc chuyên nghiệp hàng đầu. Trải nghiệm dịch vụ barbershop đẳng cấp, phong cách quý ông lịch lãm ngay hôm nay.</p>
        <div class="hero-actions">
            <a href="/register" class="btn btn-primary btn-lg">✂ Đặt Lịch Hẹn Ngay</a>
            <a href="#services"  class="btn btn-outline btn-lg">Xem Dịch Vụ</a>
        </div>
        <div class="hero-divider"></div>

        <!-- Floating stats bar -->
        <div style="display:flex; gap:48px; margin-top:56px; flex-wrap:wrap; justify-content:center; animation: fadeInDown .8s .5s cubic-bezier(.4,0,.2,1) both;">
            <div style="text-align:center;">
                <div style="font-family:var(--font-display); font-size:1.8rem; font-weight:900; color:var(--gold);">500+</div>
                <div style="font-size:.72rem; letter-spacing:.12em; text-transform:uppercase; color:var(--text-muted); margin-top:2px;">Khách hàng</div>
            </div>
            <div style="width:1px; background:var(--border);"></div>
            <div style="text-align:center;">
                <div style="font-family:var(--font-display); font-size:1.8rem; font-weight:900; color:var(--gold);">{{ count($barbers) }}+</div>
                <div style="font-size:.72rem; letter-spacing:.12em; text-transform:uppercase; color:var(--text-muted); margin-top:2px;">Master Barbers</div>
            </div>
            <div style="width:1px; background:var(--border);"></div>
            <div style="text-align:center;">
                <div style="font-family:var(--font-display); font-size:1.8rem; font-weight:900; color:var(--gold);">{{ count($services) }}</div>
                <div style="font-size:.72rem; letter-spacing:.12em; text-transform:uppercase; color:var(--text-muted); margin-top:2px;">Dịch vụ</div>
            </div>
            <div style="width:1px; background:var(--border);"></div>
            <div style="text-align:center;">
                <div style="font-family:var(--font-display); font-size:1.8rem; font-weight:900; color:var(--gold);">4.9★</div>
                <div style="font-size:.72rem; letter-spacing:.12em; text-transform:uppercase; color:var(--text-muted); margin-top:2px;">Đánh giá</div>
            </div>
        </div>
    </section>

    <div class="gold-divider"></div>

    <!-- ══ Services Section ══ -->
    <section class="section" id="services">
        <p class="section-eyebrow">✦ Dịch vụ chăm sóc tóc chuyên nghiệp</p>
        <h2 class="section-title">BẢNG GIÁ DỊCH VỤ</h2>
        <div class="service-grid">
            @foreach ($services as $s)
            <div class="service-card">
                <div>
                    <!-- Service icon based on category -->
                    <div style="font-size:2rem; margin-bottom:14px; display:inline-block; padding:12px; background:var(--gold-dim); border-radius:12px; border:1px solid var(--gold-border);">
                        @php
                            $name = mb_strtolower($s->name);
                            if (str_contains($name, 'cắt')) $icon = '✂️';
                            elseif (str_contains($name, 'uốn')) $icon = '🔄';
                            elseif (str_contains($name, 'nhuộm')) $icon = '🎨';
                            elseif (str_contains($name, 'da') || str_contains($name, 'chăm')) $icon = '✨';
                            elseif (str_contains($name, 'râu') || str_contains($name, 'cạo')) $icon = '🪒';
                            elseif (str_contains($name, 'gội') || str_contains($name, 'sấy')) $icon = '💆';
                            else $icon = '💈';
                        @endphp
                        {{ $icon }}
                    </div>
                    <h3>{{ $s->name }}</h3>
                    <p>Quy trình tiêu chuẩn: tư vấn phong cách, thực hiện chuyên nghiệp, hoàn thiện với sản phẩm cao cấp.</p>
                </div>
                <div>
                    <div class="service-price">{{ number_format($s->price, 0, ',', '.') }} ₫</div>
                    <a href="/register" class="btn btn-outline btn-sm btn-full">Chọn Dịch Vụ Này</a>
                </div>
            </div>
            @endforeach

            @if(count($services) === 0)
            <div style="grid-column:1/-1; text-align:center; padding:60px; color:var(--text-muted);">
                Đang cập nhật bảng dịch vụ…
            </div>
            @endif
        </div>
    </section>

    <div class="gold-divider"></div>

    <!-- ══ Barbers Section ══ -->
    <section class="section" id="barbers" style="padding-top:80px;">
        <p class="section-eyebrow">✦ Những người thợ tâm huyết</p>
        <h2 class="section-title">ĐỘI NGŨ MASTER BARBERS</h2>
        <div class="barber-grid">
            @foreach ($barbers as $b)
            <div class="barber-box">
                <div class="barber-avatar">✂️</div>
                <h4>{{ $b->name }}</h4>
                <span class="barber-title">Master Stylist</span>

                <!-- Rating display -->
                <div class="barber-rating">
                    @if($b->review_count > 0)
                        @php $stars = round($b->avg_rating); @endphp
                        <div class="stars-display">
                            @for($i = 1; $i <= 5; $i++)
                                <span style="color:{{ $i <= $stars ? 'var(--gold)' : 'var(--border)' }}; font-size:1.05rem;">★</span>
                            @endfor
                        </div>
                        <span style="font-size:.78rem; color:var(--text-muted); margin-top:4px; display:block;">
                            {{ number_format($b->avg_rating, 1) }}/5 &middot; {{ $b->review_count }} đánh giá
                        </span>
                    @else
                        <div class="stars-display">
                            @for($i = 1; $i <= 5; $i++)
                                <span style="color:var(--border); font-size:1.05rem;">★</span>
                            @endfor
                        </div>
                        <span style="font-size:.78rem; color:var(--text-dim); margin-top:4px; display:block;">Chưa có đánh giá</span>
                    @endif
                </div>

                <a href="/register" class="btn btn-outline btn-sm" style="margin-top:18px; width:100%;">
                    Chọn Barber Này
                </a>
            </div>
            @endforeach

            @if(count($barbers) === 0)
            <div style="grid-column:1/-1; text-align:center; padding:60px; color:var(--text-muted);">
                Đang cập nhật đội ngũ barber…
            </div>
            @endif
        </div>
    </section>

    <div class="gold-divider"></div>

    <!-- ══ Why Choose Us ══ -->
    <section class="section" style="padding-top:80px;">
        <p class="section-eyebrow">✦ Vì sao chọn chúng tôi</p>
        <h2 class="section-title">TRẢI NGHIỆM ĐẲNG CẤP</h2>
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:20px;">
            @foreach([
                ['🏆','Thợ Lành Nghề','Đội ngũ barber được đào tạo bài bản, kinh nghiệm nhiều năm.'],
                ['⚡','Đặt Lịch Nhanh','Chỉ vài bước đơn giản, lịch hẹn được xác nhận ngay tức thì.'],
                ['💎','Sản Phẩm Cao Cấp','Sử dụng 100% sản phẩm chăm sóc tóc nhập khẩu chính hãng.'],
                ['💳','Thanh Toán Linh Hoạt','Hỗ trợ tiền mặt hoặc chuyển khoản QR tiện lợi.'],
            ] as [$icon, $title, $desc])
            <div class="card" style="text-align:center; padding:32px 24px;">
                <div style="font-size:2.5rem; margin-bottom:16px; display:inline-block; padding:14px; background:var(--gold-dim); border-radius:14px; border:1px solid var(--gold-border);">{{ $icon }}</div>
                <h3 style="font-size:1rem; margin-bottom:10px; color:var(--text-white);">{{ $title }}</h3>
                <p style="font-size:.84rem; color:var(--text-muted); line-height:1.65;">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </section>

    <!-- ══ Footer ══ -->
    <footer class="home-footer" id="contact">
        <div class="footer-grid">
            <div class="footer-brand-col">
                <div class="footer-logo brand-font">⚔ THE BARBER CLUB</div>
                <p>Salon tóc nam đẳng cấp với phong cách Classic & Luxury. Chúng tôi tôn trọng từng đường kéo, từng nụ cười của quý khách.</p>
            </div>
            <div class="footer-col">
                <h5>Điều Hướng</h5>
                <ul>
                    <li><a href="#home">Trang Chủ</a></li>
                    <li><a href="#services">Dịch Vụ</a></li>
                    <li><a href="#barbers">Barbers</a></li>
                    <li><a href="/login">Đăng Nhập</a></li>
                    <li><a href="/register">Đặt Lịch</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Dịch Vụ</h5>
                <ul>
                    @foreach($services->take(5) as $s)
                    <li><a href="/register">{{ $s->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="footer-col">
                <h5>Liên Hệ</h5>
                <p>📍 123 Đường Lê Lợi, Q.1, TP.HCM</p>
                <p>📞 0901 234 567</p>
                <p>📧 contact@barberclub.vn</p>
                <p style="margin-top:12px; color:var(--gold);">⏰ Giờ hoạt động:</p>
                <p>Thứ 2 – Thứ 7: 8:00 – 21:00</p>
                <p>Chủ Nhật: 8:00 – 19:00</p>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© {{ date('Y') }} THE BARBER CLUB — Powered by Laravel</span>
            <span style="color:var(--gold);">✂ Classic &amp; Premium Barbershop</span>
        </div>
    </footer>

<script>
// ── Sticky Header on Scroll ──
const header = document.getElementById('site-header');
window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 50);
});

// ── Mobile Nav Toggle ──
const menuToggle = document.getElementById('menu-toggle');
const mainNav    = document.getElementById('main-nav');
menuToggle.addEventListener('click', () => {
    menuToggle.classList.toggle('open');
    mainNav.classList.toggle('open');
});

// Close nav when a link is clicked
mainNav.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
        menuToggle.classList.remove('open');
        mainNav.classList.remove('open');
    });
});

// ── Smooth reveal on scroll ──
const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.style.opacity = '1';
            e.target.style.transform = 'translateY(0)';
        }
    });
}, { threshold: 0.08 });

document.querySelectorAll('.service-card, .barber-box, .card').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(24px)';
    el.style.transition = 'opacity .5s ease, transform .5s ease';
    observer.observe(el);
});
</script>

</body>
</html>