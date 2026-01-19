<?php
require __DIR__ . '/../../app/includes/db.php'; require __DIR__ . '/../../app/includes/helpers.php';
const SECRET_PIN='077506';
$error=''; $success='';
if (!isset($_SESSION['bot_a'])) $_SESSION['bot_a']=random_int(1,9);
if (!isset($_SESSION['bot_b'])) $_SESSION['bot_b']=random_int(1,9);
$a=$_SESSION['bot_a']; $b=$_SESSION['bot_b']; $ans=$a+$b;
if (!isset($_SESSION['last_reg_time'])) $_SESSION['last_reg_time']=0;
$cooldown=3; $now=time();
$stage='pin';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  $honeypot=trim($_POST['website'] ?? '');
  if ($honeypot!==''){ $error='ข้อมูลไม่ถูกต้อง'; $stage='pin'; }
  else {
    if (($_POST['stage'] ?? '')==='pin'){
      $pin=trim($_POST['secret_pin'] ?? '');
      if ($pin!==SECRET_PIN){ $error='ข้อมูลไม่ถูกต้อง'; $stage='pin'; } else { $stage='form'; }
    } elseif (($_POST['stage'] ?? '')==='register'){
      $pin=trim($_POST['secret_pin'] ?? '');
      if ($pin!==SECRET_PIN){ $error='ข้อมูลไม่ถูกต้อง'; $stage='pin'; }
      elseif ($now - $_SESSION['last_reg_time'] < $cooldown){ $error='โปรดลองใหม่อีกครั้งในไม่กี่วินาที'; $stage='form'; }
      else {
        $_SESSION['last_reg_time']=$now;
        $username=trim($_POST['username'] ?? ''); $password=$_POST['password'] ?? ''; $confirm=$_POST['confirm'] ?? ''; $bot_ans=intval($_POST['bot_sum'] ?? -1);
        if ($username==='' || strlen($username)<3 || strlen($username)>50){ $error='ข้อมูลไม่ถูกต้อง'; $stage='form'; }
        elseif ($password==='' || $password!==$confirm || strlen($password)<6){ $error='ข้อมูลไม่ถูกต้อง'; $stage='form'; }
        elseif ($bot_ans!==$ans){ $error='ข้อมูลไม่ถูกต้อง'; $stage='form'; }
        else {
          $chk=$pdo->prepare("SELECT COUNT(*) FROM admins WHERE username=:u"); $chk->execute([':u'=>$username]);
          if ($chk->fetchColumn()>0){ $error='ชื่อผู้ใช้นี้ถูกใช้แล้ว'; $stage='form'; }
          else { $hash=password_hash($password, PASSWORD_BCRYPT); $ins=$pdo->prepare("INSERT INTO admins (username,password_hash) VALUES (:u,:h)");
            $ins->execute([':u'=>$username, ':h'=>$hash]); unset($_SESSION['bot_a'],$_SESSION['bot_b']); $success='สมัครผู้ดูแลสำเร็จแล้ว สามารถเข้าสู่ระบบได้'; $stage='form'; }
        }
      }
    }
  }
}
?><!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php include __DIR__ . '/../../app/includes/head.php'; ?><title>ลงทะเบียนผู้ดูแล</title>
<style>.trap{position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;}</style></head>
<body class="bg-slate-50"><div class="max-w-sm mx-auto p-6">
<?php if($stage==='pin'): ?>
<form method="post" class="bg-white rounded-2xl shadow card-glass p-6 space-y-3">
<h1 class="text-xl font-bold">ยืนยัน PIN เพื่อเข้าหน้าลงทะเบียน</h1>
<?php if($error): ?><div class="text-rose-600"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?= csrf_field() ?>
<input class="w-full rounded-xl px-3 py-2 input-fx" name="secret_pin" placeholder="PIN ลับ" required>
<div class="trap"><label>Website</label><input name="website" autocomplete="off"></div>
<input type="hidden" name="stage" value="pin">
<button class="w-full px-4 py-2 rounded-xl btn-neon bg-emerald-600 text-white">ยืนยัน</button>
<div class="text-center"><a class="text-blue-600 underline" href="login.php">กลับไปหน้าเข้าสู่ระบบ</a></div>
</form>
<?php else: ?>
<form method="post" class="bg-white rounded-2xl shadow card-glass p-6 space-y-3">
<h1 class="text-xl font-bold">ลงทะเบียนผู้ดูแล</h1>
<?php if($error): ?><div class="text-rose-600"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if($success): ?><div class="text-green-600"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?= csrf_field() ?>
<input class="w-full rounded-xl px-3 py-2 input-fx" name="username" placeholder="ชื่อผู้ใช้ (3-50 อักษร)" required>
<input class="w-full rounded-xl px-3 py-2 input-fx" type="password" name="password" placeholder="รหัสผ่าน (อย่างน้อย 6 ตัวอักษร)" required>
<input class="w-full rounded-xl px-3 py-2 input-fx" type="password" name="confirm" placeholder="ยืนยันรหัสผ่าน" required>
<div class="trap"><label>Website</label><input name="website" autocomplete="off"></div>
<label class="text-sm text-slate-600">ป้อนผลลัพธ์: <?= $a ?> + <?= $b ?> = ?</label>
<input class="w-full rounded-xl px-3 py-2 input-fx" name="bot_sum" inputmode="numeric" required>
<input type="hidden" name="stage" value="register">
<input class="w-full rounded-xl px-3 py-2 input-fx" name="secret_pin" placeholder="PIN ลับ (กรอกอีกครั้งเพื่อยืนยันสิทธิ์)" required>
<button class="w-full px-4 py-2 rounded-xl btn-neon bg-emerald-600 text-white">ลงทะเบียน</button>
<div class="text-center"><a class="text-blue-600 underline" href="login.php">กลับไปหน้าเข้าสู่ระบบ</a></div>
</form>
<?php endif; ?>
</div></body></html>
