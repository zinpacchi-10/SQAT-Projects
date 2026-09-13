<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_delivery.php'; ?>
  <div class="main-content">
    <div class="page-title">Dispatch Queue</div>
    <form method="get" action="<?= BASE_URL ?>/public/index.php" class="filters-bar">
      <input type="hidden" name="url" value="delivery/dispatch">
      <div class="form-group">
        <label>Filter by Zone</label>
        <select name="zone_id" onchange="this.form.submit()">
          <option value="">All Zones</option>
          <?php foreach ($zones as $z): ?><option value="<?= $z['id'] ?>" <?= $zoneFilter == $z['id'] ? 'selected' : '' ?>><?= htmlspecialchars($z['zone_name']) ?></option><?php endforeach; ?>
        </select>
      </div>
    </form>

    <div class="card">
      <?php if (empty($orders)): ?>
        <div class="empty-state">No orders ready for dispatch.</div>
      <?php elseif (empty($agents)): ?>
        <div class="alert alert-error">No active delivery agents available. Add or activate an agent first.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Order</th><th>Customer</th><th>Zone</th><th>Status</th><th>Assign Agent</th></tr>
          <?php foreach ($orders as $o): ?>
            <tr id="orderRow<?= $o['id'] ?>">
              <td>#<?= $o['id'] ?></td>
              <td><?= htmlspecialchars($o['customer_name']) ?></td>
              <td><?= htmlspecialchars($o['zone_name']) ?></td>
              <td><span class="badge badge-<?= $o['status'] ?>"><?= $o['status'] ?></span></td>
              <td class="flex gap-8">
                <select id="agentSelect<?= $o['id'] ?>">
                  <?php foreach ($agents as $ag): ?><option value="<?= $ag['id'] ?>"><?= htmlspecialchars($ag['name']) ?> (<?= htmlspecialchars($ag['vehicle_type']) ?>)</option><?php endforeach; ?>
                </select>
                <button class="btn btn-sm btn-primary assign-btn" data-order="<?= $o['id'] ?>">Assign</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script>
document.querySelectorAll('.assign-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    const orderId = this.dataset.order;
    const agentId = document.getElementById('agentSelect' + orderId).value;
    ajaxPost('<?= BASE_URL ?>/public/index.php?url=delivery/assignAgent', { order_id: orderId, agent_id: agentId }, function (res) {
      alert(res.message);
      document.getElementById('orderRow' + orderId).remove();
    });
  });
});
</script>
