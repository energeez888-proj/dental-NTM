<?php
require __DIR__ . '/../app/includes/db.php';
require __DIR__ . '/../app/includes/helpers.php';
require __DIR__ . '/../app/includes/holidays.php';

$tz = new DateTimeZone('Asia/Bangkok');
$today = new DateTime('today', $tz);
$minDate = $today->format('Y-m-d');
$maxDate = (clone $today)->modify('+90 days')->format('Y-m-d');

$allSlots = ['09:00-09:30','09:40-10:10','10:20-10:50','11:00-11:30','11:30-12:00'];
$selectedDate = $_POST['date'] ?? '';
$slotsForDate = $selectedDate ? available_slots($pdo, $selectedDate) : ['all'=>$allSlots,'busy'=>[]];

$errors = []; $info_message = ''; $mode_confirm_phone = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf();
  $accept    = isset($_POST['accept_terms']);
  $full_name = trim($_POST['full_name'] ?? '');
  $citizen   = preg_replace('/\D+/', '', $_POST['citizen_id'] ?? '');
  $phone_in  = preg_replace('/\D+/', '', $_POST['phone'] ?? '');
  $date_in   = $_POST['date'] ?? '';
  $slot_in   = $_POST['time_slot'] ?? '';
  $service   = $_POST['service'] ?? '';
  $coverage  = $_POST['coverage'] ?? '';

  if (!$accept)                    $errors[] = 'กรุณายอมรับข้อกำหนดการใช้งาน';
  if ($full_name === '')           $errors[] = 'กรุณากรอกชื่อ-นามสกุล';
  if (!is_valid_thai_id($citizen)) $errors[] = 'เลขบัตรประชาชนไม่ถูกต้อง (13 หลัก)';
  if (!is_valid_phone($phone_in))  $errors[] = 'เบอร์โทรต้องขึ้นต้นด้วย 0 และยาว 10 หลัก';
  if (!is_valid_service($service)) $errors[] = 'กรุณาเลือกบริการทันตกรรม';
  if (!is_valid_coverage($coverage)) $errors[] = 'กรุณาเลือกสิทธิการรักษา';

  if (!is_booking_date_allowed($date_in, $PUBLIC_HOLIDAYS)) $errors[] = 'เลือกได้เฉพาะวันพฤหัสบดี (วันทำงาน) ภายใน 90 วัน และไม่ใช่วันหยุดนักขัตฤกษ์';

  if ($date_in && $slot_in) {
    $busyNow = available_slots($pdo, $date_in)['busy'];
    if (!in_array($slot_in, $allSlots, true)) $errors[] = 'ช่วงเวลาไม่ถูกต้อง';
    elseif (in_array($slot_in, $busyNow, true)) $errors[] = 'ช่วงเวลานี้ถูกจองแล้ว กรุณาเลือกช่วงเวลาอื่น';
  }

  if (!$errors && has_booking_on_date($pdo, $citizen, $date_in)) $errors[] = 'คุณได้จองคิวในวันที่นี้แล้ว ไม่สามารถจองซ้ำได้';

  $firstPhone = get_first_phone_for_cid($pdo, $citizen);
  $phone_confirm = $_POST['phone_confirm'] ?? null;
  if (!$errors && $firstPhone && $firstPhone !== $phone_in && !$phone_confirm) { $mode_confirm_phone = true; $info_message = "คุณเคยใช้เบอร์เดิม: {$firstPhone} ต้องการเปลี่ยนเป็นเบอร์ใหม่ {$phone_in} หรือไม่?"; }
  if (!$errors && $firstPhone && $firstPhone !== $phone_in && $phone_confirm) { if ($phone_confirm === 'use_old') $phone_in = $firstPhone; }

  if (!$errors && !$mode_confirm_phone) {
    $token = random_token(24);
    $stmt = $pdo->prepare("INSERT INTO appointments (full_name, citizen_id, phone, date, time_slot, service, coverage, ref_token)
                           VALUES (:f,:c,:p,:d,:t,:s,:g,:r)");
    $stmt->execute([':f'=>$full_name, ':c'=>$citizen, ':p'=>$phone_in, ':d'=>$date_in, ':t'=>$slot_in, ':s'=>$service, ':g'=>$coverage, ':r'=>$token]);
    header('Location: ' . base_url('success.php?ref=' . urlencode($token))); exit;
  }
  $selectedDate = $date_in; $slotsForDate = $selectedDate ? available_slots($pdo, $selectedDate) : ['all'=>$allSlots,'busy'=>[]];
}

// logo
$logo_main = __DIR__ . '/assets/moph-logo.png';
$logo = file_exists($logo_main) ? 'assets/moph-logo.png' : 'assets/moph-logo-placeholder.svg';
?>
<!doctype html><html lang="th"><head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <?php include __DIR__ . '/../app/includes/head.php'; ?>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>
  <title>จองคิวทันตกรรม (หน้าเดียว)</title>
</head>
<body class="grid-overlay min-h-screen">
  <div class="sparkle" style="left:10%; top:20%; animation-delay:0s;"></div>
  <div class="sparkle" style="left:80%; top:60%; animation-delay:.6s;"></div>
  <div class="sparkle" style="left:60%; top:15%; animation-delay:1.1s;"></div>

  <div class="fixed top-4 right-4 flex items-center gap-2">
  <a class="px-4 py-2 rounded-lg btn-neon bg-slate-900/90 text-white hover:bg-slate-800" href="<?= htmlspecialchars(base_url('admin/login.php')) ?>">เจ้าหน้าที่</a>
</div>

  <div class="max-w-3xl mx-auto p-6 pt-20">
    <div class="flex items-center gap-4 mb-6">
      <img src="<?= htmlspecialchars($logo) ?>" alt="ตรากระทรวงสาธารณสุข" class="w-14 h-14 glow-ring object-cover bg-white rounded-full p-1">
      <div><h1 class="text-2xl font-extrabold tracking-tight title-underline">ระบบจองคิวทันตกรรม</h1>
        <div class="flex items-center gap-3 mt-1">
          <p class="text-slate-600 m-0">คลีนิกหมอครอบครัวหนองตาหมู่</p>
          <a class="px-6 py-1.5 rounded-lg btn-neon bg-emerald-600 text-white hover:bg-emerald-500" href="<?= htmlspecialchars(base_url('user/login.php')) ?>">เข้าสู่ระบบ</a></div></div>
    </div>

    <div class="card-glass rounded-2xl shadow-glass p-6">
      <div class="mb-4 text-slate-700">
        <ul class="list-disc pl-6 space-y-1">
          <li>เปิดให้จองเฉพาะวันพฤหัสบดี (วันทำงาน) ล่วงหน้าไม่เกิน 90 วัน</li>
          <li>วันหยุดนักขัตฤกษ์ไม่เปิดให้จอง</li>
          <li>หนึ่งช่วงเวลาต่อหนึ่งคิว</li>
        </ul>
      </div>

      <?php if ($errors): ?>
        <div class="mb-4 px-3 py-2 rounded bg-rose-100/70 text-rose-700 border border-rose-300 animate-pulse">
          <?php foreach ($errors as $e): ?><div>• <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
        </div>
      <?php endif; ?>
      <?php if ($mode_confirm_phone): ?>
        <div class="mb-4 px-3 py-2 rounded bg-amber-50 text-amber-800 border border-amber-300"><?= htmlspecialchars($info_message) ?></div>
      <?php endif; ?>

      <form method="post" class="space-y-4">
        <?= csrf_field() ?>

        <label class="flex items-center gap-2">
          <input type="checkbox" name="accept_terms" value="1" <?= isset($_POST['accept_terms']) ? 'checked' : '' ?>>
          <span>ฉันได้อ่านและยอมรับข้อกำหนด</span>
        </label>

        <div>
          <label class="block mb-1 font-medium">ชื่อ-นามสกุล</label>
          <input class="w-full rounded-xl px-3 py-2 input-fx" name="full_name" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block mb-1 font-medium">เลขบัตรประชาชน (13 หลัก)</label>
            <input class="w-full rounded-xl px-3 py-2 input-fx" name="citizen_id" maxlength="13" required value="<?= htmlspecialchars($_POST['citizen_id'] ?? '') ?>">
          </div>
          <div>
            <label class="block mb-1 font-medium">เบอร์โทร (ขึ้นต้น 0 และยาว 10 หลัก)</label>
            <input class="w-full rounded-xl px-3 py-2 input-fx" name="phone" maxlength="10" pattern="0[0-9]{9}" inputmode="tel" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block mb-1 font-medium">วันที่นัด</label>
            <input id="datePicker" name="date" class="w-full rounded-xl px-3 py-2 input-fx" placeholder="เลือกวันพฤหัสบดี (วันทำงาน)" readonly required value="<?= htmlspecialchars($selectedDate) ?>">
            <p class="text-xs text-slate-500 mt-1">เลือกได้เฉพาะวันพฤหัสบดี ภายในช่วง <?= htmlspecialchars($minDate) ?> – <?= htmlspecialchars($maxDate) ?> และไม่ใช่วันหยุด</p>
          </div>
          <div>
            <label class="block mb-1 font-medium">ท่านต้องการรับบริการทันตกรรมด้านใด</label>
            <?php $sv = $_POST['service'] ?? ''; ?>
            <select name="service" class="w-full rounded-xl px-3 py-2 input-fx" required>
              <option value="" disabled <?= $sv===''?'selected':'' ?>>— กรุณาเลือกบริการ —</option>
              <option value="ตรวจฟัน" <?= $sv==='ตรวจฟัน'?'selected':'' ?>>ตรวจฟัน</option>
              <option value="ขูดหินปูน" <?= $sv==='ขูดหินปูน'?'selected':'' ?>>ขูดหินปูน</option>
              <option value="อุดฟัน" <?= $sv==='อุดฟัน'?'selected':'' ?>>อุดฟัน</option>
            </select>
          </div>
        </div>

        <!-- ช่วงเวลา -->
        <div>
          <label class="block mb-1 font-medium">ช่วงเวลา</label>
          <div id="slotList" class="grid grid-cols-1 gap-2"></div>
        </div>

        <!-- ย้าย "สิทธิการรักษา" มาไว้ท้ายฟอร์ม (ตรงนี้) -->
        <div>
          <label class="block mb-1 font-medium">สิทธิการรักษา</label>
          <?php $cv = $_POST['coverage'] ?? ''; ?>
          <select name="coverage" class="w-full rounded-xl px-3 py-2 input-fx" required>
            <option value="" disabled <?= $cv===''?'selected':'' ?>>— กรุณาเลือกสิทธิ —</option>
            <option value="สิทธิหลักประกันสุขภาพถ้วนหน้า (บัตรทอง/ 30 บาท รักษาทุกโรค)" <?= $cv==='สิทธิหลักประกันสุขภาพถ้วนหน้า (บัตรทอง/ 30 บาท รักษาทุกโรค)'?'selected':'' ?>>สิทธิหลักประกันสุขภาพถ้วนหน้า (บัตรทอง/ 30 บาท รักษาทุกโรค)</option>
            <option value="สิทธิเบิกได้กรมบัญชีกลาง (เบิกได้ข้าราชการ/บุคคลในครอบครัว)" <?= $cv==='สิทธิเบิกได้กรมบัญชีกลาง (เบิกได้ข้าราชการ/บุคคลในครอบครัว)'?'selected':'' ?>>สิทธิเบิกได้กรมบัญชีกลาง (เบิกได้ข้าราชการ/บุคคลในครอบครัว)</option>
            <option value="สิทธิข้าราชการท้องถิ่น (อปท.)" <?= $cv==='สิทธิข้าราชการท้องถิ่น (อปท.)'?'selected':'' ?>>สิทธิข้าราชการท้องถิ่น (อปท.)</option>
          </select>
          <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded mt-2 px-3 py-2">หมายเหตุ สิทธิประกันสังคม รับบริการได้ที่โรงพยาบาลนางรอง</p>
        </div>

        <?php if ($mode_confirm_phone): ?>
          <input type="hidden" name="phone_confirm" id="phone_confirm" value="">
          <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 rounded-lg btn-neon bg-emerald-600 text-white" onclick="document.getElementById('phone_confirm').value='use_new'">ใช้เบอร์ใหม่</button>
            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300" onclick="document.getElementById('phone_confirm').value='use_old'">ใช้เบอร์เดิม</button>
          </div>
        <?php else: ?>
          <div class="flex gap-2">
            <button class="px-5 py-2.5 rounded-xl btn-neon bg-emerald-600 text-white">ยืนยันการจอง</button>
            <a class="px-5 py-2.5 rounded-xl bg-white/70 border hover:bg-white" href="<?= htmlspecialchars(base_url('')) ?>">ล้างค่า</a>
          </div>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <div id="toasts" class="toast-wrap"></div>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>
  <script>
    const HOLIDAYS = <?= json_encode(array_values($PUBLIC_HOLIDAYS)) ?>;
    const MIN_DATE = '<?= $minDate ?>'; const MAX_DATE = '<?= $maxDate ?>';
    const ALL = <?= json_encode($allSlots) ?>;

    function ymd(d){ const y=d.getFullYear(), m=('0'+(d.getMonth()+1)).slice(-2), dd=('0'+d.getDate()).slice(-2); return `${y}-${m}-${dd}`; }
    const fp = flatpickr('#datePicker', {
      locale: 'th', dateFormat: 'Y-m-d', minDate: MIN_DATE, maxDate: MAX_DATE,
      disable: [ (date) => (date.getDay() !== 4) || (HOLIDAYS.indexOf(ymd(date)) !== -1) ],
      onDayCreate: (dObj, dStr, fp, dayElem) => { const dt = dayElem.dateObj; if (!dt) return; if (HOLIDAYS.indexOf(ymd(dt)) !== -1) { dayElem.title='วันหยุด'; dayElem.classList.add('flatpickr-disabled'); } },
      onChange: (selectedDates) => { if (selectedDates && selectedDates[0]) refreshSlots(ymd(selectedDates[0])); }
    });

    const slotList = document.getElementById('slotList');
    function renderSlots(busy){
      slotList.innerHTML='';
      ALL.forEach(slot => {
        const disabled = busy.includes(slot);
        const lbl = document.createElement('label');
        lbl.className = 'flex items-center gap-3 p-3 rounded-xl border ' + (disabled ? 'bg-slate-100 text-slate-400' : 'bg-white/70');
        const input = document.createElement('input');
        input.type='radio'; input.name='time_slot'; input.value=slot; if(disabled) input.disabled=true;
        const span = document.createElement('span'); span.textContent = slot + (disabled ? ' (ไม่ว่าง)' : '');
        lbl.appendChild(input); lbl.appendChild(span); slotList.appendChild(lbl);
      });
    }
    async function refreshSlots(dateStr){
      try{ const res = await fetch('api/slots.php?date='+encodeURIComponent(dateStr)); const data = await res.json(); renderSlots(data.busy || []); }
      catch(e){ console.error(e); }
    }
    (function bootstrap(){ const v=document.getElementById('datePicker').value; if(v) refreshSlots(v); })();

    function pushToast(msg, type='info', timeout=4000){
      const wrap = document.getElementById('toasts'); const el = document.createElement('div');
      el.className = 'toast ' + (type==='error' ? 'error' : ''); el.textContent = msg; wrap.appendChild(el);
      setTimeout(()=>{ el.style.transition='opacity .4s, transform .4s'; el.style.opacity='0'; el.style.transform='translateY(10px)'; setTimeout(()=>el.remove(), 450); }, timeout);
    }
    <?php if (!empty($errors)): ?> (<?= json_encode($errors, JSON_UNESCAPED_UNICODE) ?>).forEach(e => pushToast(e, 'error')); <?php endif; ?>
    <?php if (!empty($info_message) && $mode_confirm_phone): ?> pushToast(<?= json_encode($info_message, JSON_UNESCAPED_UNICODE) ?>, 'info', 6000); <?php endif; ?>
  </script>
</body></html>
