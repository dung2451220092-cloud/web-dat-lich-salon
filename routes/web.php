<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SalonController;

// ─────────────────────────────────────────────
// 1. Trang chủ — truyền thêm avg_rating cho mỗi barber
// ─────────────────────────────────────────────
Route::get('/', function () {
    $services = DB::table('services')->orderBy('price', 'asc')->get();

    // Lấy danh sách barber kèm điểm đánh giá trung bình từ bảng reviews
    $barbers = DB::table('users')
        ->where('users.role', 'staff')
        ->leftJoin('reviews', 'users.id', '=', 'reviews.staff_id')
        ->select(
            'users.id',
            'users.name',
            DB::raw('ROUND(AVG(reviews.rating), 1) as avg_rating'),
            DB::raw('COUNT(reviews.id) as review_count')
        )
        ->groupBy('users.id', 'users.name')
        ->get();

    return view('home', compact('services', 'barbers'));
});

// ─────────────────────────────────────────────
// 2. Xác thực
// ─────────────────────────────────────────────
Route::get('/login',     [SalonController::class, 'showLogin'])->name('login');
Route::post('/login',    [SalonController::class, 'login']);
Route::get('/register',  [SalonController::class, 'showRegister']);
Route::post('/register', [SalonController::class, 'register']);
// Đăng xuất dùng POST để bảo vệ CSRF
Route::post('/logout',   [SalonController::class, 'logout'])->name('logout');
Route::get('/logout',    [SalonController::class, 'logout']); // tương thích link cũ

// ─────────────────────────────────────────────
// 3. Quyền Admin
// ─────────────────────────────────────────────
Route::get('/admin',                          [SalonController::class, 'adminDashboard']);
Route::post('/admin/add-staff',               [SalonController::class, 'addStaff']);

// ── Quản lý Dịch vụ (Services CRUD) ──
Route::post('/admin/services',                [SalonController::class, 'addService'])->name('admin.services.add');
Route::post('/admin/services/{id}/update',    [SalonController::class, 'updateService'])->name('admin.services.update');
Route::post('/admin/services/{id}/delete',    [SalonController::class, 'deleteService'])->name('admin.services.delete');

// ── Quản lý Kho (Inventory) ──
Route::post('/admin/inventory/{id}/restock',  [SalonController::class, 'restockInventory'])->name('admin.inventory.restock');

// ── Quản lý Nhân sự ──
Route::post('/admin/staff/{id}/delete',       [SalonController::class, 'deleteStaff'])->name('admin.staff.delete');

// ─────────────────────────────────────────────
// 4. Quyền Barber (Staff)
// ─────────────────────────────────────────────
Route::get('/staff',                [SalonController::class, 'staffDashboard']);
Route::post('/staff/complete/{id}', [SalonController::class, 'completeAppointment']);

// ─────────────────────────────────────────────
// 5. Quyền Khách hàng (Customer)
// ─────────────────────────────────────────────
Route::get('/customer',                         [SalonController::class, 'customerDashboard']);
Route::post('/customer/book',                   [SalonController::class, 'bookAppointment']);
Route::post('/customer/cancel/{id}',            [SalonController::class, 'cancelAppointment'])->name('customer.cancel');

// Trang QR thanh toán (sau khi đặt lịch chọn QR)
Route::get('/customer/payment-qr',              [SalonController::class, 'paymentQr'])->name('customer.payment-qr');

// Gửi đánh giá cho một lịch hẹn đã hoàn thành
Route::post('/customer/review/{appointmentId}', [SalonController::class, 'submitReview'])->name('customer.review');

// ─────────────────────────────────────────────
// 6. Đổi mật khẩu (dùng chung cho mọi role đang đăng nhập)
// ─────────────────────────────────────────────
Route::post('/change-password',                 [SalonController::class, 'changePassword'])->name('change.password');