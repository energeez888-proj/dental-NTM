<?php ?>
<!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php include __DIR__ . '/../../includes/head.php'; ?><title>ชื่อ-นามสกุล</title></head>
<body class="bg-slate-50"><div class="max-w-xl mx-auto p-6"><div class="bg-white rounded-2xl shadow p-6">
<h2 class="text-lg font-semibold mb-4">กรอกชื่อ-นามสกุล</h2>
<?php if (!empty($error)): ?><div class="mb-4 text-rose-600"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post"><?= csrf_field() ?>
<input class="w-full border rounded-lg px-3 py-2" name="full_name" placeholder="ชื่อ-นามสกุล" required>
<div class="mt-4 flex gap-2"><a class="px-4 py-2 rounded-lg bg-slate-200" href="<?= htmlspecialchars(base_url('?reset=1')) ?>">ยกเลิก</a>
<button class="px-4 py-2 rounded-lg bg-blue-600 text-white">ถัดไป</button></div></form>
</div></div></body></html>
