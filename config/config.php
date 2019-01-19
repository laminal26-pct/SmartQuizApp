<?php
// Configuration Apps
define('APPDEBUG', 'DEVELOPMENT'); // DEVELOPMENT for Build Apps And PRODUCTION for Release Apps
define('APPNAME', 'SMART QUIZ APP');
define('BASE_URL', 'http://192.168.43.190/SmartQuizApp'); // URL Apps
define('TIMEZONE', 'Asia/Jakarta'); // Timezone Apps

// Configuration Database
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_DATABASE', 'db_smartquizapp');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');

// Configuration Mailler
define('MAIL_DRIVER', 'smtp');
define('MAIL_HOST', 'smtp.mailtrap.io');
define('MAIL_PORT', '2525');
define('MAIL_USERNAME', null);
define('MAIL_PASSWORD', null);
define('MAIL_ENCRYPTION', null);

$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
define('FIRST_PART',@$components[1]);
define('SECOND_PART',@$components[2]);
define('THIRD_PART',@$components[3]);
define('FOURTH_PART',@$components[4]);
