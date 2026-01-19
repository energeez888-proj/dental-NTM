# Dental Appointment PHP (Single Page + Calendar + 90 days)
PHP 7+ • MySQL • TailwindCSS • Flatpickr • Centralized Sarabun font

- จองเฉพาะพฤหัสบดี ภายใน **90 วัน** และปิด **วันหยุด** ตาม `app/includes/holidays.php`
- ตรวจสอบซ้ำฝั่งเซิร์ฟเวอร์ด้วย `is_booking_date_allowed()`
- ฟอร์มจองหน้าเดียว + อัปเดตช่องเวลาแบบสดผ่าน `public/api/slots.php`
- User: login (เลขบัตร + เบอร์) ดูนัดที่จะถึง/ประวัติ ยกเลิกเองได้
- Admin: login, register (PIN 077506 + anti-bot), dashboard, edit, soft delete
- .ics export เพิ่มนัดเข้าปฏิทิน
- ฟอนต์ทั้งเว็บแก้ที่ `app/includes/theme.php` และรวมผ่าน `app/includes/head.php`

## Setup
1) Import `database/schema.sql` (seed admin: `admin`/`admin123`)
2) ตั้งค่า `app/includes/config.php` → DB และ `base_url`
3) แก้วันหยุดใน `app/includes/holidays.php`
4) ใช้งาน:
   - Booking: `/public/`
   - User: `/public/user/login.php`
   - Admin: `/public/admin/login.php`
