<?php
require_once APP_ROOT . '/app/core/Controller.php';

class AdminController extends Controller
{
    private UserModel $userModel;
    private SellerModel $sellerModel;
    private CategoryModel $categoryModel;
    private ProductModel $productModel;
    private OrderModel $orderModel;
    private DisputeModel $disputeModel;
    private CouponModel $couponModel;
    private AnnouncementModel $announcementModel;
    private SettingModel $settingModel;
    private NotificationModel $notificationModel;
    private DeliveryAgentModel $agentModel;
    private DeliveryAssignmentModel $assignmentModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->sellerModel = new SellerModel();
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
        $this->orderModel = new OrderModel();
        $this->disputeModel = new DisputeModel();
        $this->couponModel = new CouponModel();
        $this->announcementModel = new AnnouncementModel();
        $this->settingModel = new SettingModel();
        $this->notificationModel = new NotificationModel();
        $this->agentModel = new DeliveryAgentModel();
        $this->assignmentModel = new DeliveryAssignmentModel();
        $this->requireRole('admin');
    }

    public function dashboard(): void
    {
        $stats = [
            'customers' => $this->userModel->countByRole('customer'),
            'sellers' => $this->sellerModel->countApproved(),
            'pendingSellers' => count($this->sellerModel->all('pending')),
            'deliveryManagers' => $this->userModel->countByRole('delivery_manager'),
            'ordersToday' => $this->orderModel->countToday(),
            'revenueMonth' => $this->orderModel->revenueThisMonth(),
            'openDisputes' => count($this->disputeModel->all('open')),
        ];
        $topSellers = $this->orderModel->topSellersPlatform(5);
        $topCategories = $this->orderModel->topCategoriesPlatform(5);
        $this->view('admin/dashboard', compact('stats', 'topSellers', 'topCategories'));
    }

    // ---------------- Sellers ----------------
    public function sellers(): void
    {
        $status = $this->input('status', '');
        $items = $this->sellerModel->all($status);
        $this->view('admin/sellers', ['items' => $items, 'status' => $status]);
    }

    public function sellerDetail(int $id): void
    {
        $seller = $this->sellerModel->findById($id);
        if (!$seller) { $this->redirect('admin/sellers'); return; }
        $products = $this->productModel->bySeller($id);
        $this->view('admin/seller_detail', compact('seller', 'products'));
    }

    public function approveSeller(int $id): void
    {
        $this->sellerModel->updateApproval($id, 'approved');
        $seller = $this->sellerModel->findById($id);
        $this->notificationModel->create($seller['user_id'], 'Congratulations! Your shop "' . $seller['shop_name'] . '" has been approved.');
        $this->setFlash('success', 'Seller approved.');
        $this->redirect('admin/sellers');
    }

    public function rejectSeller(int $id): void
    {
        $reason = $this->input('reason', 'Application did not meet platform requirements.');
        $this->sellerModel->updateApproval($id, 'rejected', $reason);
        $this->setFlash('success', 'Seller application rejected.');
        $this->redirect('admin/sellers');
    }

    public function suspendSeller(int $id): void
    {
        $this->sellerModel->updateApproval($id, 'suspended');
        $seller = $this->sellerModel->findById($id);
        $this->notificationModel->create($seller['user_id'], 'Your shop has been suspended by the platform admin.');
        $this->setFlash('success', 'Seller suspended.');
        $this->redirect('admin/sellers');
    }

    public function reactivateSeller(int $id): void
    {
        $this->sellerModel->updateApproval($id, 'approved');
        $this->setFlash('success', 'Seller reactivated.');
        $this->redirect('admin/sellers');
    }

    public function updateCommission(): void
    {
        $id = (int)$this->input('seller_id');
        $rate = (float)$this->input('commission_rate');
        if ($rate < 0 || $rate > 100) {
            $this->setFlash('error', 'Commission rate must be between 0 and 100.');
        } else {
            $this->sellerModel->updateCommission($id, $rate);
            $this->setFlash('success', 'Commission rate updated.');
        }
        $this->redirect('admin/sellerDetail/' . $id);
    }

    // ---------------- Categories ----------------
    public function categories(): void
    {
        $items = $this->categoryModel->all();
        $topLevel = $this->categoryModel->topLevel();
        $this->view('admin/categories', compact('items', 'topLevel'));
    }

    public function addCategory(): void
    {
        $name = $this->input('name');
        $desc = $this->input('description', '');
        $parentId = $this->input('parent_id') ?: null;

        if (trim($name) === '') {
            $this->setFlash('error', 'Category name is required.');
        } else {
            $this->categoryModel->create($parentId ? (int)$parentId : null, $name, $desc);
            $this->setFlash('success', 'Category added.');
        }
        $this->redirect('admin/categories');
    }

    public function editCategory(int $id): void
    {
        if ($this->isPost()) {
            $parentId = $this->input('parent_id') ?: null;
            $this->categoryModel->update($id, $parentId ? (int)$parentId : null, $this->input('name'), $this->input('description', ''));
            $this->setFlash('success', 'Category updated.');
        }
        $this->redirect('admin/categories');
    }

    public function deleteCategory(int $id): void
    {
        $ok = $this->categoryModel->delete($id);
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Category deleted.' : 'Cannot delete: products exist in this category.');
        $this->redirect('admin/categories');
    }

    // ---------------- Customers ----------------
    public function customers(): void
    {
        $search = $this->input('search', '');
        $items = $this->userModel->listByRole('customer', $search);
        $this->view('admin/customers', ['items' => $items, 'search' => $search]);
    }

    public function toggleCustomer(int $id): void
    {
        $user = $this->userModel->findById($id);
        if ($user) {
            $this->userModel->setActive($id, $user['is_active'] ? 0 : 1);
            $this->setFlash('success', 'Customer account status updated.');
        }
        $this->redirect('admin/customers');
    }

    // ---------------- Delivery Managers ----------------
    public function deliveryManagers(): void
    {
        $items = $this->userModel->listByRole('delivery_manager');
        $this->view('admin/delivery_managers', compact('items'));
    }

    public function addDeliveryManager(): void
    {
        $name = $this->input('name');
        $email = $this->input('email');
        $phone = $this->input('phone');
        $password = $this->input('password');

        $v = new Validator();
        $v->required($name, 'Name')->required($email, 'Email')->email($email)->required($phone, 'Phone')
          ->required($password, 'Password')->minLength($password, 6, 'Password');

        if ($v->fails() || $this->userModel->findByEmail($email)) {
            $this->setFlash('error', $v->fails() ? implode(' ', $v->errors()) : 'Email already exists.');
        } else {
            $this->userModel->create($name, $email, password_hash($password, PASSWORD_DEFAULT), $phone, 'delivery_manager');
            $this->setFlash('success', 'Delivery manager account created.');
        }
        $this->redirect('admin/deliveryManagers');
    }

    public function toggleDeliveryManager(int $id): void
    {
        $user = $this->userModel->findById($id);
        if ($user) {
            $this->userModel->setActive($id, $user['is_active'] ? 0 : 1);
            $this->setFlash('success', 'Account status updated.');
        }
        $this->redirect('admin/deliveryManagers');
    }

    // ---------------- Products ----------------
    public function products(): void
    {
        $keyword = $this->input('keyword', '');
        $categoryId = $this->input('category_id', '');
        $sellerId = $this->input('seller_id', '');
        $items = $this->productModel->adminSearch($keyword, $categoryId, $sellerId);
        $categories = $this->categoryModel->all();
        $sellers = $this->sellerModel->all('approved');
        $this->view('admin/products', compact('items', 'categories', 'sellers', 'keyword', 'categoryId', 'sellerId'));
    }

    public function removeProduct(int $id): void
    {
        $this->productModel->adminRemove($id);
        $this->setFlash('success', 'Product removed from marketplace.');
        $this->redirect('admin/products');
    }

    // ---------------- Featured products ----------------
    public function featured(): void
    {
        $featured = $this->productModel->featured();
        $categories = $this->categoryModel->all();
        $allProducts = $this->productModel->adminSearch();
        $this->view('admin/featured', compact('featured', 'allProducts'));
    }

    public function addFeatured(): void
    {
        $productId = (int)$this->input('product_id');
        $this->productModel->addFeatured($productId);
        $this->setFlash('success', 'Product added to featured list.');
        $this->redirect('admin/featured');
    }

    public function removeFeatured(int $id): void
    {
        $this->productModel->removeFeatured($id);
        $this->setFlash('success', 'Removed from featured list.');
        $this->redirect('admin/featured');
    }

    // ---------------- Orders ----------------
    public function orders(): void
    {
        $filters = [
            'status' => $this->input('status', ''),
            'date_from' => $this->input('date_from', ''),
            'date_to' => $this->input('date_to', ''),
            'customer' => $this->input('customer', ''),
        ];
        $items = $this->orderModel->allWithFilters($filters);
        $this->view('admin/orders', ['items' => $items, 'filters' => $filters]);
    }

    public function orderDetail(int $id): void
    {
        $order = $this->orderModel->find($id);
        if (!$order) { $this->redirect('admin/orders'); return; }
        $items = $this->orderModel->items($id);
        $this->view('admin/order_detail', compact('order', 'items'));
    }

    // ---------------- Disputes ----------------
    public function disputes(): void
    {
        $status = $this->input('status', '');
        $items = $this->disputeModel->all($status);
        $this->view('admin/disputes', ['items' => $items, 'status' => $status]);
    }

    public function resolveDispute(): void
    {
        $id = (int)$this->input('dispute_id');
        $note = $this->input('note', 'Resolved by platform admin.');
        $dispute = $this->disputeModel->find($id);
        if ($dispute) {
            $this->disputeModel->resolve($id, $note);
            $this->notificationModel->create($dispute['customer_id'], 'Your dispute has been resolved: ' . $note);
            $this->setFlash('success', 'Dispute marked as resolved.');
        }
        $this->redirect('admin/disputes');
    }

    // ---------------- Platform coupons ----------------
    public function coupons(): void
    {
        $items = $this->couponModel->platformCoupons();
        $this->view('admin/coupons', compact('items'));
    }

    public function addPlatformCoupon(): void
    {
        $code = strtoupper(trim($this->input('code')));
        $pct = (float)$this->input('discount_pct');
        $maxUses = (int)$this->input('max_uses', 100);
        $minOrder = (float)$this->input('min_order_amount', 0);
        $validUntil = $this->input('valid_until');

        $v = new Validator();
        $v->required($code, 'Code')->required($pct, 'Discount %')->min($pct, 1, 'Discount %')
          ->required($validUntil, 'Valid until')->date($validUntil, 'Valid until');

        if ($v->fails() || $this->couponModel->findByCode($code)) {
            $this->setFlash('error', $v->fails() ? implode(' ', $v->errors()) : 'Coupon code already exists.');
        } else {
            $this->couponModel->create(null, true, $code, $pct, $maxUses, $minOrder, $validUntil);
            $this->setFlash('success', 'Platform coupon created.');
        }
        $this->redirect('admin/coupons');
    }

    public function toggleCoupon(int $id): void
    {
        $this->couponModel->toggleActive($id);
        $this->redirect('admin/coupons');
    }

    // ---------------- Announcements ----------------
    public function announcements(): void
    {
        $items = $this->announcementModel->all();
        $this->view('admin/announcements', compact('items'));
    }

    public function addAnnouncement(): void
    {
        $title = $this->input('title');
        $message = $this->input('message');
        if (trim($title) === '' || trim($message) === '') {
            $this->setFlash('error', 'Title and message are required.');
        } else {
            $this->announcementModel->create($this->currentUser()['id'], $title, $message);
            $this->setFlash('success', 'Announcement published.');
        }
        $this->redirect('admin/announcements');
    }

    // ---------------- Reports ----------------
    public function reports(): void
    {
        $defaultCommission = $this->settingModel->get('default_commission_rate', '10.00');
        $topSellers = $this->orderModel->topSellersPlatform(10);
        $topCategories = $this->orderModel->topCategoriesPlatform(10);
        $agentPerformance = $this->agentModel->performanceReport();
        $revenueMonth = $this->orderModel->revenueThisMonth();
        $this->view('admin/reports', compact('defaultCommission', 'topSellers', 'topCategories', 'agentPerformance', 'revenueMonth'));
    }

    public function updateDefaultCommission(): void
    {
        $rate = (float)$this->input('default_commission_rate');
        if ($rate >= 0 && $rate <= 100) {
            $this->settingModel->set('default_commission_rate', (string)$rate);
            $this->setFlash('success', 'Default commission rate updated.');
        } else {
            $this->setFlash('error', 'Rate must be between 0 and 100.');
        }
        $this->redirect('admin/reports');
    }
}
