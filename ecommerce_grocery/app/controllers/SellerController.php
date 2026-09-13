<?php
require_once APP_ROOT . '/app/core/Controller.php';

class SellerController extends Controller
{
    private SellerModel $sellerModel;
    private ProductModel $productModel;
    private CategoryModel $categoryModel;
    private CouponModel $couponModel;
    private OrderModel $orderModel;
    private ReturnRequestModel $returnModel;
    private ReviewModel $reviewModel;
    private NotificationModel $notificationModel;

    public function __construct()
    {
        $this->sellerModel = new SellerModel();
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->couponModel = new CouponModel();
        $this->orderModel = new OrderModel();
        $this->returnModel = new ReturnRequestModel();
        $this->reviewModel = new ReviewModel();
        $this->notificationModel = new NotificationModel();
        $this->requireRole('seller');
    }

    private function mySeller(): array
    {
        $seller = $this->sellerModel->findByUserId($this->currentUser()['id']);
        if (!$seller) { die('Seller profile not found.'); }
        return $seller;
    }

    public function dashboard(): void
    {
        $seller = $this->mySeller();
        $sellerId = $seller['id'];
        $products = $this->productModel->bySeller($sellerId);
        $lowStock = $this->productModel->lowStock($sellerId);
        $revenue = $this->orderModel->sellerRevenue($sellerId, 'month');
        $pendingOrders = $this->orderModel->sellerOrders($sellerId, 'pending');
        $this->view('seller/dashboard', [
            'seller' => $seller, 'productCount' => count($products), 'lowStock' => $lowStock,
            'revenue' => $revenue, 'pendingOrders' => $pendingOrders,
        ]);
    }

    // ---------------- Products ----------------
    public function products(): void
    {
        $seller = $this->mySeller();
        $products = $this->productModel->bySeller($seller['id']);
        $this->view('seller/products', compact('products'));
    }

    public function addProduct(): void
    {
        $seller = $this->mySeller();
        $categories = $this->categoryModel->all();

        if ($this->isPost()) {
            $name = $this->input('name');
            $desc = $this->input('description');
            $brand = $this->input('brand');
            $unit = $this->input('unit');
            $price = $this->input('price');
            $stock = $this->input('stock_qty');
            $reorder = $this->input('reorder_level', 5);
            $expiry = $this->input('expiry_date') ?: null;
            $categoryId = (int)$this->input('category_id');

            $v = new Validator();
            $v->required($name, 'Product name')->required($unit, 'Unit')
              ->required($price, 'Price')->numeric($price, 'Price')->min($price, 0.01, 'Price')
              ->required($stock, 'Stock quantity')->numeric($stock, 'Stock quantity')->min($stock, 0, 'Stock quantity')
              ->required($categoryId, 'Category');
            if ($expiry) $v->date($expiry, 'Expiry date');

            if ($v->fails()) {
                $this->view('seller/add_product', ['categories' => $categories, 'errors' => $v->errors(), 'old' => $_POST]);
                return;
            }

            $imagePath = null;
            if (!empty($_FILES['primary_image']['name'])) {
                $imagePath = $this->handleUpload('primary_image');
            }

            $productId = $this->productModel->insertProduct(
                $seller['id'], $categoryId, $name, $desc, $brand, $unit,
                (float)$price, (int)$stock, (int)$reorder, $expiry, $imagePath
            );

            if (!empty($_FILES['extra_images']['name'][0])) {
                foreach ($_FILES['extra_images']['name'] as $idx => $fname) {
                    if ($fname === '' || $idx >= 4) continue;
                    $path = $this->handleUploadFromArray('extra_images', $idx);
                    if ($path) $this->productModel->addExtraImage($productId, $path, $idx);
                }
            }

            $this->setFlash('success', 'Product added successfully.');
            $this->redirect('seller/products');
            return;
        }
        $this->view('seller/add_product', ['categories' => $categories, 'errors' => [], 'old' => []]);
    }

    public function editProduct(int $id): void
    {
        $seller = $this->mySeller();
        $product = $this->productModel->find($id);
        if (!$product || $product['seller_id'] != $seller['id']) { $this->redirect('seller/products'); return; }
        $categories = $this->categoryModel->all();
        $images = $this->productModel->images($id);

        if ($this->isPost()) {
            $name = $this->input('name');
            $desc = $this->input('description');
            $brand = $this->input('brand');
            $unit = $this->input('unit');
            $price = $this->input('price');
            $stock = $this->input('stock_qty');
            $reorder = $this->input('reorder_level', 5);
            $expiry = $this->input('expiry_date') ?: null;
            $categoryId = (int)$this->input('category_id');

            $v = new Validator();
            $v->required($name, 'Product name')->required($unit, 'Unit')
              ->required($price, 'Price')->numeric($price, 'Price')->min($price, 0.01, 'Price')
              ->required($stock, 'Stock quantity')->numeric($stock, 'Stock quantity')->min($stock, 0, 'Stock quantity')
              ->required($categoryId, 'Category');

            if ($v->fails()) {
                $this->setFlash('error', implode(' ', $v->errors()));
                $this->redirect('seller/editProduct/' . $id);
                return;
            }

            $this->productModel->updateProduct($id, $categoryId, $name, $desc, $brand, $unit, (float)$price, (int)$stock, (int)$reorder, $expiry);

            if (!empty($_FILES['primary_image']['name'])) {
                $path = $this->handleUpload('primary_image');
                if ($path) $this->productModel->updateImage($id, $path);
            }

            $this->setFlash('success', 'Product updated.');
            $this->redirect('seller/products');
            return;
        }
        $this->view('seller/edit_product', compact('product', 'categories', 'images'));
    }

    public function toggleAvailability(int $id): void
    {
        $seller = $this->mySeller();
        $product = $this->productModel->find($id);
        if ($product && $product['seller_id'] == $seller['id']) {
            $this->productModel->toggleAvailability($id);
            $this->setFlash('success', 'Availability updated.');
        }
        $this->redirect('seller/products');
    }

    public function deleteProduct(int $id): void
    {
        $seller = $this->mySeller();
        $product = $this->productModel->find($id);
        if ($product && $product['seller_id'] == $seller['id']) {
            $ok = $this->productModel->delete($id);
            $this->setFlash($ok ? 'success' : 'error', $ok ? 'Product deleted.' : 'Cannot delete: product is linked to pending orders.');
        }
        $this->redirect('seller/products');
    }

    /** AJAX: update stock quantity from product list inline form */
    public function updateStock(): void
    {
        $seller = $this->mySeller();
        $productId = (int)$this->input('product_id');
        $newQty = (int)$this->input('stock_qty');
        $product = $this->productModel->find($productId);

        if (!$product || $product['seller_id'] != $seller['id']) {
            $this->json(['success' => false, 'message' => 'Product not found.'], 404);
        }
        if ($newQty < 0) {
            $this->json(['success' => false, 'message' => 'Stock quantity cannot be negative.'], 400);
        }
        $this->productModel->updateStock($productId, $newQty);
        $this->json(['success' => true, 'message' => 'Stock updated.', 'new_qty' => $newQty, 'low_stock' => $newQty <= $product['reorder_level']]);
    }

    private function handleUpload(string $field): ?string
    {
        if (empty($_FILES[$field]['name'])) return null;
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) return null;
        $dir = UPLOAD_PATH . '/products';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $filename = uniqid('prod_', true) . '.' . $ext;
        if (move_uploaded_file($_FILES[$field]['tmp_name'], $dir . '/' . $filename)) {
            return 'products/' . $filename;
        }
        return null;
    }

    private function handleUploadFromArray(string $field, int $idx): ?string
    {
        if (empty($_FILES[$field]['name'][$idx])) return null;
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($_FILES[$field]['name'][$idx], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) return null;
        $dir = UPLOAD_PATH . '/products';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $filename = uniqid('prod_', true) . '.' . $ext;
        if (move_uploaded_file($_FILES[$field]['tmp_name'][$idx], $dir . '/' . $filename)) {
            return 'products/' . $filename;
        }
        return null;
    }

    // ---------------- Coupons ----------------
    public function coupons(): void
    {
        $seller = $this->mySeller();
        $items = $this->couponModel->bySeller($seller['id']);
        $this->view('seller/coupons', compact('items'));
    }

    public function addCoupon(): void
    {
        $seller = $this->mySeller();
        $code = strtoupper(trim($this->input('code')));
        $pct = (float)$this->input('discount_pct');
        $maxUses = (int)$this->input('max_uses', 100);
        $minOrder = (float)$this->input('min_order_amount', 0);
        $validUntil = $this->input('valid_until');

        $v = new Validator();
        $v->required($code, 'Coupon code')->required($pct, 'Discount %')->min($pct, 1, 'Discount %')
          ->required($validUntil, 'Valid until date')->date($validUntil, 'Valid until date');

        if ($v->fails() || $this->couponModel->findByCode($code)) {
            $this->setFlash('error', $v->fails() ? implode(' ', $v->errors()) : 'Coupon code already exists.');
        } else {
            $this->couponModel->create($seller['id'], false, $code, $pct, $maxUses, $minOrder, $validUntil);
            $this->setFlash('success', 'Coupon created.');
        }
        $this->redirect('seller/coupons');
    }

    public function toggleCoupon(int $id): void
    {
        $this->couponModel->toggleActive($id);
        $this->redirect('seller/coupons');
    }

    // ---------------- Orders ----------------
    public function orders(): void
    {
        $seller = $this->mySeller();
        $statusFilter = $this->input('status', '');
        $items = $this->orderModel->sellerOrders($seller['id'], $statusFilter);
        $this->view('seller/orders', ['items' => $items, 'statusFilter' => $statusFilter]);
    }

    public function orderItemDetail(int $itemId): void
    {
        $seller = $this->mySeller();
        $item = $this->orderModel->itemById($itemId);
        if (!$item || $item['seller_id'] != $seller['id']) { $this->redirect('seller/orders'); return; }
        $order = $this->orderModel->find($item['order_id']);
        $this->view('seller/order_item_detail', compact('item', 'order'));
    }

    public function confirmItem(int $itemId): void
    {
        $seller = $this->mySeller();
        $item = $this->orderModel->itemById($itemId);
        if ($item && $item['seller_id'] == $seller['id'] && $item['item_status'] === 'pending') {
            $this->orderModel->updateItemStatus($itemId, 'processing');
            $order = $this->orderModel->find($item['order_id']);
            $this->notificationModel->create($order['customer_id'], 'An item in your order #' . $item['order_id'] . ' is now being processed.');
            $this->setFlash('success', 'Item confirmed and marked as processing.');
        }
        $this->redirect('seller/orders');
    }

    public function shipItem(): void
    {
        $seller = $this->mySeller();
        $itemId = (int)$this->input('item_id');
        $note = $this->input('tracking_note', '');
        $item = $this->orderModel->itemById($itemId);
        if ($item && $item['seller_id'] == $seller['id']) {
            $this->orderModel->updateItemStatus($itemId, 'shipped', $note);
            $order = $this->orderModel->find($item['order_id']);
            $this->notificationModel->create($order['customer_id'], 'Your item has been shipped. Tracking note: ' . $note);
            $this->setFlash('success', 'Item marked as shipped.');
        }
        $this->redirect('seller/orders');
    }

    // ---------------- Returns ----------------
    public function returns(): void
    {
        $seller = $this->mySeller();
        $items = $this->returnModel->bySeller($seller['id']);
        $this->view('seller/returns', compact('items'));
    }

    public function handleReturn(): void
    {
        $seller = $this->mySeller();
        $id = (int)$this->input('return_id');
        $decision = $this->input('decision'); // approved/rejected
        $note = $this->input('note', '');
        $ret = $this->returnModel->find($id);
        if ($ret) {
            $this->returnModel->updateStatus($id, $decision, $note);
            if ($decision === 'approved') {
                $item = $this->orderModel->itemById($ret['order_item_id']);
                if ($item) {
                    $this->orderModel->updateItemStatus($ret['order_item_id'], 'returned');
                    $this->productModel->incrementStock($item['product_id'], $item['quantity']);
                }
            }
            $this->notificationModel->create($ret['customer_id'], 'Your return request has been ' . $decision . '.');
            $this->setFlash('success', 'Return request updated.');
        }
        $this->redirect('seller/returns');
    }

    // ---------------- Reviews ----------------
    public function reviews(): void
    {
        $seller = $this->mySeller();
        $items = $this->reviewModel->bySeller($seller['id']);
        $this->view('seller/reviews', compact('items'));
    }

    public function replyReview(): void
    {
        $id = (int)$this->input('review_id');
        $reply = $this->input('reply');
        if (trim($reply) !== '') {
            $this->reviewModel->reply($id, $reply);
            $this->setFlash('success', 'Reply posted.');
        }
        $this->redirect('seller/reviews');
    }

    // ---------------- Analytics ----------------
    public function analytics(): void
    {
        $seller = $this->mySeller();
        $period = $this->input('period', 'month');
        $revenue = $this->orderModel->sellerRevenue($seller['id'], $period);
        $topProducts = $this->orderModel->topSellingProducts($seller['id']);
        $volume = $this->orderModel->orderVolumeByDay($seller['id'], 14);
        $commission = (float)$seller['commission_rate'];
        $netPayout = $revenue['revenue'] * (1 - $commission / 100);
        $this->view('seller/analytics', compact('revenue', 'topProducts', 'volume', 'period', 'commission', 'netPayout'));
    }

    // ---------------- Profile ----------------
    public function profile(): void
    {
        $seller = $this->mySeller();
        $this->view('seller/profile', compact('seller'));
    }

    public function updateProfile(): void
    {
        $seller = $this->mySeller();
        $shopName = $this->input('shop_name');
        $desc = $this->input('shop_description');
        $address = $this->input('address');

        if (trim($shopName) === '' || trim($address) === '') {
            $this->setFlash('error', 'Shop name and address are required.');
        } else {
            $this->sellerModel->updateProfile($seller['id'], $shopName, $desc, $address);
            if (!empty($_FILES['shop_logo']['name'])) {
                $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                $ext = strtolower(pathinfo($_FILES['shop_logo']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, $allowed, true)) {
                    $dir = UPLOAD_PATH . '/shops';
                    if (!is_dir($dir)) mkdir($dir, 0777, true);
                    $filename = uniqid('shop_', true) . '.' . $ext;
                    if (move_uploaded_file($_FILES['shop_logo']['tmp_name'], $dir . '/' . $filename)) {
                        $this->sellerModel->updateLogo($seller['id'], 'shops/' . $filename);
                    }
                }
            }
            $this->setFlash('success', 'Shop profile updated.');
        }
        $this->redirect('seller/profile');
    }
}
