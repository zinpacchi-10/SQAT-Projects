<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_customer.php'; ?>
  <div class="main-content">
    <div class="page-title">My Return Requests</div>
    <div class="card">
      <?php if (empty($returns)): ?>
        <div class="empty-state">No return requests yet.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Product</th><th>Reason</th><th>Status</th><th>Seller Note</th><th>Requested</th></tr>
          <?php foreach ($returns as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['product_name']) ?></td>
              <td><?= htmlspecialchars($r['reason']) ?></td>
              <td><span class="badge badge-<?= $r['status'] ?>"><?= $r['status'] ?></span></td>
              <td><?= htmlspecialchars($r['seller_note'] ?? '-') ?></td>
              <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
