<?php
require __DIR__ . '/../app/includes/db.php';
require __DIR__ . '/../app/includes/helpers.php';
if (isset($_GET['reset'])) { unset($_SESSION['booking']); header('Location: ' . base_url('')); exit; }
$step = intval($_GET['step'] ?? 0);
if ($step === 0) { include __DIR__ . '/../app/views/user/step0.php'; exit; }
if ($step === 1) {
  if ($_SERVER['REQUEST_METHOD']==='POST') { verify_csrf(); $name = trim($_POST['full_name'] ?? ''); if ($name==='') { $error='กรุณากรอกชื่อ-นามสกุล'; } else { $_SESSION['booking']['full_name']=$name; header('Location: ?step=2'); exit; } }
  include __DIR__ . '/../app/views/user/step1.php'; exit;
}
if ($step === 2) {
  if ($_SERVER['REQUEST_METHOD']==='POST') { verify_csrf(); $cid = preg_replace('/\D+/', '', $_POST['citizen_id'] ?? ''); if (!is_valid_thai_id($cid)) { $error = 'เลขบัตรประชาชนไม่ถูกต้อง (13 หลัก)'; } else { $_SESSION['booking']['citizen_id']=$cid; header('Location: ?step=3'); exit; } }
  include __DIR__ . '/../app/views/user/step2.php'; exit;
}
if ($step === 3) {
  if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $phone = preg_replace('/\D+/', '', $_POST['phone'] ?? '');
    if (!is_valid_phone($phone)) { $error='เบอร์โทรต้องเป็นตัวเลข 10 หลัก และขึ้นต้นด้วย 0'; }
    else {
      $cid = $_SESSION['booking']['citizen_id'] ?? null; if (!$cid) { header('Location: ?step=2'); exit; }
      $firstPhone = get_first_phone_for_cid($pdo, $cid);
      if ($firstPhone && $firstPhone !== $phone) { $_SESSION['booking']['phone_candidate']=$phone; $_SESSION['booking']['existing_phone']=$firstPhone; header('Location: ?step=31'); exit; }
      else { $_SESSION['booking']['phone']=$phone; header('Location: ?step=4'); exit; }
    }
  }
  include __DIR__ . '/../app/views/user/step3.php'; exit;
}
if ($step === 31) {
  $cid = $_SESSION['booking']['citizen_id'] ?? null; $candidate = $_SESSION['booking']['phone_candidate'] ?? null; $existing = $_SESSION['booking']['existing_phone'] ?? null;
  if (!$cid || !$candidate || !$existing) { header('Location: ?step=3'); exit; }
  if ($_SERVER['REQUEST_METHOD']==='POST') { verify_csrf(); if (isset($_POST['use_new'])) { $_SESSION['booking']['phone'] = $candidate; unset($_SESSION['booking']['phone_candidate'], $_SESSION['booking']['existing_phone']); header('Location: ?step=4'); exit; } elseif (isset($_POST['use_old'])) { $_SESSION['booking']['phone'] = $existing; unset($_SESSION['booking']['phone_candidate'], $_SESSION['booking']['existing_phone']); header('Location: ?step=4'); exit; } }
  include __DIR__ . '/../app/views/user/step3_confirm.php'; exit;
}
if ($step === 4) {
  $dates = thursday_options();
  if ($_SERVER['REQUEST_METHOD']==='POST') { verify_csrf(); $date = $_POST['date'] ?? ''; if (!in_array($date, $dates, true)) { $error='เลือกได้เฉพาะวันพฤหัสบดี ภายใน 1 เดือน'; } else { $cid = $_SESSION['booking']['citizen_id'] ?? null; if (!$cid) { header('Location: ?step=2'); exit; } if (has_booking_on_date($pdo, $cid, $date)) { $error='คุณได้จองคิวในวันที่นี้แล้ว ไม่สามารถจองซ้ำได้'; } else { $_SESSION['booking']['date']=$date; header('Location: ?step=5'); exit; } } }
  include __DIR__ . '/../app/views/user/step4.php'; exit;
}
if ($step === 5) {
  $date = $_SESSION['booking']['date'] ?? null; if (!$date) { header('Location: ?step=4'); exit; } $slots = available_slots($pdo, $date);
  if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $time_slot = $_POST['time_slot'] ?? '';
    if (!in_array($time_slot, $slots['all'], true)) { $error='ช่วงเวลาไม่ถูกต้อง'; }
    elseif (in_array($time_slot, $slots['busy'], true)) { $error='ช่วงเวลานี้ถูกจองแล้ว'; }
    else { $_SESSION['booking']['time_slot']=$time_slot; header('Location: ?step=6'); exit; }
  }
  include __DIR__ . '/../app/views/user/step5.php'; exit;
}
if ($step === 6) {
  $data = $_SESSION['booking'] ?? [];
  if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    if (isset($_POST['cancel'])) { unset($_SESSION['booking']); header('Location: ' . base_url('')); exit; }
    $cid = $data['citizen_id'] ?? ''; $date = $data['date'] ?? '';
    if ($cid && $date && has_booking_on_date($pdo, $cid, $date)) { $error = 'ไม่สามารถยืนยันได้: คุณได้จองคิวในวันที่นี้อยู่แล้ว'; }
    else {
      $token = random_token(24);
      $stmt = $pdo->prepare("INSERT INTO appointments (full_name, citizen_id, phone, date, time_slot, ref_token) VALUES (:f,:c,:p,:d,:t,:r)");
      $stmt->execute([':f'=>$data['full_name']??'',
                      ':c'=>$data['citizen_id']??'',
                      ':p'=>$data['phone']??'',
                      ':d'=>$data['date']??'',
                      ':t'=>$data['time_slot']??'',
                      ':r'=>$token]);
      unset($_SESSION['booking']);
      header('Location: ' . base_url('success.php?ref=' . urlencode($token))); exit;
    }
  }
  include __DIR__ . '/../app/views/user/step6.php'; exit;
}
header('Location: ' . base_url(''));
