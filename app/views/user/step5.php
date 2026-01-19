<?php $all=$slots['all']; $busy=$slots['busy']; ?>
<!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php include __DIR__ . '/../../includes/head.php'; ?><title>เลือกเวลา</title></head>
<body class="bg-slate-50"><div class="max-w-xl mx-auto p-6"><div class="bg-white rounded-2xl shadow p-6">
<h2 class="text-lg font-semibold mb-4">เลือกช่วงเวลา</h2>
<?php if (!empty($error)): ?><div class="mb-4 text-rose-600"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post" class="space-y-4"><?= csrf_field() ?>
<div class="grid grid-cols-1 gap-2">
<?php foreach ($all as $slot): $disabled=in_array($slot,$busy,true); ?>
<label class="flex items-center gap-3 p-3 border rounded-lg <?= $disabled?'bg-slate-100 text-slate-400':'' ?>">
<input type="radio" name="time_slot" value="<?= htmlspecialchars($slot) ?>" <?= $disabled?'disabled':'' ?>>
<span><?= htmlspecialchars($slot) ?> <?= $disabled?'(ไม่ว่าง)':'' ?></span>
</label>
<?php endforeach; ?>
</div>
<div class="flex gap-2"><a class="px-4 py-2 rounded-lg bg-slate-200" href="?step=4">ย้อนกลับ</a><button class="px-4 py-2 rounded-lg bg-blue-600 text-white">ถัดไป</button></div>
</form></div></div></body></html>
