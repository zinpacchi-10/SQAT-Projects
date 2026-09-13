<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_customer.php'; ?>
  <div class="main-content">
    <div class="page-title">Notifications</div>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No notifications yet.</div>
      <?php else: ?>
        <?php foreach ($items as $n): ?>
          <div class="review-item">
            <div><?= htmlspecialchars($n['message']) ?></div>
            <div class="text-muted" style="font-size:12px;"><?= date('d M Y, h:i A', strtotime($n['created_at'])) ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
