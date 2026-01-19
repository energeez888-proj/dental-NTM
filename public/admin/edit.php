<?php
require __DIR__ . '/../../app/includes/db.php'; require __DIR__ . '/../../app/includes/helpers.php'; require __DIR__ . '/../../app/includes/holidays.php';
if (!isset($_SESSION['admin_id'])){ header('Location: login.php'); exit; }
$id = intval($_GET['id'] ?? 0);
$stmt=$pdo->prepare("SELECT * FROM appointments WHERE id=:id"); $stmt->execute([':id'=>$id]); $ap=$stmt->fetch();
if (!$ap){ echo 'ไม่พบข้อมูล'; exit; }
$dates = thursday_options(); $slots = available_slots($pdo, $ap['date']);
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  $full_name=trim($_POST['full_name'] ?? ''); $citizen_id=preg_replace('/\D+/', '', $_POST['citizen_id'] ?? '');
  $phone=preg_replace('/\D+/', '', $_POST['phone'] ?? ''); $date=$_POST['date'] ?? ''; $time_slot=$_POST['time_slot'] ?? ''; $service=$_POST['service'] ?? ''; $coverage=$_POST['coverage'] ?? '';
  if ($full_name==='') $error='กรุณากรอกชื่อ';
  elseif (!is_valid_thai_id($citizen_id)) $error='เลขบัตรไม่ถูกต้อง';
  elseif (!is_valid_phone($phone)) $error='เบอร์โทรไม่ถูกต้อง';
  elseif (!is_booking_date_allowed($date, $PUBLIC_HOLIDAYS)) $error='วันนัดไม่ถูกต้อง (ต้องเป็นวันพฤหัส ภายใน 90 วัน และไม่ใช่วันหยุด)';
  elseif (!is_valid_service($service)) $error='กรุณาเลือกบริการ';
  elseif (!is_valid_coverage($coverage)) $error='กรุณาเลือกสิทธิการรักษา';
  else {
    if ($date !== $ap['date'] || $time_slot !== $ap['time_slot']) {
      $check=$pdo->prepare("SELECT COUNT(*) FROM appointments WHERE date=:d AND time_slot=:t AND id<>:id AND is_deleted=0");
      $check->execute([':d'=>$date, ':t'=>$time_slot, ':id'=>$id]);
      if ($check->fetchColumn()>0) $error='ช่วงเวลานี้มีผู้อื่นจองแล้ว';
    }
  }
  if (!$error){
    $upd=$pdo->prepare("UPDATE appointments SET full_name=:f,citizen_id=:c,phone=:p,date=:d,time_slot=:t,service=:s,coverage=:g WHERE id=:id");
    $upd->execute([':f'=>$full_name, ':c'=>$citizen_id, ':p'=>$phone, ':d'=>$date, ':t'=>$time_slot, ':s'=>$service, ':g'=>$coverage, ':id'=>$id]);
    header('Location: dashboard.php'); exit;
  }
  $ap=['id'=>$id,'full_name'=>$full_name,'citizen_id'=>$citizen_id,'phone'=>$phone,'date'=>$date,'time_slot'=>$time_slot,'service'=>$service,'coverage'=>$coverage,'ref_token'=>$ap['ref_token']];
  $slots = available_slots($pdo, $date);
}
?><!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php include __DIR__ . '/../../app/includes/head.php'; ?><title>แก้ไขการจอง</title></head>
<body class="bg-slate-50"><div class="max-w-xl mx-auto p-6"><div class="bg-white rounded-2xl shadow card-glass p-6">
<h1 class="text-xl font-bold mb-4">แก้ไขการจอง</h1>
<?php if($error): ?><div class="mb-4 text-rose-600"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post" class="space-y-3"><?= csrf_field() ?>
<input class="w-full rounded-xl px-3 py-2 input-fx" name="full_name" value="<?= htmlspecialchars($ap['full_name']) ?>" required>
<input class="w-full rounded-xl px-3 py-2 input-fx" name="citizen_id" value="<?= htmlspecialchars($ap['citizen_id']) ?>" maxlength="13" required>
<input class="w-full rounded-xl px-3 py-2 input-fx" name="phone" value="<?= htmlspecialchars($ap['phone']) ?>" maxlength="10" required>
<select class="w-full rounded-xl px-3 py-2 input-fx" name="date" required>
<?php foreach($dates as $d): ?><option value="<?= htmlspecialchars($d) ?>" <?= $ap['date']===$d?'selected':'' ?>><?= htmlspecialchars($d) ?></option><?php endforeach; ?>
</select>
<?php $all=['09:00-09:30','09:40-10:10','10:20-10:50','11:00-11:30','11:30-12:00']; ?>
<label class="block mb-1 font-medium">ท่านต้องการรับบริการทันตกรรมด้านใด</label>
<select class="w-full rounded-xl px-3 py-2 input-fx" name="service" required>
  <option value="ตรวจฟัน" <?= ($ap['service'] ?? '')==='ตรวจฟัน'?'selected':'' ?>>ตรวจฟัน</option>
  <option value="ขูดหินปูน" <?= ($ap['service'] ?? '')==='ขูดหินปูน'?'selected':'' ?>>ขูดหินปูน</option>
  <option value="อุดฟัน" <?= ($ap['service'] ?? '')==='อุดฟัน'?'selected':'' ?>>อุดฟัน</option>
</select>
<label class="block mb-1 font-medium mt-3">สิทธิการรักษา</label>
<select class="w-full rounded-xl px-3 py-2 input-fx" name="coverage" required>
  <option value="สิทธิหลักประกันสุขภาพถ้วนหน้า (บัตรทอง/ 30 บาท รักษาทุกโรค)" <?= ($ap['coverage'] ?? '')==='สิทธิหลักประกันสุขภาพถ้วนหน้า (บัตรทอง/ 30 บาท รักษาทุกโรค)'?'selected':'' ?>>สิทธิหลักประกันสุขภาพถ้วนหน้า (บัตรทอง/ 30 บาท รักษาทุกโรค)</option>
  <option value="สิทธิเบิกได้กรมบัญชีกลาง (เบิกได้ข้าราชการ/บุคคลในครอบครัว)" <?= ($ap['coverage'] ?? '')==='สิทธิเบิกได้กรมบัญชีกลาง (เบิกได้ข้าราชการ/บุคคลในครอบครัว)'?'selected':'' ?>>สิทธิเบิกได้กรมบัญชีกลาง (เบิกได้ข้าราชการ/บุคคลในครอบครัว)</option>
  <option value="สิทธิข้าราชการท้องถิ่น (อปท.)" <?= ($ap['coverage'] ?? '')==='สิทธิข้าราชการท้องถิ่น (อปท.)'?'selected':'' ?>>สิทธิข้าราชการท้องถิ่น (อปท.)</option>
</select>
<select class="w-full rounded-xl px-3 py-2 input-fx" name="time_slot" required>
<?php foreach($all as $slot): ?><option value="<?= htmlspecialchars($slot) ?>" <?= $ap['time_slot']===$slot?'selected':'' ?>><?= htmlspecialchars($slot) ?></option><?php endforeach; ?>
</select>
<div class="flex gap-2"><a class="px-4 py-2 rounded-xl bg-slate-200" href="dashboard.php">ยกเลิก</a>
<button class="px-4 py-2 rounded-xl btn-neon bg-blue-600 text-white">บันทึก</button></div></form>
</div></div></body></html>
