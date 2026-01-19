<?php
require __DIR__ . '/../app/includes/db.php'; require __DIR__ . '/../app/includes/helpers.php';
$ref = $_GET['ref'] ?? ''; $stmt = $pdo->prepare("SELECT * FROM appointments WHERE ref_token=:r"); $stmt->execute([':r'=>$ref]); $ap=$stmt->fetch();
?><!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php include __DIR__ . '/../app/includes/head.php'; ?><title>จองสำเร็จ</title></head>
<body class="bg-slate-50"><div class="max-w-2xl mx-auto p-6"><div class="bg-white rounded-2xl shadow p-6">
<?php if($ap): ?><h1 class="text-xl font-bold mb-3">บันทึกใบจองคิวเรียบร้อยแล้ว</h1>
<p class="mb-2 text-slate-600">เก็บลิงก์นี้ไว้เพื่อตรวจสอบภายหลัง:</p>
<a class="text-blue-600 underline break-all" href="<?= htmlspecialchars(base_url('ticket.php?ref=' . urlencode($ap['ref_token']))) ?>">
<?= htmlspecialchars(base_url('ticket.php?ref=' . urlencode($ap['ref_token']))) ?></a>
<div class="mt-4"><a class="px-3 py-1 rounded bg-emerald-600 text-white" href="<?= htmlspecialchars(base_url('ics.php?ref=' . urlencode($ap['ref_token']))) ?>">เพิ่มลงปฏิทิน</a></div>
<?php else: ?><p class="text-rose-600">ไม่พบข้อมูลการจอง</p><?php endif; ?>
<div class="mt-6"><a class="px-4 py-2 rounded-lg bg-blue-600 text-white" href="<?= htmlspecialchars(base_url('')) ?>">กลับหน้าหลัก</a></div>
</div></div></body></html>
