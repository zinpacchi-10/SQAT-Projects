<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_seller.php'; ?>
  <div class="main-content">
    <div class="page-title">Product Reviews</div>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No reviews yet.</div>
      <?php else: ?>
        <?php foreach ($items as $r): ?>
          <div class="review-item">
            <div class="flex-between">
              <strong><?= htmlspecialchars($r['product_name']) ?></strong>
              <span class="rating">★ <?= $r['rating'] ?></span>
            </div>
            <div class="text-muted" style="font-size:12px;">by <?= htmlspecialchars($r['customer_name']) ?> on <?= date('d M Y', strtotime($r['created_at'])) ?></div>
            <p><?= nl2br(htmlspecialchars($r['review_text'])) ?></p>
            <?php if ($r['seller_reply']): ?>
              <div class="seller-reply"><strong>Your reply:</strong> <?= nl2br(htmlspecialchars($r['seller_reply'])) ?></div>
            <?php else: ?>
              <form method="post" action="<?= BASE_URL ?>/public/index.php?url=seller/replyReview" class="flex gap-8 mt-8">
                <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                <input type="text" name="reply" placeholder="Write a reply..." style="flex:1;">
                <button class="btn btn-sm btn-secondary">Reply</button>
              </form>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
