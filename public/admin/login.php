<?php
require __DIR__ . '/../../app/includes/db.php'; require __DIR__ . '/../../app/includes/helpers.php';
if (isset($_SESSION['admin_id'])){ header('Location: dashboard.php'); exit; }
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  $user=trim($_POST['username'] ?? ''); $pass=$_POST['password'] ?? '';
  $stmt=$pdo->prepare("SELECT * FROM admins WHERE username=:u LIMIT 1"); $stmt->execute([':u'=>$user]); $adm=$stmt->fetch();
  if ($adm && password_verify($pass, $adm['password_hash'])){ $_SESSION['admin_id']=$adm['id']; $_SESSION['admin_user']=$adm['username']; header('Location: dashboard.php'); exit; }
  else { $error='เข้าสู่ระบบไม่สำเร็จ'; }
}
?><!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php include __DIR__ . '/../../app/includes/head.php'; ?><title>Admin Login</title></head>
<body class="bg-slate-50"><div class="max-w-sm mx-auto p-6">
<form method="post" class="bg-white rounded-2xl shadow card-glass p-6 space-y-3">
<h1 class="text-xl font-bold">เข้าสู่ระบบผู้ดูแล</h1>
<?php if($error): ?><div class="text-rose-600"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?= csrf_field() ?>
<input class="w-full rounded-xl px-3 py-2 input-fx" name="username" placeholder="ชื่อผู้ใช้" required>
<input class="w-full rounded-xl px-3 py-2 input-fx" type="password" name="password" placeholder="รหัสผ่าน" required>
<button class="w-full px-4 py-2 rounded-xl btn-neon bg-emerald-600 text-white">เข้าสู่ระบบ</button>
<div class="mt-3 text-center flex justify-between items-center"><a class="text-blue-600 underline" href="register.php">ลงทะเบียนผู้ดูแล</a><a class="text-slate-700 underline" href="<?= htmlspecialchars(base_url('')) ?>">กลับหน้าหลัก</a></div>
</form></div></body></html>
