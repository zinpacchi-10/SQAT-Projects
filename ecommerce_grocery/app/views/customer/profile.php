<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_customer.php'; ?>
  <div class="main-content">
    <div class="page-title">Profile Settings</div>
    <div class="card">
      <h3>Personal Information</h3>
      <form method="post" action="<?= BASE_URL ?>/public/index.php?url=customer/updateProfile" enctype="multipart/form-data">
        <div class="form-row">
          <div class="form-group"><label>Name</label><input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required></div>
          <div class="form-group"><label>Phone</label><input type="tel" name="phone" value="<?= htmlspecialchars($user['phone']) ?>"></div>
        </div>
        <div class="form-group"><label>Email</label><input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled></div>
        <div class="form-group"><label>Profile Picture</label><input type="file" name="profile_pic" accept="image/*"></div>
        <button class="btn btn-primary">Save Changes</button>
      </form>
    </div>
    <div class="card">
      <h3>Change Password</h3>
      <form method="post" action="<?= BASE_URL ?>/public/index.php?url=customer/changePassword">
        <div class="form-row">
          <div class="form-group"><label>Current Password</label><input type="password" name="current_password" required></div>
          <div class="form-group"><label>New Password</label><input type="password" name="new_password" required></div>
        </div>
        <button class="btn btn-secondary">Update Password</button>
      </form>
    </div>
  </div>
</div>
