<?php
require_once 'config.php';
$link = mysqli_connect(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
date_default_timezone_set(TIMEZONE);
//mysqli_close($link);
