<?php
/**
 * Router
 * Reads ?url=controller/action/param1/param2... and dispatches to the
 * matching Controller class + method. Keeping routing query-string based
 * (rather than requiring mod_rewrite) makes the project drop into any
 * XAMPP htdocs folder without extra Apache configuration.
 */
class Router
{
    private array $map = [
        'auth'     => 'AuthController',
        'customer' => 'CustomerController',
        'seller'   => 'SellerController',
        'delivery' => 'DeliveryController',
        'admin'    => 'AdminController',
        'api'      => 'ApiController',
        'home'     => 'HomeController',
    ];

    public function dispatch(string $url): void
    {
        $url = trim($url, '/');
        $parts = $url === '' ? ['home', 'index'] : explode('/', $url);

        $controllerKey = strtolower($parts[0]);
        $action = isset($parts[1]) && $parts[1] !== '' ? $parts[1] : 'index';
        $params = array_slice($parts, 2);

        if (!isset($this->map[$controllerKey])) {
            http_response_code(404);
            echo "404 - Page not found.";
            return;
        }

        $controllerClass = $this->map[$controllerKey];
        $controllerFile = APP_ROOT . "/app/controllers/{$controllerClass}.php";

        if (!file_exists($controllerFile)) {
            http_response_code(404);
            echo "404 - Controller not found.";
            return;
        }

        require_once $controllerFile;
        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            http_response_code(404);
            echo "404 - Action not found.";
            return;
        }

        call_user_func_array([$controller, $action], $params);
    }
}
