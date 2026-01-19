<?php
require __DIR__ . '/../../app/includes/db.php'; require __DIR__ . '/../../app/includes/helpers.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: dashboard.php'); exit; }
verify_csrf(); $id = intval($_POST['id'] ?? 0);
if ($id > 0) { $pdo->prepare("UPDATE appointments SET is_deleted=1 WHERE id=:id")->execute([':id'=>$id]); }
header('Location: dashboard.php?deleted=1');
