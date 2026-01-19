<?php
require __DIR__ . '/../app/includes/db.php'; require __DIR__ . '/../app/includes/helpers.php';
$ref=$_GET['ref']??''; $stmt=$pdo->prepare("SELECT * FROM appointments WHERE ref_token=:r AND is_deleted=0"); $stmt->execute([':r'=>$ref]); $ap=$stmt->fetch();
?><!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php include __DIR__ . '/../app/includes/head.php'; ?><title>ใบนัด</title></head>
<body class="bg-slate-50"><div class="max-w-xl mx-auto p-6"><div class="bg-white rounded-2xl shadow p-6">
<?php if($ap): ?><h1 class="text-xl font-bold mb-4">ใบนัดจองคิวทันตกรรม</h1>
<div class="space-y-1 text-slate-700">
<div><span class="font-medium">ชื่อ-นามสกุล:</span> <?= htmlspecialchars($ap['full_name']) ?></div>
<div><span class="font-medium">เลขบัตรประชาชน:</span> <?= htmlspecialchars($ap['citizen_id']) ?></div>
<div><span class="font-medium">เบอร์โทร:</span> <?= htmlspecialchars($ap['phone']) ?></div>
<div><span class="font-medium">วันที่จอง:</span> <?= htmlspecialchars($ap['date']) ?></div>
<div><span class="font-medium">เวลาที่จอง:</span> <?= htmlspecialchars($ap['time_slot']) ?></div>
<div><span class="font-medium">บริการ:</span> <?= htmlspecialchars($ap['service']) ?></div>
<div><span class="font-medium">สิทธิการรักษา:</span> <?= htmlspecialchars($ap['coverage']) ?></div>
<div class="text-xs text-slate-400">รหัสอ้างอิง: <?= htmlspecialchars($ap['ref_token']) ?></div>
</div>
<div class="mt-4 px-3 py-2 border border-rose-300 bg-rose-50 text-rose-700 rounded">
กรุณามาก่อนเวลานัด 30 นาที
</div>
<?php else: ?><p class="text-rose-600">ไม่พบข้อมูลการจอง</p><?php endif; ?>
<?php $backUrl = (isset($_SESSION['user_login']) && $_SESSION['user_login']===true) ? base_url('user/home.php') : base_url(''); ?>
<div class="mt-6">
  <a class="px-4 py-2 rounded-lg bg-blue-600 text-white" href="<?= htmlspecialchars($backUrl) ?>">
    <?= (isset($_SESSION['user_login']) && $_SESSION['user_login']===true) ? 'กลับไปหน้า user/home.php' : 'กลับหน้าหลัก' ?>
  </a>
</div>
</div></div></body></html>
