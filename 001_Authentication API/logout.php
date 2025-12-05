<?php
require_once 'config/database.php';
require_once 'includes/header.php';

logout();
header('Location: login.php');
exit();
?>