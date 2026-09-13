<?php
require_once APP_ROOT . '/app/core/Controller.php';

class CustomerController extends Controller
{
    private ProductModel $productModel;
    private CategoryModel $categoryModel;
    private OrderModel $orderModel;
    private CouponModel $couponModel;
    private DeliveryZoneModel $zoneModel;
    private AddressModel $addressModel;
    private ReviewModel $reviewModel;
    private WishlistModel $wishlistModel;
    private ReturnRequestModel $returnModel;
    private DisputeModel $disputeModel;
    private NotificationModel $notificationModel;
    private UserModel $userModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->orderModel = new OrderModel();
        $this->couponModel = new CouponModel();
        $this->zoneModel = new DeliveryZoneModel();
        $this->addressModel = new AddressModel();
        $this->reviewModel = new ReviewModel();
        $this->wishlistModel = new WishlistModel();
        $this->returnModel = new ReturnRequestModel();
        $this->disputeModel = new DisputeModel();
        $this->notificationModel = new NotificationModel();
        $this->userModel = new UserModel();
        if (!empty($_SESSION['user'])) {
            $this->requireRole('customer');
        }
    }

    public function dashboard(): void
    {
        $this->requireRole('customer');
        $uid = $this->currentUser()['id'];
        $orders = $this->orderModel->byCustomer($uid);
        $wishlistCount = count($this->wishlistModel->byCustomer($uid));
        $notifCount = $this->notificationModel->unreadCount($uid);
        $this->view('customer/dashboard', ['orders' => array_slice($orders, 0, 5), 'orderCount' => count($orders), 'wishlistCount' => $wishlistCount, 'notifCount' => $notifCount]);
    }

    // ---------------- Product browsing ----------------
    public function products(): void
    {
        $filters = [
            'keyword' => $this->input('keyword', ''),
            'category_id' => $this->input('category_id', ''),
            'brand' => $this->input('brand', ''),
            'min_price' => $this->input('min_price', ''),
            'max_price' => $this->input('max_price', ''),
            'min_rating' => $this->input('min_rating', ''),
            'sort' => $this->input('sort', 'newest'),
        ];
        $products = $this->productModel->browse($filters);
        $categories = $this->categoryModel->all();
        $brands = $this->productModel->distinctBrands();
        $this->view('customer/products', compact('products', 'categories', 'brands', 'filters'), 'guest_or_customer');
    }

    public function productDetail(int $id): void
    {
        $product = $this->productModel->find($id);
        if (!$product) { http_response_code(404); echo "Product not found."; return; }
        $images = $this->productModel->images($id);
        $reviews = $this->reviewModel->byProduct($id);
        $avg = 0;
        if (!empty($reviews)) { $avg = round(array_sum(array_column($reviews, 'rating')) / count($reviews), 1); }
        $inWishlist = false;
        if ($this->currentUser()) {
            $inWishlist = $this->wishlistModel->exists($this->currentUser()['id'], $id);
        }
        $this->view('customer/product_detail', compact('product', 'images', 'reviews', 'avg', 'inWishlist'));
    }

    // ---------------- Cart (session based) ----------------
    private function getCartData(): array { return $_SESSION['cart'] ?? []; }
    private function saveCart(array $cart): void { $_SESSION['cart'] = $cart; }

    public function cart(): void
    {
        $cartItems = [];
        $subtotal = 0;
        foreach ($this->getCartData() as $pid => $qty) {
            $p = $this->productModel->find($pid);
            if (!$p) continue;
            $lineTotal = $p['price'] * $qty;
            $subtotal += $lineTotal;
            $cartItems[] = ['product' => $p, 'qty' => $qty, 'line_total' => $lineTotal];
        }
        $this->view('customer/cart', compact('cartItems', 'subtotal'));
    }

    public function addToCart(): void
    {
        $productId = (int)$this->input('product_id');
        $qty = max(1, (int)$this->input('qty', 1));
        $product = $this->productModel->find($productId);

        if (!$this->currentUser()) {
            $this->json(['success' => false, 'message' => 'Please log in as a customer to add items to cart.'], 401);
        }
        if (!$product || !$product['is_available']) {
            $this->json(['success' => false, 'message' => 'Product is not available.'], 400);
        }
        $cart = $this->getCartData();
        $newQty = ($cart[$productId] ?? 0) + $qty;
        if ($newQty > $product['stock_qty']) {
            $this->json(['success' => false, 'message' => 'Only ' . $product['stock_qty'] . ' units available in stock.'], 400);
        }
        $cart[$productId] = $newQty;
        $this->saveCart($cart);
        $this->json(['success' => true, 'message' => 'Added to cart.', 'cart_count' => count($cart)]);
    }

    public function updateCartQty(): void
    {
        $productId = (int)$this->input('product_id');
        $qty = (int)$this->input('qty');
        $cart = $this->getCartData();
        $product = $this->productModel->find($productId);

        if ($qty <= 0) {
            unset($cart[$productId]);
            $this->saveCart($cart);
            $this->redirect('customer/cart');
            return;
        }
        if (!$product || $qty > $product['stock_qty']) {
            $this->setFlash('error', 'Requested quantity exceeds available stock (' . ($product['stock_qty'] ?? 0) . ' left).');
            $this->redirect('customer/cart');
            return;
        }
        $cart[$productId] = $qty;
        $this->saveCart($cart);
        $this->redirect('customer/cart');
    }

    public function removeFromCart(int $productId): void
    {
        $cart = $this->getCartData();
        unset($cart[$productId]);
        $this->saveCart($cart);
        $this->redirect('customer/cart');
    }

    // ---------------- Coupon (AJAX) ----------------
    public function applyCoupon(): void
    {
        $code = strtoupper(trim($this->input('code', '')));
        $subtotal = (float)$this->input('subtotal', 0);
        if ($code === '') {
            $this->json(['valid' => false, 'message' => 'Please enter a coupon code.']);
        }
        $result = $this->couponModel->validate($code, $subtotal);
        if ($result['valid']) {
            $_SESSION['applied_coupon'] = $result['coupon']['id'];
        } else {
            unset($_SESSION['applied_coupon']);
        }
        $this->json($result);
    }

    // ---------------- Checkout ----------------
    public function checkout(): void
    {
        $this->requireRole('customer');
        $uid = $this->currentUser()['id'];
        $cartItems = [];
        $subtotal = 0;
        foreach ($this->getCartData() as $pid => $qty) {
            $p = $this->productModel->find($pid);
            if (!$p) continue;
            $lineTotal = $p['price'] * $qty;
            $subtotal += $lineTotal;
            $cartItems[] = ['product' => $p, 'qty' => $qty, 'line_total' => $lineTotal];
        }
        if (empty($cartItems)) {
            $this->setFlash('error', 'Your cart is empty.');
            $this->redirect('customer/products');
            return;
        }
        $addresses = $this->addressModel->byCustomer($uid);
        $zones = $this->zoneModel->all();
        $this->view('customer/checkout', compact('cartItems', 'subtotal', 'addresses', 'zones'));
    }

    public function placeOrder(): void
    {
        $this->requireRole('customer');
        $uid = $this->currentUser()['id'];

        $addressText = $this->input('shipping_address');
        $zoneId = (int)$this->input('delivery_zone_id');
        $paymentMethod = $this->input('payment_method', 'COD');

        $v = new Validator();
        $v->required($addressText, 'Shipping address')->required($zoneId, 'Delivery zone')
          ->inArray($paymentMethod, ['COD', 'Card'], 'Payment method');

        $cart = $this->getCartData();
        if (empty($cart)) { $v = new Validator(); $v->required('', 'Cart'); }

        if ($v->fails() || empty($cart)) {
            $this->setFlash('error', empty($cart) ? 'Your cart is empty.' : implode(' ', $v->errors()));
            $this->redirect('customer/checkout');
            return;
        }

        $zone = $this->zoneModel->find($zoneId);
        if (!$zone) {
            $this->setFlash('error', 'Invalid delivery zone selected.');
            $this->redirect('customer/checkout');
            return;
        }

        $items = [];
        $subtotal = 0;
        foreach ($cart as $pid => $qty) {
            $p = $this->productModel->find($pid);
            if (!$p) continue;
            if ($qty > $p['stock_qty']) {
                $this->setFlash('error', 'Insufficient stock for "' . $p['name'] . '". Only ' . $p['stock_qty'] . ' left.');
                $this->redirect('customer/cart');
                return;
            }
            $subtotal += $p['price'] * $qty;
            $items[] = ['product_id' => $pid, 'quantity' => $qty, 'unit_price' => $p['price'], 'seller_id' => $p['seller_id']];
        }

        $discount = 0;
        $couponId = $_SESSION['applied_coupon'] ?? null;
        if ($couponId) {
            $coupon = $this->couponModel->findById($couponId);
            if ($coupon) {
                $check = $this->couponModel->validate($coupon['code'], $subtotal);
                if ($check['valid']) { $discount = $check['discount']; }
                else { $couponId = null; }
            }
        }

        $deliveryFee = (float)$zone['delivery_fee'];
        $total = max(0, $subtotal + $deliveryFee - $discount);

        $result = $this->orderModel->placeOrder($uid, $addressText, $zoneId, $paymentMethod, $subtotal, $deliveryFee, $discount, $total, $couponId, $items);

        if (!$result['success']) {
            $this->setFlash('error', $result['message']);
            $this->redirect('customer/cart');
            return;
        }

        if ($couponId) { $this->couponModel->incrementUse($couponId); }
        $this->saveCart([]);
        unset($_SESSION['applied_coupon']);
        $this->notificationModel->create($uid, 'Your order #' . $result['order_id'] . ' has been placed successfully.');

        $this->redirect('customer/orderConfirmation/' . $result['order_id']);
    }

    public function orderConfirmation(int $id): void
    {
        $this->requireRole('customer');
        $order = $this->orderModel->find($id);
        if (!$order || $order['customer_id'] != $this->currentUser()['id']) { http_response_code(404); echo "Order not found."; return; }
        $items = $this->orderModel->items($id);
        $this->view('customer/order_confirmation', compact('order', 'items'));
    }

    // ---------------- Orders ----------------
    public function orders(): void
    {
        $this->requireRole('customer');
        $orders = $this->orderModel->byCustomer($this->currentUser()['id']);
        $this->view('customer/orders', compact('orders'));
    }

    public function orderDetail(int $id): void
    {
        $this->requireRole('customer');
        $order = $this->orderModel->find($id);
        if (!$order || $order['customer_id'] != $this->currentUser()['id']) { http_response_code(404); echo "Order not found."; return; }
        $items = $this->orderModel->items($id);
        $canCancel = $this->orderModel->canCancel($order);
        $this->view('customer/order_detail', compact('order', 'items', 'canCancel'));
    }

    /** AJAX polling endpoint for live order status badge */
    public function orderStatus(int $id): void
    {
        $order = $this->orderModel->find($id);
        if (!$order || !$this->currentUser() || $order['customer_id'] != $this->currentUser()['id']) {
            $this->json(['success' => false, 'message' => 'Not found'], 404);
        }
        $this->json(['success' => true, 'status' => $order['status']]);
    }

    public function cancelOrder(int $id): void
    {
        $this->requireRole('customer');
        $order = $this->orderModel->find($id);
        if ($order && $order['customer_id'] == $this->currentUser()['id'] && $this->orderModel->canCancel($order)) {
            $this->orderModel->cancel($id);
            $this->notificationModel->create($order['customer_id'], 'Order #' . $id . ' has been cancelled.');
            $this->setFlash('success', 'Order cancelled successfully.');
        } else {
            $this->setFlash('error', 'This order can no longer be cancelled.');
        }
        $this->redirect('customer/orderDetail/' . $id);
    }

    // ---------------- Returns ----------------
    public function returns(): void
    {
        $this->requireRole('customer');
        $returns = $this->returnModel->byCustomer($this->currentUser()['id']);
        $this->view('customer/returns', compact('returns'));
    }

    public function requestReturn(): void
    {
        $this->requireRole('customer');
        $orderItemId = (int)$this->input('order_item_id');
        $orderId = (int)$this->input('order_id');
        $reason = $this->input('reason');

        $item = $this->orderModel->itemById($orderItemId);
        if (!$item || $item['item_status'] !== 'delivered') {
            $this->setFlash('error', 'Only delivered items are eligible for return.');
            $this->redirect('customer/orderDetail/' . $orderId);
            return;
        }
        if ($this->returnModel->alreadyRequested($orderItemId)) {
            $this->setFlash('error', 'A return request already exists for this item.');
            $this->redirect('customer/orderDetail/' . $orderId);
            return;
        }
        if (trim($reason) === '') {
            $this->setFlash('error', 'Please provide a reason for the return.');
            $this->redirect('customer/orderDetail/' . $orderId);
            return;
        }
        $this->returnModel->create($orderId, $orderItemId, $this->currentUser()['id'], $reason);
        $this->orderModel->updateStatus($orderId, 'return_requested');
        $this->setFlash('success', 'Return request submitted.');
        $this->redirect('customer/orderDetail/' . $orderId);
    }

    // ---------------- Reviews ----------------
    public function addReview(): void
    {
        $this->requireRole('customer');
        $productId = (int)$this->input('product_id');
        $orderId = (int)$this->input('order_id');
        $rating = (int)$this->input('rating');
        $text = $this->input('review_text', '');
        $uid = $this->currentUser()['id'];

        if ($rating < 1 || $rating > 5) {
            $this->setFlash('error', 'Rating must be between 1 and 5.');
            $this->redirect('customer/orderDetail/' . $orderId);
            return;
        }
        if ($this->reviewModel->alreadyReviewed($uid, $orderId, $productId)) {
            $this->setFlash('error', 'You already reviewed this product for this order.');
            $this->redirect('customer/orderDetail/' . $orderId);
            return;
        }
        $this->reviewModel->create($productId, $orderId, $uid, $rating, $text);
        $this->setFlash('success', 'Review submitted. Thank you!');
        $this->redirect('customer/orderDetail/' . $orderId);
    }

    public function editReview(int $id): void
    {
        $this->requireRole('customer');
        $review = $this->reviewModel->find($id);
        if (!$review || $review['customer_id'] != $this->currentUser()['id']) { $this->redirect('customer/orders'); return; }
        if ($this->isPost()) {
            $rating = (int)$this->input('rating');
            $text = $this->input('review_text', '');
            $this->reviewModel->update($id, max(1, min(5, $rating)), $text);
            $this->setFlash('success', 'Review updated.');
            $this->redirect('customer/orders');
            return;
        }
        $this->view('customer/edit_review', compact('review'));
    }

    public function deleteReview(int $id): void
    {
        $this->requireRole('customer');
        $review = $this->reviewModel->find($id);
        if ($review && $review['customer_id'] == $this->currentUser()['id']) {
            $this->reviewModel->delete($id);
            $this->setFlash('success', 'Review deleted.');
        }
        $this->redirect('customer/orders');
    }

    // ---------------- Wishlist ----------------
    public function wishlist(): void
    {
        $this->requireRole('customer');
        $items = $this->wishlistModel->byCustomer($this->currentUser()['id']);
        $this->view('customer/wishlist', compact('items'));
    }

    public function toggleWishlist(): void
    {
        if (!$this->currentUser()) { $this->json(['success' => false, 'message' => 'Please log in.'], 401); }
        $productId = (int)$this->input('product_id');
        $uid = $this->currentUser()['id'];
        if ($this->wishlistModel->exists($uid, $productId)) {
            $this->wishlistModel->remove($uid, $productId);
            $this->json(['success' => true, 'action' => 'removed']);
        } else {
            $this->wishlistModel->add($uid, $productId);
            $this->json(['success' => true, 'action' => 'added']);
        }
    }

    // ---------------- Addresses ----------------
    public function addresses(): void
    {
        $this->requireRole('customer');
        $items = $this->addressModel->byCustomer($this->currentUser()['id']);
        $this->view('customer/addresses', compact('items'));
    }

    public function addAddress(): void
    {
        $this->requireRole('customer');
        $label = $this->input('label', 'Home');
        $full = $this->input('full_address');
        $city = $this->input('city');
        $default = (bool)$this->input('is_default');
        if (trim($full) === '') {
            $this->setFlash('error', 'Address is required.');
        } else {
            $this->addressModel->create($this->currentUser()['id'], $label, $full, $city, $default);
            $this->setFlash('success', 'Address added.');
        }
        $this->redirect('customer/addresses');
    }

    public function editAddress(int $id): void
    {
        $this->requireRole('customer');
        if ($this->isPost()) {
            $this->addressModel->update($id, $this->input('label'), $this->input('full_address'), $this->input('city'));
            $this->setFlash('success', 'Address updated.');
        }
        $this->redirect('customer/addresses');
    }

    public function deleteAddress(int $id): void
    {
        $this->requireRole('customer');
        $this->addressModel->delete($id);
        $this->setFlash('success', 'Address removed.');
        $this->redirect('customer/addresses');
    }

    public function setDefaultAddress(int $id): void
    {
        $this->requireRole('customer');
        $this->addressModel->setDefault($id, $this->currentUser()['id']);
        $this->redirect('customer/addresses');
    }

    // ---------------- Disputes ----------------
    public function disputes(): void
    {
        $this->requireRole('customer');
        $items = $this->disputeModel->byCustomer($this->currentUser()['id']);
        $this->view('customer/disputes', compact('items'));
    }

    public function fileDispute(): void
    {
        $this->requireRole('customer');
        $orderId = (int)$this->input('order_id', 0) ?: null;
        $desc = $this->input('description');
        if (trim($desc) === '') {
            $this->setFlash('error', 'Please describe your issue.');
        } else {
            $sellerId = null;
            if ($orderId) {
                $items = $this->orderModel->items($orderId);
                if (!empty($items)) $sellerId = $items[0]['seller_id'];
            }
            $this->disputeModel->create($this->currentUser()['id'], $sellerId, $orderId, $desc);
            $this->setFlash('success', 'Dispute submitted to platform admin.');
        }
        $this->redirect('customer/disputes');
    }

    // ---------------- Notifications ----------------
    public function notifications(): void
    {
        $this->requireRole('customer');
        $items = $this->notificationModel->byUser($this->currentUser()['id']);
        $this->notificationModel->markAllRead($this->currentUser()['id']);
        $this->view('customer/notifications', compact('items'));
    }

    // ---------------- Profile ----------------
    public function profile(): void
    {
        $this->requireRole('customer');
        $user = $this->userModel->findById($this->currentUser()['id']);
        $this->view('customer/profile', compact('user'));
    }

    public function updateProfile(): void
    {
        $this->requireRole('customer');
        $uid = $this->currentUser()['id'];
        $name = $this->input('name');
        $phone = $this->input('phone');
        if (trim($name) === '') {
            $this->setFlash('error', 'Name is required.');
            $this->redirect('customer/profile');
            return;
        }
        $this->userModel->updateProfile($uid, $name, $phone);
        $_SESSION['user']['name'] = $name;

        if (!empty($_FILES['profile_pic']['name'])) {
            $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
                $dir = UPLOAD_PATH . '/profiles';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                $filename = uniqid('pfp_', true) . '.' . $ext;
                if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $dir . '/' . $filename)) {
                    $this->userModel->updateProfilePic($uid, 'profiles/' . $filename);
                    $_SESSION['user']['profile_pic'] = 'profiles/' . $filename;
                }
            }
        }
        $this->setFlash('success', 'Profile updated.');
        $this->redirect('customer/profile');
    }

    public function changePassword(): void
    {
        $this->requireRole('customer');
        $uid = $this->currentUser()['id'];
        $current = $this->input('current_password');
        $new = $this->input('new_password');
        $user = $this->userModel->findById($uid);

        if (!password_verify($current, $user['password_hash'])) {
            $this->setFlash('error', 'Current password is incorrect.');
        } elseif (strlen($new) < 6) {
            $this->setFlash('error', 'New password must be at least 6 characters.');
        } else {
            $this->userModel->updatePassword($uid, password_hash($new, PASSWORD_DEFAULT));
            $this->setFlash('success', 'Password changed successfully.');
        }
        $this->redirect('customer/profile');
    }
}
