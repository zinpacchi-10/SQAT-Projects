<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_customer.php'; ?>
  <div class="main-content">
    <div class="page-title">My Disputes</div>
    <div class="card">
      <h3>File a New Dispute</h3>
      <form method="post" action="<?= BASE_URL ?>/public/index.php?url=customer/fileDispute">
        <div class="form-group"><label>Order ID (optional)</label><input type="number" name="order_id"></div>
        <div class="form-group"><label>Describe the issue</label><textarea name="description" required></textarea></div>
        <button class="btn btn-primary">Submit Dispute</button>
      </form>
    </div>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No disputes filed.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Order</th><th>Description</th><th>Status</th><th>Admin Note</th><th>Date</th></tr>
          <?php foreach ($items as $d): ?>
            <tr>
              <td><?= $d['order_id'] ? '#' . $d['order_id'] : '-' ?></td>
              <td><?= htmlspecialchars($d['description']) ?></td>
              <td><span class="badge badge-<?= $d['status'] ?>"><?= $d['status'] ?></span></td>
              <td><?= htmlspecialchars($d['admin_note'] ?? '-') ?></td>
              <td><?= date('d M Y', strtotime($d['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
