<?php
require __DIR__ . '/../../app/includes/helpers.php';
unset($_SESSION['user_login'], $_SESSION['user_cid'], $_SESSION['user_phone']);
header('Location: login.php');
