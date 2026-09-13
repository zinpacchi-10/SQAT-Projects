<?php require APP_ROOT . '/app/views/layouts/header.php'; ?>
<div class="page container" style="max-width:560px;">
  <div class="card">
    <h2 class="page-title">Register Your Shop</h2>
    <p class="page-subtitle">Sell groceries on FreshCart. Your account will be reviewed by the platform admin before you can log in.</p>
    <?php if (!empty($errors)): ?>
      <div class="alert alert-error"><ul><?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul></div>
    <?php endif; ?>
    <form method="post" action="<?= BASE_URL ?>/public/index.php?url=auth/registerSeller" enctype="multipart/form-data">
      <div class="section-title" style="margin-top:0;">Your Account</div>
      <div class="form-row">
        <div class="form-group"><label>Full Name</label><input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required></div>
        <div class="form-group"><label>Phone</label><input type="tel" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" required></div>
      </div>
      <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required></div>
      <div class="form-group"><label>Password</label><input type="password" name="password" required><div class="form-hint">Minimum 6 characters.</div></div>

      <div class="section-title">Shop Details</div>
      <div class="form-group"><label>Shop Name</label><input type="text" name="shop_name" value="<?= htmlspecialchars($old['shopName'] ?? '') ?>" required></div>
      <div class="form-group"><label>Shop Description</label><textarea name="shop_description"><?= htmlspecialchars($old['shopDesc'] ?? '') ?></textarea></div>
      <div class="form-group"><label>Shop Address</label><input type="text" name="address" value="<?= htmlspecialchars($old['address'] ?? '') ?>" required></div>
      <div class="form-group"><label>Shop Logo (optional)</label><input type="file" name="shop_logo" accept="image/*"></div>

      <button type="submit" class="btn btn-primary btn-block">Submit for Approval</button>
    </form>
    <p class="text-muted mt-16">Already registered? <a href="<?= BASE_URL ?>/public/index.php?url=auth/login" style="color:var(--green);font-weight:600;">Log in</a></p>
  </div>
</div>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
