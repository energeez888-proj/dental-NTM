<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function csrf_token() { if (!isset($_SESSION['_csrf_token'])) $_SESSION['_csrf_token'] = bin2hex(random_bytes(16)); return $_SESSION['_csrf_token']; }
function csrf_field() { return '<input type="hidden" name="_csrf" value="'.csrf_token().'">'; }
function verify_csrf() {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['_csrf']) || !hash_equals($_SESSION['_csrf_token'], $_POST['_csrf'])) { http_response_code(400); echo 'Invalid CSRF'; exit; }
  }
}
function base_url($path=''){ $config = require __DIR__ . '/config.php'; $base = rtrim($config['app']['base_url'],'/'); return $base.'/'.ltrim($path,'/'); }

function is_valid_thai_id($id) { if (!preg_match('/^[0-9]{13}$/', $id)) return false; $sum=0; for($i=0;$i<12;$i++){ $sum += intval($id[$i])*(13-$i);} $check=(11-($sum%11))%10; return $check===intval($id[12]); }
function is_valid_phone($phone){ return preg_match('/^0[0-9]{9}$/',$phone)===1; }
function is_valid_service($s){ return in_array($s, ['ตรวจฟัน','ขูดหินปูน','อุดฟัน'], true); }
function is_valid_coverage($c){
  $allowed=[
    'สิทธิหลักประกันสุขภาพถ้วนหน้า (บัตรทอง/ 30 บาท รักษาทุกโรค)',
    'สิทธิเบิกได้กรมบัญชีกลาง (เบิกได้ข้าราชการ/บุคคลในครอบครัว)',
    'สิทธิข้าราชการท้องถิ่น (อปท.)'
  ];
  return in_array($c, $allowed, true);
}

function thursday_options(){
  $tz = new DateTimeZone('Asia/Bangkok');
  $today = new DateTime('today', $tz);
  $end = (clone $today)->modify('+90 days');
  $dates = []; $d = clone $today;
  while ($d <= $end) { if ($d->format('N') == 4) { $dates[] = $d->format('Y-m-d'); } $d->modify('+1 day'); }
  return $dates;
}
function available_slots(PDO $pdo, $date){
  $all=['09:00-09:30','09:40-10:10','10:20-10:50','11:00-11:30','11:30-12:00'];
  $stmt=$pdo->prepare("SELECT time_slot FROM appointments WHERE date=:d AND is_deleted=0");
  $stmt->execute([':d'=>$date]);
  $busy=array_column($stmt->fetchAll(),'time_slot');
  return ['all'=>$all,'busy'=>$busy];
}
function random_token($len=24){ return bin2hex(random_bytes((int)($len/2))); }
function get_first_phone_for_cid(PDO $pdo, $citizen_id){ $stmt=$pdo->prepare("SELECT phone FROM appointments WHERE citizen_id=:c ORDER BY created_at ASC LIMIT 1"); $stmt->execute([':c'=>$citizen_id]); $row=$stmt->fetch(); return $row?$row['phone']:null; }
function has_booking_on_date(PDO $pdo, $citizen_id, $date){ $stmt=$pdo->prepare("SELECT COUNT(*) FROM appointments WHERE citizen_id=:c AND date=:d AND is_deleted=0"); $stmt->execute([':c'=>$citizen_id, ':d'=>$date]); return $stmt->fetchColumn()>0; }
function is_booking_date_allowed($date, array $holidays) {
  $tz = new DateTimeZone('Asia/Bangkok');
  $d = DateTime::createFromFormat('Y-m-d', $date, $tz);
  if (!$d) return false; $today = new DateTime('today', $tz); $max=(clone $today)->modify('+90 days');
  if ($d < $today || $d > $max) return false; if ($d->format('N') != 4) return false;
  return !in_array($d->format('Y-m-d'), $holidays, true);
}
