<?php
session_start();
session_destroy();
require_once '../../path.php';
require_once (ABSPATH . 'config/config.php');
require_once (ABSPATH . 'config/database.php');
$url = BASE_URL;
echo "<script>var url = '$url'; window.location.href= url;</script>";
?>
