<?php
/**
 * Global application configuration.
 * BASE_URL is auto-detected so the project works regardless of the
 * folder name it is placed under inside htdocs.
 */
require_once __DIR__ . '/database.php';

define('APP_ROOT', dirname(__DIR__));

$scriptDir = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'])));
if ($scriptDir === '/' || $scriptDir === '\\') {
    $scriptDir = '';
}
define('BASE_URL', $scriptDir); // e.g. /ecommerce_grocery

define('UPLOAD_URL', BASE_URL . '/public/uploads');
define('UPLOAD_PATH', APP_ROOT . '/public/uploads');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
