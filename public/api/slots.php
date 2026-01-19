<?php
require __DIR__ . '/../../app/includes/db.php';
require __DIR__ . '/../../app/includes/helpers.php';
require __DIR__ . '/../../app/includes/holidays.php';
header('Content-Type: application/json; charset=utf-8');
$date = $_GET['date'] ?? '';
$all = ['09:00-09:30','09:40-10:10','10:20-10:50','11:00-11:30','11:30-12:00'];
if (!is_booking_date_allowed($date, $PUBLIC_HOLIDAYS)) { echo json_encode(['busy' => [], 'all' => $all]); exit; }
$slots = available_slots($pdo, $date);
echo json_encode(['busy' => $slots['busy'], 'all' => $all]);
