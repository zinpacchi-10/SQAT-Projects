<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="page-title">Platform Announcements</div>
    <div class="card">
      <h3>Publish New Announcement</h3>
      <form method="post" action="<?= BASE_URL ?>/public/index.php?url=admin/addAnnouncement">
        <div class="form-group"><label>Title</label><input type="text" name="title" required></div>
        <div class="form-group"><label>Message</label><textarea name="message" required></textarea></div>
        <button class="btn btn-primary">Publish</button>
      </form>
    </div>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No announcements published yet.</div>
      <?php else: ?>
        <?php foreach ($items as $a): ?>
          <div class="review-item">
            <strong><?= htmlspecialchars($a['title']) ?></strong>
            <div class="text-muted" style="font-size:12px;"><?= date('d M Y, h:i A', strtotime($a['created_at'])) ?></div>
            <p><?= nl2br(htmlspecialchars($a['message'])) ?></p>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
