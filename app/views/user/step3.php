<?php ?>
<!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php include __DIR__ . '/../../includes/head.php'; ?><title>เบอร์โทร</title></head>
<body class="bg-slate-50"><div class="max-w-xl mx-auto p-6"><div class="bg-white rounded-2xl shadow p-6">
<h2 class="text-lg font-semibold mb-4">กรอกเบอร์โทร (ต้องขึ้นต้นด้วย 0 และยาว 10 หลัก)</h2>
<?php if (!empty($error)): ?><div class="mb-4 text-rose-600"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post"><?= csrf_field() ?>
<input class="w-full border rounded-lg px-3 py-2" name="phone" maxlength="10" pattern="0[0-9]{9}" inputmode="tel" placeholder="0XXXXXXXXX" required>
<div class="mt-4 flex gap-2"><a class="px-4 py-2 rounded-lg bg-slate-200" href="?step=2">ย้อนกลับ</a>
<button class="px-4 py-2 rounded-lg bg-blue-600 text-white">ถัดไป</button></div></form>
</div></div></body></html>
