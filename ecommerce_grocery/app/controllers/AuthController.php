<?php
require_once APP_ROOT . '/app/core/Controller.php';

class AuthController extends Controller
{
    private UserModel $userModel;
    private SellerModel $sellerModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->sellerModel = new SellerModel();
    }

    public function login(): void
    {
        if ($this->isPost()) {
            $email = $this->input('email');
            $password = $this->input('password');

            $v = new Validator();
            $v->required($email, 'Email')->email($email)->required($password, 'Password');

            if ($v->fails()) {
                $this->view('auth/login', ['errors' => $v->errors(), 'old' => ['email' => $email]], 'none');
                return;
            }

            $user = $this->userModel->findByEmail($email);
            if (!$user || !password_verify($password, $user['password_hash'])) {
                $this->view('auth/login', ['errors' => ['Invalid email or password.'], 'old' => ['email' => $email]], 'none');
                return;
            }
            if (!$user['is_active']) {
                $this->view('auth/login', ['errors' => ['Your account has been deactivated. Contact the platform admin.'], 'old' => ['email' => $email]], 'none');
                return;
            }
            if ($user['role'] === 'seller') {
                $seller = $this->sellerModel->findByUserId($user['id']);
                if ($seller && $seller['is_approved'] === 'pending') {
                    $this->view('auth/login', ['errors' => ['Your seller account is awaiting admin approval.'], 'old' => ['email' => $email]], 'none');
                    return;
                }
                if ($seller && $seller['is_approved'] === 'rejected') {
                    $this->view('auth/login', ['errors' => ['Your seller application was rejected: ' . $seller['rejection_reason']], 'old' => ['email' => $email]], 'none');
                    return;
                }
                if ($seller && $seller['is_approved'] === 'suspended') {
                    $this->view('auth/login', ['errors' => ['Your seller account has been suspended.'], 'old' => ['email' => $email]], 'none');
                    return;
                }
            }

            $_SESSION['user'] = [
                'id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'],
                'role' => $user['role'], 'is_active' => $user['is_active'], 'profile_pic' => $user['profile_pic'],
            ];

            $this->setFlash('success', 'Welcome back, ' . $user['name'] . '!');
            $this->redirectByRole($user['role']);
            return;
        }
        $this->view('auth/login', ['errors' => [], 'old' => []], 'none');
    }

    private function redirectByRole(string $role): void
    {
        $map = ['customer' => 'customer/dashboard', 'seller' => 'seller/dashboard', 'delivery_manager' => 'delivery/dashboard', 'admin' => 'admin/dashboard'];
        $this->redirect($map[$role] ?? 'home/index');
    }

    public function registerCustomer(): void
    {
        if ($this->isPost()) {
            $name = $this->input('name');
            $email = $this->input('email');
            $phone = $this->input('phone');
            $password = $this->input('password');

            $v = new Validator();
            $v->required($name, 'Name')->required($email, 'Email')->email($email)
              ->required($phone, 'Phone')->required($password, 'Password')->minLength($password, 6, 'Password');

            $errors = $v->errors();
            if (!$errors && $this->userModel->findByEmail($email)) {
                $errors[] = 'An account with this email already exists.';
            }

            if (!empty($errors)) {
                $this->view('auth/register_customer', ['errors' => $errors, 'old' => compact('name', 'email', 'phone')], 'none');
                return;
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $this->userModel->create($name, $email, $hash, $phone, 'customer');
            $this->setFlash('success', 'Registration successful! Please log in.');
            $this->redirect('auth/login');
            return;
        }
        $this->view('auth/register_customer', ['errors' => [], 'old' => []], 'none');
    }

    public function registerSeller(): void
    {
        if ($this->isPost()) {
            $name = $this->input('name');
            $email = $this->input('email');
            $phone = $this->input('phone');
            $password = $this->input('password');
            $shopName = $this->input('shop_name');
            $shopDesc = $this->input('shop_description');
            $address = $this->input('address');

            $v = new Validator();
            $v->required($name, 'Name')->required($email, 'Email')->email($email)
              ->required($phone, 'Phone')->required($password, 'Password')->minLength($password, 6, 'Password')
              ->required($shopName, 'Shop name')->required($address, 'Address');

            $errors = $v->errors();
            if (!$errors && $this->userModel->findByEmail($email)) {
                $errors[] = 'An account with this email already exists.';
            }

            if (!empty($errors)) {
                $this->view('auth/register_seller', ['errors' => $errors, 'old' => compact('name', 'email', 'phone', 'shopName', 'shopDesc', 'address')], 'none');
                return;
            }

            $logoPath = null;
            if (!empty($_FILES['shop_logo']['name'])) {
                $logoPath = $this->handleUpload('shop_logo', UPLOAD_PATH . '/shops');
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $userId = $this->userModel->create($name, $email, $hash, $phone, 'seller');
            $this->sellerModel->create($userId, $shopName, $shopDesc, $address, $logoPath);

            $this->setFlash('success', 'Registration submitted! Your shop is pending admin approval before you can log in.');
            $this->redirect('auth/login');
            return;
        }
        $this->view('auth/register_seller', ['errors' => [], 'old' => []], 'none');
    }

    private function handleUpload(string $fieldName, string $destDir): ?string
    {
        if (empty($_FILES[$fieldName]['name'])) return null;
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) return null;
        if (!is_dir($destDir)) mkdir($destDir, 0777, true);
        $filename = uniqid('img_', true) . '.' . $ext;
        $target = $destDir . '/' . $filename;
        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $target)) {
            return basename($destDir) . '/' . $filename;
        }
        return null;
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: ' . BASE_URL . '/public/index.php?url=auth/login');
        exit;
    }
}
