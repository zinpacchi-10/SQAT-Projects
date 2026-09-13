<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_delivery.php'; ?>
  <div class="main-content">
    <div class="page-title">Active Deliveries</div>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No active deliveries.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Order</th><th>Customer</th><th>Zone</th><th>Agent</th><th>Status</th><th>Assigned</th><th>Actions</th></tr>
          <?php foreach ($items as $a): ?>
            <tr id="row<?= $a['id'] ?>">
              <td>#<?= $a['order_id'] ?></td>
              <td><?= htmlspecialchars($a['customer_name']) ?></td>
              <td><?= htmlspecialchars($a['zone_name']) ?></td>
              <td><?= htmlspecialchars($a['agent_name']) ?></td>
              <td><span class="badge badge-<?= $a['status'] ?>" id="badge<?= $a['id'] ?>"><?= str_replace('_', ' ', $a['status']) ?></span></td>
              <td class="text-muted"><?= date('d M, h:i A', strtotime($a['assigned_at'])) ?></td>
              <td class="flex gap-8">
                <?php if ($a['status'] === 'assigned'): ?>
                  <button class="btn btn-sm btn-secondary status-btn" data-id="<?= $a['id'] ?>" data-status="picked_up">Picked Up</button>
                <?php elseif ($a['status'] === 'picked_up'): ?>
                  <button class="btn btn-sm btn-secondary status-btn" data-id="<?= $a['id'] ?>" data-status="in_transit">In Transit</button>
                <?php elseif ($a['status'] === 'in_transit'): ?>
                  <button class="btn btn-sm btn-primary status-btn" data-id="<?= $a['id'] ?>" data-status="delivered">Delivered</button>
                <?php endif; ?>
                <button class="btn btn-sm btn-danger fail-btn" data-id="<?= $a['id'] ?>">Failed</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script>
document.querySelectorAll('.status-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    ajaxPost('<?= BASE_URL ?>/public/index.php?url=delivery/updateDeliveryStatus', { assignment_id: this.dataset.id, status: this.dataset.status }, function () {
      location.reload();
    });
  });
});
document.querySelectorAll('.fail-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    const reason = prompt('Reason for failed delivery:');
    if (reason === null || reason.trim() === '') return;
    ajaxPost('<?= BASE_URL ?>/public/index.php?url=delivery/updateDeliveryStatus', { assignment_id: this.dataset.id, status: 'failed', reason: reason }, function () {
      location.reload();
    });
  });
});
</script>
