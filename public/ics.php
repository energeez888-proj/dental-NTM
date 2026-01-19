<?php
require __DIR__ . '/../app/includes/db.php'; require __DIR__ . '/../app/includes/helpers.php';
$ref = $_GET['ref'] ?? ''; $stmt=$pdo->prepare("SELECT * FROM appointments WHERE ref_token=:r AND is_deleted=0 LIMIT 1"); $stmt->execute([':r'=>$ref]); $ap=$stmt->fetch();
if(!$ap){ http_response_code(404); echo "Not found"; exit; }
$parts=explode('-', $ap['time_slot']); $start=$parts[0]??'09:00'; $end=$parts[1]??'09:30';
$dt_date=$ap['date']; $tz=new DateTimeZone('Asia/Bangkok');
$dtstart=date_create_from_format('Y-m-d H:i',$dt_date.' '.$start,$tz);
$dtend=date_create_from_format('Y-m-d H:i',$dt_date.' '.$end,$tz);
$uid=$ap['ref_token'].'@dental-clinic.local';
$summary='นัดทำฟัน - '.($ap['service'] ?? 'ทันตกรรม').' | คลีนิกหมอครอบครัวหนองตาหมู่'; $location='คลีนิกหมอครอบครัวหนองตาหมู่';
$desc='ชื่อ: '.$ap['full_name']."\nบริการ: ".($ap['service'] ?? '')."\nโทร: ".$ap['phone']."\nอ้างอิง: ".$ap['ref_token'];
$fmt=function($dt){ return $dt->format('Ymd\THis'); };
$ics="BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//DentalClinic//Booking//TH\r\nCALSCALE:GREGORIAN\r\nMETHOD:PUBLISH\r\nBEGIN:VEVENT\r\n";
$ics.="UID:$uid\r\nSUMMARY:$summary\r\nDTSTART;TZID=Asia/Bangkok:".$fmt($dtstart)."\r\nDTEND;TZID=Asia/Bangkok:".$fmt($dtend)."\r\nDTSTAMP:".gmdate('Ymd\THis\Z')."\r\nLOCATION:$location\r\nDESCRIPTION:$desc\r\nEND:VEVENT\r\nEND:VCALENDAR\r\n";
header('Content-Type: text/calendar; charset=utf-8'); header('Content-Disposition: attachment; filename=\"appointment-'.$dt_date.'.ics\"'); echo $ics;
