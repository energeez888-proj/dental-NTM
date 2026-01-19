<?php ?>
<!doctype html><html lang="th"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php include __DIR__ . '/../../includes/head.php'; ?>
<title>ข้อกำหนด</title></head>
<body class="bg-slate-50">
  <div class="fixed top-4 right-4">
    <a class="px-4 py-2 rounded-lg bg-slate-800 text-white hover:bg-slate-700" href="<?= htmlspecialchars(base_url('admin/login.php')) ?>">เจ้าหน้าที่</a>
  </div>
  <div class="max-w-2xl mx-auto p-6">
    <div class="bg-white rounded-2xl shadow p-6">
      <h1 class="text-xl font-bold mb-4">เว็บจองนัด งานทันตกรรม คลีนิกหมอครอบครัวหนองตาหมู่</h1>
      <ul class="list-disc pl-6 text-slate-600 space-y-1">
        <li>เปิดให้จองเฉพาะวันพฤหัสบดี ล่วงหน้าไม่เกิน 1 เดือน</li>
        <li>หนึ่งช่วงเวลาต่อหนึ่งคิว</li>
        <li>ตรวจสอบข้อมูลก่อนยืนยัน</li>
      </ul>
      <div class="mt-6 flex items-center justify-between">
        <a class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500" href="?step=1">ตกลง</a>
        <a class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-500" href="<?= htmlspecialchars(base_url('user/login.php')) ?>">ล็อคอิน</a>
      </div>
    </div>
  </div>
</body></html>
