<?php
/**
 * Front Controller
 * Every request hits this file. It boots config, autoloads core/model
 * classes, and hands off routing to the Router.
 */
require_once __DIR__ . '/../config/config.php';
require_once APP_ROOT . '/app/core/Database.php';
require_once APP_ROOT . '/app/core/Model.php';
require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/core/Validator.php';
require_once APP_ROOT . '/app/core/Router.php';

// Autoload models on demand
spl_autoload_register(function ($class) {
    $modelFile = APP_ROOT . "/app/models/{$class}.php";
    if (file_exists($modelFile)) {
        require_once $modelFile;
    }
});

$url = $_GET['url'] ?? '';
$router = new Router();
$router->dispatch($url);
