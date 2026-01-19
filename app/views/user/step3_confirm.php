<?php $candidate=$_SESSION['booking']['phone_candidate']??''; $existing=$_SESSION['booking']['existing_phone']??''; ?>
<!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php include __DIR__ . '/../../includes/head.php'; ?><title>ยืนยันเปลี่ยนเบอร์โทร</title></head>
<body class="bg-slate-50"><div class="max-w-xl mx-auto p-6"><div class="bg-white rounded-2xl shadow p-6">
<h2 class="text-lg font-semibold mb-2">คุณเคยใช้เบอร์เดิม: <span class="font-mono"><?= htmlspecialchars($existing) ?></span></h2>
<p class="text-slate-700 mb-4">ต้องการเปลี่ยนเป็นเบอร์ใหม่ <span class="font-semibold"><?= htmlspecialchars($candidate) ?></span> หรือไม่?</p>
<form method="post" class="flex gap-2">
  <?= csrf_field() ?>
  <button name="use_new" class="px-4 py-2 rounded-lg bg-emerald-600 text-white">ใช้เบอร์ใหม่</button>
  <button name="use_old" class="px-4 py-2 rounded-lg bg-slate-200">ใช้เบอร์เดิม</button>
</form>
</div></div></body></html>
