<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="page-title">Delivery Managers</div>
    <div class="card">
      <h3>Create Delivery Manager Account</h3>
      <form method="post" action="<?= BASE_URL ?>/public/index.php?url=admin/addDeliveryManager" class="form-row" style="align-items:flex-end;">
        <div class="form-group"><label>Name</label><input type="text" name="name" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
        <div class="form-group"><label>Phone</label><input type="tel" name="phone" required></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
        <button class="btn btn-primary">Create Account</button>
      </form>
    </div>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No delivery manager accounts yet.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th></th></tr>
          <?php foreach ($items as $u): ?>
            <tr>
              <td><?= htmlspecialchars($u['name']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td><?= htmlspecialchars($u['phone']) ?></td>
              <td><span class="badge <?= $u['is_active'] ? 'badge-active' : 'badge-cancelled' ?>"><?= $u['is_active'] ? 'Active' : 'Deactivated' ?></span></td>
              <td><a href="<?= BASE_URL ?>/public/index.php?url=admin/toggleDeliveryManager/<?= $u['id'] ?>" class="btn btn-sm <?= $u['is_active'] ? 'btn-danger' : 'btn-primary' ?>"><?= $u['is_active'] ? 'Deactivate' : 'Reactivate' ?></a></td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
