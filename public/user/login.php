<?php
require __DIR__ . '/../../app/includes/db.php'; require __DIR__ . '/../../app/includes/helpers.php';
if (isset($_SESSION['user_login']) && $_SESSION['user_login']===true){ header('Location: home.php'); exit; }
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  $cid=preg_replace('/\D+/', '', $_POST['citizen_id'] ?? ''); $phone=preg_replace('/\D+/', '', $_POST['phone'] ?? '');
  if (!is_valid_thai_id($cid) || !is_valid_phone($phone)) { $error='ข้อมูลไม่ถูกต้อง'; }
  else {
    $stmt=$pdo->prepare("SELECT COUNT(*) FROM appointments WHERE citizen_id=:c AND phone=:p AND is_deleted=0");
    $stmt->execute([':c'=>$cid, ':p'=>$phone]);
    if ($stmt->fetchColumn()>0){ $_SESSION['user_login']=true; $_SESSION['user_cid']=$cid; $_SESSION['user_phone']=$phone; header('Location: home.php'); exit; }
    else { $error='ไม่พบบัญชีการจอง'; }
  }
}
?><!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php include __DIR__ . '/../../app/includes/head.php'; ?><title>เข้าสู่ระบบผู้ใช้</title></head>
<body class="bg-slate-50"><div class="max-w-sm mx-auto p-6"><form method="post" class="bg-white rounded-2xl shadow card-glass p-6 space-y-3">
<h1 class="text-xl font-bold">เข้าสู่ระบบผู้ใช้</h1>
<?php if($error): ?><div class="text-rose-600"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?= csrf_field() ?>
<input class="w-full rounded-xl px-3 py-2 input-fx" name="citizen_id" maxlength="13" placeholder="เลขบัตรประชาชน 13 หลัก" required>
<input class="w-full rounded-xl px-3 py-2 input-fx" name="phone" maxlength="10" pattern="0[0-9]{9}" inputmode="tel" placeholder="0XXXXXXXXX" required>
<button class="w-full px-4 py-2 rounded-xl btn-neon bg-emerald-600 text-white">เข้าสู่ระบบ</button>
<div class="text-center"><a class="text-blue-600 underline" href="../">กลับหน้าหลัก</a></div></form></div></body></html>
