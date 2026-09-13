<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_delivery.php'; ?>
  <div class="main-content">
    <div class="page-title">Profile Settings</div>
    <div class="card">
      <form method="post" action="<?= BASE_URL ?>/public/index.php?url=delivery/updateProfile">
        <div class="form-row">
          <div class="form-group"><label>Name</label><input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required></div>
          <div class="form-group"><label>Phone</label><input type="tel" name="phone" value="<?= htmlspecialchars($user['phone']) ?>"></div>
        </div>
        <div class="form-group"><label>Email</label><input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled></div>
        <button class="btn btn-primary">Save Changes</button>
      </form>
    </div>
  </div>
</div>
