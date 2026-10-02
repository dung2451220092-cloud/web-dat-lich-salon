<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SalonController extends Controller
{
    // =========================================================
    // HELPER: Kiểm tra quyền truy cập theo role
    // Nếu sai role → redirect /login kèm flash message lỗi
    // =========================================================
    private function requireRole(string $role)
    {
        if (session('role') !== $role) {
            return redirect('/login')->with('error', 'Bạn không có quyền truy cập trang này. Vui lòng đăng nhập.');
        }
        return null;
    }

    // 1. Hiển thị form đăng nhập & đăng ký
    public function showLogin()    { return view('login'); }
    public function showRegister() { return view('register'); }

    // =========================================================
    // 2. Xử lý Đăng ký (Khách hàng)
    // =========================================================
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        DB::table('users')->insert([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'customer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/login')->with('success', 'Đăng ký thành công! Hãy đăng nhập.');
    }

    // =========================================================
    // 3. Xử lý Đăng nhập & Phân quyền
    // =========================================================
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = DB::table('users')->where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            session([
                'user_id' => $user->id,
                'name'    => $user->name,
                'role'    => $user->role,
            ]);

            if ($user->role === 'admin') return redirect('/admin');
            if ($user->role === 'staff') return redirect('/staff');
            return redirect('/customer');
        }

        return back()->with('error', 'Email hoặc mật khẩu không chính xác.');
    }

    // 4. Đăng xuất
    public function logout()
    {
        session()->flush();
        return redirect('/login')->with('success', 'Đã đăng xuất thành công.');
    }

    // =========================================================
    // 5. Dashboard Admin
    // =========================================================
    public function adminDashboard()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $totalRevenue = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.status', 'completed')
            ->sum('services.price');

        $todayAppointments = DB::table('appointments')
            ->whereDate('booking_time', date('Y-m-d'))
            ->count();

        $inventoryAlerts = DB::table('inventory')
            ->whereColumn('quantity', '<=', 'min_threshold')
            ->get();

        $staffs = DB::table('users')->where('role', 'staff')->get();

        // Danh sách dịch vụ để quản lý
        $services = DB::table('services')->orderBy('name', 'asc')->get();

        // Toàn bộ kho (không lọc) để hiển thị bảng nhập kho
        $allInventory = DB::table('inventory')->orderBy('id', 'asc')->get();

        return view('admin', compact(
            'totalRevenue', 'todayAppointments',
            'inventoryAlerts', 'staffs',
            'services', 'allInventory'
        ));
    }

    // =========================================================
    // 6. Admin thêm Barber mới
    // =========================================================
    public function addStaff(Request $request)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        DB::table('users')->insert([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'staff',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Tạo tài khoản Barber thành công!');
    }

    // =========================================================
    // 6a-2. Admin xóa tài khoản Barber
    // =========================================================
    public function deleteStaff(Request $request, $id)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        // Không cho xóa chính mình
        if ((int)$id === (int)session('user_id')) {
            return back()->with('error', 'Không thể xóa tài khoản của chính bạn.');
        }

        $staff = DB::table('users')->where('id', $id)->where('role', 'staff')->first();

        if (!$staff) {
            return back()->with('error', 'Không tìm thấy barber cần xóa.');
        }

        // Kiểm tra còn lịch hẹn pending/confirmed chưa
        $pendingCount = DB::table('appointments')
            ->where('staff_id', $id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if ($pendingCount > 0) {
            return back()->with('error',
                "Không thể xóa: barber \"{$staff->name}\" còn {$pendingCount} lịch hẹn chưa hoàn thành."
            );
        }

        DB::table('users')->where('id', $id)->delete();

        return back()->with('success', "Đã xóa barber \"{$staff->name}\" khỏi hệ thống.");
    }

    // =========================================================
    // 6a-3. Đổi mật khẩu (dùng chung cho mọi role đang login)
    // =========================================================
    public function changePassword(Request $request)
    {
        // Bắt buộc phải đang đăng nhập
        if (!session('user_id')) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập.');
        }

        $request->validate([
            'old_password'     => 'required',
            'new_password'     => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ], [
            'old_password.required'     => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required'     => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min'          => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'confirm_password.required' => 'Vui lòng xác nhận mật khẩu mới.',
            'confirm_password.same'     => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user = DB::table('users')->where('id', session('user_id'))->first();

        if (!$user || !Hash::check($request->old_password, $user->password)) {
            return back()->with('pw_error', 'Mật khẩu hiện tại không chính xác.')->withFragment('change-pw');
        }

        // Không cho đặt lại mật khẩu giống cũ
        if (Hash::check($request->new_password, $user->password)) {
            return back()->with('pw_error', 'Mật khẩu mới phải khác mật khẩu hiện tại.')->withFragment('change-pw');
        }

        DB::table('users')->where('id', session('user_id'))->update([
            'password'   => Hash::make($request->new_password),
            'updated_at' => now(),
        ]);

        return back()->with('pw_success', 'Đổi mật khẩu thành công! Hãy đăng nhập lại nếu cần.')->withFragment('change-pw');
    }

    // =========================================================
    // 6b. Admin thêm Dịch vụ mới
    // =========================================================
    public function addService(Request $request)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        DB::table('services')->insert([
            'name'       => trim($request->name),
            'price'      => $request->price,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Đã thêm dịch vụ "' . trim($request->name) . '" thành công!');
    }

    // =========================================================
    // 6c. Admin sửa Dịch vụ (tên & giá)
    // =========================================================
    public function updateService(Request $request, $id)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $affected = DB::table('services')->where('id', $id)->update([
            'name'       => trim($request->name),
            'price'      => $request->price,
            'updated_at' => now(),
        ]);

        if (!$affected) {
            return back()->with('error', 'Không tìm thấy dịch vụ cần cập nhật.');
        }

        return back()->with('success', 'Đã cập nhật dịch vụ "' . trim($request->name) . '" thành công!');
    }

    // =========================================================
    // 6d. Admin xóa Dịch vụ
    // =========================================================
    public function deleteService(Request $request, $id)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        // Kiểm tra còn lịch hẹn chưa hoàn thành liên quan đến dịch vụ này
        $activeCount = DB::table('appointments')
            ->where('service_id', $id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if ($activeCount > 0) {
            return back()->with('error', "Không thể xóa: dịch vụ đang có {$activeCount} lịch hẹn chưa hoàn thành.");
        }

        $service = DB::table('services')->where('id', $id)->first();
        DB::table('services')->where('id', $id)->delete();

        $name = $service ? $service->name : "#$id";
        return back()->with('success', "Đã xóa dịch vụ \"{$name}\" thành công.");
    }

    // =========================================================
    // 6e. Admin nhập thêm hàng vào kho (Restock)
    // =========================================================
    public function restockInventory(Request $request, $id)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        $request->validate([
            'restock_amount' => 'required|integer|min:1|max:9999',
        ]);

        $item = DB::table('inventory')->where('id', $id)->first();

        if (!$item) {
            return back()->with('error', 'Không tìm thấy mặt hàng trong kho.');
        }

        DB::table('inventory')
            ->where('id', $id)
            ->increment('quantity', $request->restock_amount);

        $itemName = $item->name ?? $item->item_name ?? "#$id";
        $newQty   = $item->quantity + $request->restock_amount;

        return back()->with('success', "Đã nhập thêm {$request->restock_amount} đơn vị vào kho \"{$itemName}\". Tồn kho mới: {$newQty}.");
    }

    // =========================================================
    // 7. Bàn làm việc Barber (Staff)
    // Thêm cột payment_method, payment_status vào SELECT
    // =========================================================
    public function staffDashboard()
    {
        if ($redirect = $this->requireRole('staff')) return $redirect;

        // Alias appointments.id để tránh ghi đè khi JOIN nhiều bảng
        $appointments = DB::table('appointments')
            ->join('users',    'appointments.customer_id', '=', 'users.id')
            ->join('services', 'appointments.service_id',  '=', 'services.id')
            ->select(
                'appointments.id             as id',
                'appointments.status         as status',
                'appointments.booking_time   as booking_time',
                'appointments.payment_method as payment_method',
                'appointments.payment_status as payment_status',
                'users.name                  as customer_name',
                'services.name               as service_name',
                'services.price              as service_price'
            )
            ->where('appointments.staff_id', session('user_id'))
            ->orderBy('appointments.booking_time', 'asc')
            ->get();

        // Lấy danh sách kho - hỗ trợ cả cột `item_name` và `name`
        $inventoryRaw = DB::table('inventory')->get();

        // Chuẩn hóa: đảm bảo mỗi item đều có thuộc tính `display_name`
        $inventory = $inventoryRaw->map(function ($item) {
            $item->display_name = isset($item->item_name) && $item->item_name !== null
                ? $item->item_name
                : ($item->name ?? '(Không tên)');
            return $item;
        });

        return view('staff', compact('appointments', 'inventory'));
    }

    // =========================================================
    // 8. Barber xác nhận xong lịch & trừ kho vật tư
    // Cập nhật: đồng thời đánh dấu payment_status = 'paid'
    // =========================================================
    public function completeAppointment(Request $request, $id)
    {
        if ($redirect = $this->requireRole('staff')) return $redirect;

        // Xác nhận lịch hẹn thuộc về staff đang đăng nhập
        $appointment = DB::table('appointments')
            ->where('id', $id)
            ->where('staff_id', session('user_id'))
            ->first();

        if (!$appointment) {
            return back()->with('error', 'Không tìm thấy lịch hẹn hoặc bạn không có quyền thao tác.');
        }

        // Cập nhật trạng thái thành completed & đánh dấu đã thanh toán
        DB::table('appointments')->where('id', $id)->update([
            'status'         => 'completed',
            'payment_status' => 'paid',
            'updated_at'     => now(),
        ]);

        // Trừ kho - kiểm tra số lượng tồn kho trước để không để âm
        if ($request->item_id && $request->quantity_used > 0) {
            $item = DB::table('inventory')->where('id', $request->item_id)->first();

            if (!$item) {
                return back()->with('error', 'Vật tư không tồn tại trong kho.');
            }

            if ($item->quantity < $request->quantity_used) {
                return back()->with('error', "Tồn kho không đủ! Hiện còn {$item->quantity}, cần dùng {$request->quantity_used}.");
            }

            DB::table('inventory')
                ->where('id', $request->item_id)
                ->decrement('quantity', $request->quantity_used);
        }

        return back()->with('success', 'Đã hoàn thành lịch hẹn, cập nhật kho và đánh dấu đã thanh toán!');
    }

    // =========================================================
    // 9. Trang cá nhân Khách hàng (Customer)
    // Cập nhật: bỏ branch, thêm payment_method, payment_status,
    //           service_price (cho QR), và danh sách review đã có
    // =========================================================
    public function customerDashboard()
    {
        if ($redirect = $this->requireRole('customer')) return $redirect;

        $services = DB::table('services')->get();
        $barbers  = DB::table('users')->where('role', 'staff')->get();

        // Lấy lịch hẹn — alias id để tránh ghi đè khi JOIN
        // Bỏ JOIN branches vì tiệm 1 địa chỉ cố định
        $myAppointments = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->join('users as staff_user', 'appointments.staff_id', '=', 'staff_user.id')
            ->select(
                'appointments.id             as id',
                'appointments.status         as status',
                'appointments.booking_time   as booking_time',
                'appointments.payment_method as payment_method',
                'appointments.payment_status as payment_status',
                'services.name               as service_name',
                'services.price              as service_price',
                'staff_user.name             as staff_name'
            )
            ->where('appointments.customer_id', session('user_id'))
            ->orderBy('appointments.booking_time', 'desc')
            ->get();

        // Lấy danh sách appointment_id đã được đánh giá bởi khách này
        $reviewedIds = DB::table('reviews')
            ->where('customer_id', session('user_id'))
            ->pluck('appointment_id')
            ->toArray();

        return view('customer', compact('services', 'barbers', 'myAppointments', 'reviewedIds'));
    }

    // =========================================================
    // 10. Khách hàng đặt lịch hẹn
    // Cập nhật: bỏ branch_id, thêm payment_method
    // =========================================================
    public function bookAppointment(Request $request)
    {
        if ($redirect = $this->requireRole('customer')) return $redirect;

        // --- Validate dữ liệu đầu vào ---
        $request->validate([
            'service_id'     => 'required|integer|exists:services,id',
            'staff_id'       => 'required|integer|exists:users,id',
            'booking_time'   => 'required|date',
            'payment_method' => 'required|in:cash,qr',
        ]);

        // Chuẩn hóa định dạng thời gian từ input datetime-local
        $bookingTime = str_replace('T', ' ', $request->booking_time);

        // --- Không cho phép chọn thời gian trong quá khứ ---
        if (strtotime($bookingTime) < time()) {
            return back()
                ->withInput()
                ->with('error', 'Thời gian đặt lịch không được ở quá khứ. Vui lòng chọn lại.');
        }

        // --- Chặn trùng lịch thợ (+/- 30 phút) ---
        $conflictExists = DB::table('appointments')
            ->where('staff_id', $request->staff_id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereBetween('booking_time', [
                date('Y-m-d H:i:s', strtotime($bookingTime) - 30 * 60),
                date('Y-m-d H:i:s', strtotime($bookingTime) + 30 * 60),
            ])
            ->exists();

        if ($conflictExists) {
            return back()
                ->withInput()
                ->with('error', 'Thợ này đã có lịch trong khung giờ bạn chọn (±30 phút). Vui lòng chọn thợ hoặc giờ khác.');
        }

        // --- Lưu lịch hẹn ---
        // branch_id = null (tiệm 1 địa chỉ duy nhất, không cần chọn)
        // payment_status mặc định là 'unpaid', sẽ cập nhật thành 'paid' khi barber hoàn thành
        $newId = DB::table('appointments')->insertGetId([
            'customer_id'    => session('user_id'),
            'branch_id'      => null,
            'service_id'     => $request->service_id,
            'staff_id'       => $request->staff_id,
            'booking_time'   => $bookingTime,
            'status'         => 'pending',
            'payment_method' => $request->payment_method,
            'payment_status' => 'unpaid',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // Lấy giá dịch vụ để hiển thị QR nếu cần
        $service = DB::table('services')->where('id', $request->service_id)->first();

        if ($request->payment_method === 'qr') {
            // Chuyển hướng sang trang QR, truyền thông tin cần thiết qua session
            session([
                'qr_appointment_id' => $newId,
                'qr_amount'         => $service ? intval($service->price) : 0,
            ]);
            return redirect('/customer/payment-qr');
        }

        return back()->with('success', 'Đặt lịch thành công! Vui lòng đến đúng giờ và thanh toán tại quầy.');
    }

    // =========================================================
    // 10b. Trang QR thanh toán sau khi đặt lịch
    // =========================================================
    public function paymentQr()
    {
        if ($redirect = $this->requireRole('customer')) return $redirect;

        $appointmentId = session('qr_appointment_id');
        $amount        = session('qr_amount', 0);

        if (!$appointmentId) {
            return redirect('/customer')->with('error', 'Không tìm thấy thông tin lịch hẹn cần thanh toán.');
        }

        return view('payment_qr', compact('appointmentId', 'amount'));
    }

    // =========================================================
    // 11. Khách hủy lịch (chỉ hủy được khi status = pending)
    // =========================================================
    public function cancelAppointment(Request $request, $id)
    {
        if ($redirect = $this->requireRole('customer')) return $redirect;

        $deleted = DB::table('appointments')
            ->where('id', $id)
            ->where('customer_id', session('user_id'))
            ->where('status', 'pending')
            ->delete();

        if ($deleted) {
            return back()->with('success', 'Đã hủy lịch hẹn thành công.');
        }

        return back()->with('error', 'Không thể hủy lịch này (lịch đã được xác nhận hoặc hoàn thành).');
    }

    // =========================================================
    // 12. Khách gửi đánh giá (Rating 1-5 sao)
    // Lưu vào bảng `reviews`
    // Cấu trúc bảng gợi ý:
    //   reviews(id, appointment_id, customer_id, staff_id, rating, comment, created_at)
    // =========================================================
    public function submitReview(Request $request, $appointmentId)
    {
        if ($redirect = $this->requireRole('customer')) return $redirect;

        // Validate
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        // Xác nhận lịch hẹn thuộc về khách hàng và đã hoàn thành
        $appointment = DB::table('appointments')
            ->where('id', $appointmentId)
            ->where('customer_id', session('user_id'))
            ->where('status', 'completed')
            ->first();

        if (!$appointment) {
            return back()->with('error', 'Không tìm thấy lịch hẹn hoặc lịch chưa hoàn thành.');
        }

        // Kiểm tra đã đánh giá chưa (tránh duplicate)
        $alreadyReviewed = DB::table('reviews')
            ->where('appointment_id', $appointmentId)
            ->where('customer_id', session('user_id'))
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'Bạn đã đánh giá lịch hẹn này rồi.');
        }

        // Lưu đánh giá
        DB::table('reviews')->insert([
            'appointment_id' => $appointmentId,
            'customer_id'    => session('user_id'),
            'staff_id'       => $appointment->staff_id,
            'rating'         => $request->rating,
            'comment'        => $request->comment,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá! Phản hồi của bạn rất có giá trị với chúng tôi. ⭐');
    }
}