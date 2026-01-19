<?php
require __DIR__ . '/../../app/includes/db.php'; require __DIR__ . '/../../app/includes/helpers.php';
if (!isset($_SESSION['user_login']) || $_SESSION['user_login'] !== true) { header('Location: login.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: home.php'); exit; }
verify_csrf(); $cid = $_SESSION['user_cid']; $id = intval($_POST['id'] ?? 0);
$stmt = $pdo->prepare("SELECT id, citizen_id, date, is_deleted FROM appointments WHERE id=:id LIMIT 1"); $stmt->execute([':id' => $id]); $ap = $stmt->fetch();
if (!$ap || $ap['citizen_id'] !== $cid) { http_response_code(403); echo "Forbidden"; exit; }
$today = new DateTime('today', new DateTimeZone('Asia/Bangkok')); $apDate = DateTime::createFromFormat('Y-m-d', $ap['date'], new DateTimeZone('Asia/Bangkok'));
if ($apDate < $today) { header('Location: home.php'); exit; }
if ((int)$ap['is_deleted'] === 0) { $pdo->prepare("UPDATE appointments SET is_deleted=1 WHERE id=:id")->execute([':id'=>$id]); }
header('Location: home.php?canceled=1');
