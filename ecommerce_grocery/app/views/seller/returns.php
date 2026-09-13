<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_seller.php'; ?>
  <div class="main-content">
    <div class="page-title">Return Requests</div>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No return requests.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Customer</th><th>Product</th><th>Reason</th><th>Status</th><th>Action</th></tr>
          <?php foreach ($items as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['customer_name']) ?></td>
              <td><?= htmlspecialchars($r['product_name']) ?></td>
              <td><?= htmlspecialchars($r['reason']) ?></td>
              <td><span class="badge badge-<?= $r['status'] ?>"><?= $r['status'] ?></span></td>
              <td>
                <?php if ($r['status'] === 'pending'): ?>
                  <form method="post" action="<?= BASE_URL ?>/public/index.php?url=seller/handleReturn" class="flex gap-8">
                    <input type="hidden" name="return_id" value="<?= $r['id'] ?>">
                    <input type="text" name="note" placeholder="Note" style="width:120px;">
                    <button name="decision" value="approved" class="btn btn-sm btn-primary">Approve</button>
                    <button name="decision" value="rejected" class="btn btn-sm btn-danger">Reject</button>
                  </form>
                <?php else: ?>
                  <span class="text-muted"><?= htmlspecialchars($r['seller_note'] ?? '-') ?></span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
