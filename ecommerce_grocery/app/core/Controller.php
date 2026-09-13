<?php
/**
 * Base Controller
 * Provides view rendering, JSON output, redirects, and role-based
 * access-control helpers used by every controller in the app.
 */
abstract class Controller
{
    /** Render a view file inside the shared layout */
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data);
        $viewFile = APP_ROOT . '/app/views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            die("View not found: {$view}");
        }

        if ($layout === 'none') {
            require $viewFile;
            return;
        }

        require APP_ROOT . '/app/views/layouts/header.php';
        require $viewFile;
        require APP_ROOT . '/app/views/layouts/footer.php';
    }

    /** Output JSON and stop execution — used by AJAX endpoints */
    protected function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . '/public/index.php?url=' . ltrim($path, '/'));
        exit;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function input(string $key, $default = null)
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        return is_string($value) ? trim($value) : $value;
    }

    /** Flash a message to session for one-time display after redirect */
    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /** Require the user to be logged in, optionally with a specific role */
    protected function requireRole($roles): void
    {
        $user = $this->currentUser();
        if (!$user) {
            $this->setFlash('error', 'Please log in to continue.');
            $this->redirect('auth/login');
        }
        $roles = is_array($roles) ? $roles : [$roles];
        if (!in_array($user['role'], $roles, true)) {
            http_response_code(403);
            die('403 Forbidden: you do not have access to this page.');
        }
        if ((int)($user['is_active'] ?? 1) === 0) {
            session_destroy();
            die('Your account has been deactivated. Contact platform admin.');
        }
    }

    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function verifyCsrf(): bool
    {
        $token = $_POST['csrf_token'] ?? '';
        return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}
