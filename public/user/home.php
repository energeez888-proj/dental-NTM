<?php
require __DIR__ . '/../../app/includes/db.php'; require __DIR__ . '/../../app/includes/helpers.php';
if (!isset($_SESSION['user_login']) || $_SESSION['user_login']!==true){ header('Location: login.php'); exit; }
$cid=$_SESSION['user_cid'];
$up=$pdo->prepare("SELECT * FROM appointments WHERE citizen_id=:c AND is_deleted=0 AND date >= CURDATE() ORDER BY date ASC, time_slot ASC LIMIT 1"); $up->execute([':c'=>$cid]); $upcoming=$up->fetch();
$hist=$pdo->prepare("SELECT * FROM appointments WHERE citizen_id=:c AND is_deleted=0 AND date < CURDATE() ORDER BY date DESC, time_slot DESC"); $hist->execute([':c'=>$cid]); $history=$hist->fetchAll();
?><!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php include __DIR__ . '/../../app/includes/head.php'; ?><title>ใบนัดของฉัน</title></head>
<body class="bg-slate-50"><div class="max-w-3xl mx-auto p-6">
<div class="flex justify-between items-center mb-4"><h1 class="text-xl font-bold">ใบนัดของฉัน</h1><a class="px-3 py-1 rounded-lg bg-slate-200" href="logout.php">ออกจากระบบ</a></div>
<?php if(isset($_GET['canceled'])): ?><div class="mb-4 px-3 py-2 rounded bg-amber-100 text-amber-800">ยกเลิกใบนัดเรียบร้อยแล้ว</div><?php endif; ?>
<div class="bg-white rounded-2xl shadow p-6 mb-6">
<h2 class="text-lg font-semibold mb-3">นัดที่กำลังจะถึง</h2>
<?php if($upcoming): ?><div class="space-y-1 text-slate-700">
<div><span class="font-medium">วันที่:</span> <?= htmlspecialchars($upcoming['date']) ?></div>
<div><span class="font-medium">เวลา:</span> <?= htmlspecialchars($upcoming['time_slot']) ?></div>
<div><span class="font-medium">บริการ:</span> <?= htmlspecialchars($upcoming['service']) ?></div>
<div><span class="font-medium">สิทธิการรักษา:</span> <?= htmlspecialchars($upcoming['coverage']) ?></div>
<div><span class="font-medium">ชื่อ:</span> <?= htmlspecialchars($upcoming['full_name']) ?></div>
<div class="flex gap-2 mt-3">
  <form method="post" action="cancel.php" onsubmit="return confirm('ยืนยันการยกเลิกนัดนี้?');" class="inline">
    <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$upcoming['id'] ?>"><button class="px-3 py-1 rounded bg-rose-600 text-white">ยกเลิกนัด</button>
  </form>
  <a class="px-3 py-1 rounded bg-blue-600 text-white" href="../ticket.php?ref=<?= urlencode($upcoming['ref_token']) ?>">เปิดใบนัด</a>
  <a class="px-3 py-1 rounded bg-emerald-600 text-white" href="../ics.php?ref=<?= urlencode($upcoming['ref_token']) ?>">เพิ่มลงปฏิทิน</a>
</div></div><?php else: ?><p class="text-slate-600">ยังไม่มีนัดในอนาคต</p><?php endif; ?>
</div>
<div class="bg-white rounded-2xl shadow p-6">
<h2 class="text-lg font-semibold mb-3">ประวัติการจองที่ผ่านมา</h2>
<?php if($history): ?><div class="overflow-x-auto"><table class="min-w-full text-sm">
<thead class="bg-slate-100"><tr><th class="text-left p-2">วันที่</th><th class="text-left p-2">เวลา</th><th class="text-left p-2">บริการ</th><th class="text-left p-2">สิทธิการรักษา</th><th class="text-left p-2">ใบนัด</th><th class="text-left p-2">ปฏิทิน</th></tr></thead>
<tbody><?php foreach($history as $h): ?><tr class="border-t">
<td class="p-2"><?= htmlspecialchars($h['date']) ?></td>
<td class="p-2"><?= htmlspecialchars($h['time_slot']) ?></td>
<td class="p-2"><?= htmlspecialchars($h['service']) ?></td>
<td class="p-2"><?= htmlspecialchars($h['coverage']) ?></td>
<td class="p-2"><a class="text-blue-600 underline" href="../ticket.php?ref=<?= urlencode($h['ref_token']) ?>">เปิด</a></td>
<td class="p-2"><a class="text-emerald-600 underline" href="../ics.php?ref=<?= urlencode($h['ref_token']) ?>">เพิ่ม</a></td>
</tr><?php endforeach; ?></tbody></table></div>
<?php else: ?><p class="text-slate-600">ยังไม่มีข้อมูลย้อนหลัง</p><?php endif; ?></div>
</div></body></html>
