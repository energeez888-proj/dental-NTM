<?php
require __DIR__ . '/../../app/includes/db.php'; require __DIR__ . '/../../app/includes/helpers.php';
if (!isset($_SESSION['admin_id'])){ header('Location: login.php'); exit; }
$rows=$pdo->query("SELECT * FROM appointments ORDER BY date DESC, time_slot DESC")->fetchAll();
$flash = isset($_GET['deleted']) ? 'ลบใบนัดเรียบร้อยแล้ว' : '';
?><!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php include __DIR__ . '/../../app/includes/head.php'; ?><title>แดชบอร์ด</title></head>
<body class="bg-slate-50"><div class="max-w-5xl mx-auto p-6">
<div class="flex justify-between items-center mb-4"><h1 class="text-xl font-bold">รายการจองนัด</h1>
<div class="flex items-center gap-2"><span class="text-slate-600"><?= htmlspecialchars($_SESSION['admin_user']) ?></span>
<a class="px-3 py-1.5 rounded-xl bg-slate-200" href="logout.php">ออกจากระบบ</a></div></div>
<?php if($flash): ?><div class="mb-3 px-3 py-2 rounded bg-emerald-100 text-emerald-700"><?= htmlspecialchars($flash) ?></div><?php endif; ?>
<div class="bg-white rounded-2xl shadow overflow-x-auto"><table class="min-w-full text-sm">
<thead class="bg-slate-100"><tr>
<th class="text-left p-3">ชื่อ-นามสกุล</th><th class="text-left p-3">บัตรประชาชน</th><th class="text-left p-3">เบอร์โทร</th>
<th class="text-left p-3">วันที่</th><th class="text-left p-3">เวลา</th><th class="text-left p-3">บริการ</th><th class="text-left p-3">สิทธิการรักษา</th><th class="text-left p-3">สถานะ</th><th class="text-left p-3">จัดการ</th>
</tr></thead>
<tbody><?php foreach($rows as $r): ?><tr class="border-t">
<td class="p-3"><?= htmlspecialchars($r['full_name']) ?></td>
<td class="p-3"><?= htmlspecialchars($r['citizen_id']) ?></td>
<td class="p-3"><?= htmlspecialchars($r['phone']) ?></td>
<td class="p-3"><?= htmlspecialchars($r['date']) ?></td>
<td class="p-3"><?= htmlspecialchars($r['time_slot']) ?></td>
<td class="p-3"><?= htmlspecialchars($r['service']) ?></td>
<td class="p-3"><?= htmlspecialchars($r['coverage']) ?></td>
<td class="p-3">
<?php if ((int)$r['is_deleted']===1): ?><span class="px-2 py-1 rounded bg-slate-200 text-slate-600 text-xs">ถูกลบ</span>
<?php else: ?><span class="px-2 py-1 rounded bg-emerald-100 text-emerald-700 text-xs">ใช้งาน</span><?php endif; ?>
</td>
<td class="p-3">
  <div class="flex items-center gap-2">
    <a class="px-3 py-1.5 rounded-xl btn-neon bg-amber-500 text-white" href="edit.php?id=<?= (int)$r['id'] ?>">แก้ไข</a>
    <?php if ((int)$r['is_deleted']===0): ?>
      <form method="post" action="delete.php" onsubmit="return confirm('ยืนยันการลบ (ผู้ใช้จะเข้าถึงใบนัดนี้ไม่ได้อีก)?');">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
        <button type="submit" class="px-3 py-1.5 rounded-xl btn-neon bg-rose-600 text-white">ลบ</button>
      </form>
    <?php else: ?>
      <button class="px-3 py-1.5 rounded-xl bg-slate-300 text-white" disabled>ลบแล้ว</button>
    <?php endif; ?>
  </div>
</td>
</tr><?php endforeach; ?></tbody></table></div></div></body></html>
