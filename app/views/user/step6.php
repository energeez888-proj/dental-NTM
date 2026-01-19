<?php $data=$_SESSION['booking']??[]; ?>
<!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php include __DIR__ . '/../../includes/head.php'; ?><title>ยืนยันการจอง</title></head>
<body class="bg-slate-50"><div class="max-w-xl mx-auto p-6">
<?php if (!empty($error)): ?><div class="mb-3 px-3 py-2 rounded bg-rose-100 text-rose-700"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="bg-white rounded-2xl shadow p-6">
<h2 class="text-lg font-semibold mb-4">ตรวจสอบและยืนยัน</h2>
<div class="space-y-1 text-slate-700">
<div><span class="font-medium">ชื่อ-นามสกุล:</span> <?= htmlspecialchars($data['full_name'] ?? '') ?></div>
<div><span class="font-medium">เลขบัตรประชาชน:</span> <?= htmlspecialchars($data['citizen_id'] ?? '') ?></div>
<div><span class="font-medium">เบอร์โทร:</span> <?= htmlspecialchars($data['phone'] ?? '') ?></div>
<div><span class="font-medium">วันที่จอง:</span> <?= htmlspecialchars($data['date'] ?? '') ?></div>
<div><span class="font-medium">เวลาที่จอง:</span> <?= htmlspecialchars($data['time_slot'] ?? '') ?></div>
</div>
<form method="post" class="mt-6 flex gap-2"><?= csrf_field() ?>
<button name="confirm" class="px-4 py-2 rounded-lg bg-green-600 text-white">ยืนยัน</button>
<button name="cancel" class="px-4 py-2 rounded-lg bg-slate-200" value="1">ยกเลิก</button>
</form>
</div></div></body></html>
