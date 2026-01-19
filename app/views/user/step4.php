<?php $selected=$_SESSION['booking']['date']??''; $dates=$dates??[]; ?>
<!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php include __DIR__ . '/../../includes/head.php'; ?><title>เลือกวันพฤหัสฯ</title></head>
<body class="bg-slate-50"><div class="max-w-xl mx-auto p-6"><div class="bg-white rounded-2xl shadow p-6">
<h2 class="text-lg font-semibold mb-4">เลือกวันนัด (เฉพาะวันพฤหัสบดี ภายใน 1 เดือน)</h2>
<?php if (!empty($error)): ?><div class="mb-4 text-rose-600"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post" class="space-y-3"><?= csrf_field() ?>
<select class="w-full border rounded-lg px-3 py-2" name="date" required>
<option value="">-- เลือกวันที่ --</option>
<?php foreach($dates as $d): ?><option value="<?= htmlspecialchars($d) ?>" <?= $selected===$d?'selected':'' ?>><?= htmlspecialchars($d) ?></option><?php endforeach; ?>
</select>
<div class="flex gap-2"><a class="px-4 py-2 rounded-lg bg-slate-200" href="?step=3">ย้อนกลับ</a><button class="px-4 py-2 rounded-lg bg-blue-600 text-white">ถัดไป</button></div>
</form></div></div></body></html>
