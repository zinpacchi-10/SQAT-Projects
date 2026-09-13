<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_delivery.php'; ?>
  <div class="main-content">
    <div class="page-title">Delivery History</div>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No completed or failed deliveries yet.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Order</th><th>Customer</th><th>Zone</th><th>Agent</th><th>Status</th><th>Details</th><th>Timestamp</th><th></th></tr>
          <?php foreach ($items as $a): ?>
            <tr>
              <td>#<?= $a['order_id'] ?></td>
              <td><?= htmlspecialchars($a['customer_name']) ?></td>
              <td><?= htmlspecialchars($a['zone_name']) ?></td>
              <td><?= htmlspecialchars($a['agent_name']) ?></td>
              <td><span class="badge badge-<?= $a['status'] ?>"><?= $a['status'] ?></span></td>
              <td><?= $a['status'] === 'failed' ? htmlspecialchars($a['failed_reason']) : '-' ?></td>
              <td class="text-muted"><?= date('d M Y, h:i A', strtotime($a['assigned_at'])) ?></td>
              <td>
                <?php if ($a['status'] === 'failed'): ?>
                  <form method="post" action="<?= BASE_URL ?>/public/index.php?url=delivery/reassign" class="flex gap-8">
                    <input type="hidden" name="assignment_id" value="<?= $a['id'] ?>">
                    <select name="agent_id">
                      <?php foreach ($agents as $ag): ?><option value="<?= $ag['id'] ?>"><?= htmlspecialchars($ag['name']) ?></option><?php endforeach; ?>
                    </select>
                    <button class="btn btn-sm btn-primary">Reassign</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
