<?php
/*
|--------------------------------------------------------------------------
| OWSA-INV V2
|--------------------------------------------------------------------------
| Author: Siamon Hasan
| Project Name: OSWA-INV
| Version: v2
| Official page: http://oswapp.com/
|--------------------------------------------------------------------------
*/

// Use deployment-provided database settings when available; retain local
// defaults for development environments such as XAMPP.
$dbHost = getenv('DB_HOST');
$dbUser = getenv('DB_USER');
$dbPass = getenv('DB_PASS');
$dbName = getenv('DB_NAME');

define('DB_HOST', $dbHost !== false && $dbHost !== '' ? $dbHost : 'localhost');
define('DB_USER', $dbUser !== false && $dbUser !== '' ? $dbUser : 'root');
define('DB_PASS', $dbPass !== false ? $dbPass : '');
define('DB_NAME', $dbName !== false && $dbName !== '' ? $dbName : 'inventory_system');

?>
