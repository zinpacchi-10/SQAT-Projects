<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="page-title">Customers</div>
    <form method="get" action="<?= BASE_URL ?>/public/index.php" class="filters-bar">
      <input type="hidden" name="url" value="admin/customers">
      <div class="form-group"><label>Search</label><input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Name or email"></div>
      <button class="btn btn-primary">Search</button>
    </form>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No customers found.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Name</th><th>Email</th><th>Phone</th><th>Joined</th><th>Status</th><th></th></tr>
          <?php foreach ($items as $u): ?>
            <tr>
              <td><?= htmlspecialchars($u['name']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td><?= htmlspecialchars($u['phone']) ?></td>
              <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
              <td><span class="badge <?= $u['is_active'] ? 'badge-active' : 'badge-cancelled' ?>"><?= $u['is_active'] ? 'Active' : 'Deactivated' ?></span></td>
              <td><a href="<?= BASE_URL ?>/public/index.php?url=admin/toggleCustomer/<?= $u['id'] ?>" class="btn btn-sm <?= $u['is_active'] ? 'btn-danger' : 'btn-primary' ?>"><?= $u['is_active'] ? 'Deactivate' : 'Reactivate' ?></a></td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
