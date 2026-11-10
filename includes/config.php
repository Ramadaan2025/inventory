<?php
/*
|--------------------------------------------------------------------------
| OWSA-INV V2
|--------------------------------------------------------------------------
| Author: Siamon Hasan
| Project Name: OSWA-INV
| Version: v2
|--------------------------------------------------------------------------
*/

// Prefer EthioDeploy's managed DATABASE_URL. Individual DB_* variables can
// override it; local defaults keep XAMPP and other development environments
// working without deployment configuration.
$databaseUrl = getenv('DATABASE_URL');
$databaseParts = $databaseUrl !== false && $databaseUrl !== ''
    ? parse_url($databaseUrl)
    : false;

$urlHost = is_array($databaseParts) ? ($databaseParts['host'] ?? '') : '';
$urlUser = is_array($databaseParts) && isset($databaseParts['user'])
    ? rawurldecode($databaseParts['user'])
    : '';
$urlPass = is_array($databaseParts) && isset($databaseParts['pass'])
    ? rawurldecode($databaseParts['pass'])
    : '';
$urlName = is_array($databaseParts) && isset($databaseParts['path'])
    ? rawurldecode(ltrim($databaseParts['path'], '/'))
    : '';
$urlPort = is_array($databaseParts) ? ($databaseParts['port'] ?? 3306) : 3306;

$dbHost = getenv('DB_HOST');
$dbUser = getenv('DB_USER');
$dbPass = getenv('DB_PASS');
$dbName = getenv('DB_NAME');
$dbPort = getenv('DB_PORT');

define('DB_HOST', $dbHost !== false && $dbHost !== '' ? $dbHost : ($urlHost ?: 'localhost'));
define('DB_USER', $dbUser !== false && $dbUser !== '' ? $dbUser : ($urlUser ?: 'root'));
define('DB_PASS', $dbPass !== false ? $dbPass : $urlPass);
define('DB_NAME', $dbName !== false && $dbName !== '' ? $dbName : ($urlName ?: 'inventory_system'));
define('DB_PORT', $dbPort !== false && $dbPort !== '' ? (int)$dbPort : (int)$urlPort);

?>
