---
name: ui-ux-design
description: Hướng dẫn thiết kế giao diện Dark & Gold chuẩn UI/UX cho The Barber Club
---

# NGUYÊN TẮC THIẾT KẾ UI/UX (THE BARBER CLUB)

## 1. Hệ màu chủ đạo (Color Palette)
- Nền chính (Background): `#0f172a` hoặc `#121212` (Đen xám, không dùng đen tuyền #000).
- Nền thẻ/Card (Surface): `#1e293b` hoặc `#1a1a1a` (Tạo chiều sâu phân lớp).
- Màu nhấn thương hiệu (Accent Gold): `#d4af37` hoặc `#f59e0b` (Vàng kim loại sang trọng).
- Màu chữ chính (Text Primary): `#f8fafc` (Trắng ngà, dễ đọc trên nền tối).
- Màu chữ phụ (Text Muted): `#94a3b8` (Xám nhạt cho nhãn, ghi chú).
- Trạng thái: Thành công dùng xanh ngọc (`#10b981`), Lỗi dùng đỏ cam (`#ef4444`).

## 2. Bố cục & Thành phần (Layout & Components)
- Thẻ card: Bo góc `border-radius: 8px - 12px`, có viền mỏng ánh kim (`border: 1px solid rgba(212, 175, 55, 0.2)`).
- Bảng biểu (Tables): Header nền sẫm viền vàng, dòng chẵn/lẻ xen kẽ nhẹ, có padding thoáng (`12px - 16px`).
- Nút bấm (Buttons): Nền vàng chữ đen đậm hoặc viền vàng chữ vàng; hiệu ứng hover đổi sắc độ nhẹ (`transition: 0.2s`).
- Form nhập liệu: Viền sáng lên màu vàng khi focus (`outline: none; border-color: #d4af37; box-shadow: 0 0 0 2px rgba(212,175,55,0.25)`).

## 3. Trải nghiệm người dùng (UX Constraints)
- Định dạng số tiền: Luôn hiển thị dạng `number_format($price) VNĐ`.
- Trạng thái phản hồi: Mọi thao tác submit form phải có hiệu ứng loading/disabled để chống bấm nhiều lần.
- Cảnh báo: Luôn có modal hoặc xác nhận trình duyệt trước các hành động hủy/xóa.
- Responsive: Tối ưu chuẩn hiển thị trên mobile (không để vỡ bảng, tràn thanh cuộn ngang).