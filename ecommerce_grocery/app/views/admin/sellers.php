<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="page-title">Sellers</div>
    <div class="pill-nav">
      <a href="<?= BASE_URL ?>/public/index.php?url=admin/sellers" class="<?= $status === '' ? 'active' : '' ?>">All</a>
      <?php foreach (['pending', 'approved', 'rejected', 'suspended'] as $s): ?>
        <a href="<?= BASE_URL ?>/public/index.php?url=admin/sellers&status=<?= $s ?>" class="<?= $status === $s ? 'active' : '' ?>"><?= ucfirst($s) ?></a>
      <?php endforeach; ?>
    </div>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No sellers found.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Shop</th><th>Owner</th><th>Email</th><th>Status</th><th>Commission</th><th>Actions</th></tr>
          <?php foreach ($items as $s): ?>
            <tr>
              <td><a href="<?= BASE_URL ?>/public/index.php?url=admin/sellerDetail/<?= $s['id'] ?>"><?= htmlspecialchars($s['shop_name']) ?></a></td>
              <td><?= htmlspecialchars($s['owner_name']) ?></td>
              <td><?= htmlspecialchars($s['email']) ?></td>
              <td><span class="badge badge-<?= $s['is_approved'] ?>"><?= $s['is_approved'] ?></span></td>
              <td><?= $s['commission_rate'] ?>%</td>
              <td class="flex gap-8">
                <?php if ($s['is_approved'] === 'pending'): ?>
                  <a href="<?= BASE_URL ?>/public/index.php?url=admin/approveSeller/<?= $s['id'] ?>" class="btn btn-sm btn-primary">Approve</a>
                  <form method="post" action="<?= BASE_URL ?>/public/index.php?url=admin/rejectSeller/<?= $s['id'] ?>" style="display:inline;">
                    <input type="text" name="reason" placeholder="Reason" style="width:110px;">
                    <button class="btn btn-sm btn-danger">Reject</button>
                  </form>
                <?php elseif ($s['is_approved'] === 'approved'): ?>
                  <a href="<?= BASE_URL ?>/public/index.php?url=admin/suspendSeller/<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Suspend this seller?')">Suspend</a>
                <?php elseif ($s['is_approved'] === 'suspended'): ?>
                  <a href="<?= BASE_URL ?>/public/index.php?url=admin/reactivateSeller/<?= $s['id'] ?>" class="btn btn-sm btn-primary">Reactivate</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
