<div class="page container">
  <div class="page-title">Shop Groceries &amp; Household Products</div>
  <div class="page-subtitle">Browse products from all sellers on FreshCart.</div>

  <form method="get" action="<?= BASE_URL ?>/public/index.php" class="filters-bar">
    <input type="hidden" name="url" value="customer/products">
    <div class="form-group">
      <label>Search</label>
      <input type="text" name="keyword" placeholder="Search products..." value="<?= htmlspecialchars($filters['keyword']) ?>">
    </div>
    <div class="form-group">
      <label>Category</label>
      <select name="category_id">
        <option value="">All Categories</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $filters['category_id'] == $c['id'] ? 'selected' : '' ?>><?= $c['parent_id'] ? '— ' : '' ?><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Brand</label>
      <select name="brand">
        <option value="">All Brands</option>
        <?php foreach ($brands as $b): ?>
          <option value="<?= htmlspecialchars($b) ?>" <?= $filters['brand'] === $b ? 'selected' : '' ?>><?= htmlspecialchars($b) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Min Price</label>
      <input type="number" name="min_price" step="0.01" value="<?= htmlspecialchars($filters['min_price']) ?>">
    </div>
    <div class="form-group">
      <label>Max Price</label>
      <input type="number" name="max_price" step="0.01" value="<?= htmlspecialchars($filters['max_price']) ?>">
    </div>
    <div class="form-group">
      <label>Min Rating</label>
      <select name="min_rating">
        <option value="">Any</option>
        <?php foreach ([4,3,2,1] as $r): ?><option value="<?= $r ?>" <?= $filters['min_rating'] == $r ? 'selected' : '' ?>><?= $r ?>+ stars</option><?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Sort By</label>
      <select name="sort">
        <option value="newest" <?= $filters['sort'] === 'newest' ? 'selected' : '' ?>>Newest</option>
        <option value="price_asc" <?= $filters['sort'] === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
        <option value="price_desc" <?= $filters['sort'] === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
        <option value="rating" <?= $filters['sort'] === 'rating' ? 'selected' : '' ?>>Top Rated</option>
        <option value="popularity" <?= $filters['sort'] === 'popularity' ? 'selected' : '' ?>>Most Popular</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Apply Filters</button>
  </form>

  <?php if (empty($products)): ?>
    <div class="empty-state">No products match your filters.</div>
  <?php else: ?>
    <div class="product-grid">
      <?php foreach ($products as $p): require APP_ROOT . '/app/views/partials/product_card.php'; endforeach; ?>
    </div>
  <?php endif; ?>
</div>
