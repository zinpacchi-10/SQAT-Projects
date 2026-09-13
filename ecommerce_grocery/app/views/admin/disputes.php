<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="page-title">Disputes</div>
    <div class="pill-nav">
      <a href="<?= BASE_URL ?>/public/index.php?url=admin/disputes" class="<?= $status === '' ? 'active' : '' ?>">All</a>
      <a href="<?= BASE_URL ?>/public/index.php?url=admin/disputes&status=open" class="<?= $status === 'open' ? 'active' : '' ?>">Open</a>
      <a href="<?= BASE_URL ?>/public/index.php?url=admin/disputes&status=resolved" class="<?= $status === 'resolved' ? 'active' : '' ?>">Resolved</a>
    </div>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No disputes found.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Customer</th><th>Seller</th><th>Order</th><th>Description</th><th>Status</th><th>Action</th></tr>
          <?php foreach ($items as $d): ?>
            <tr>
              <td><?= htmlspecialchars($d['customer_name']) ?></td>
              <td><?= htmlspecialchars($d['shop_name'] ?? '-') ?></td>
              <td><?= $d['order_id'] ? '#' . $d['order_id'] : '-' ?></td>
              <td><?= htmlspecialchars($d['description']) ?></td>
              <td><span class="badge badge-<?= $d['status'] ?>"><?= $d['status'] ?></span></td>
              <td>
                <?php if ($d['status'] === 'open'): ?>
                  <form method="post" action="<?= BASE_URL ?>/public/index.php?url=admin/resolveDispute" class="flex gap-8">
                    <input type="hidden" name="dispute_id" value="<?= $d['id'] ?>">
                    <input type="text" name="note" placeholder="Resolution note" style="width:150px;">
                    <button class="btn btn-sm btn-primary">Resolve</button>
                  </form>
                <?php else: ?>
                  <span class="text-muted"><?= htmlspecialchars($d['admin_note'] ?? '') ?></span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
