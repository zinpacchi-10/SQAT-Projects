<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_delivery.php'; ?>
  <div class="main-content">
    <div class="page-title">Delivery Agents</div>
    <div class="card">
      <h3>Add New Agent</h3>
      <form method="post" action="<?= BASE_URL ?>/public/index.php?url=delivery/addAgent" class="form-row" style="align-items:flex-end;">
        <div class="form-group"><label>Name</label><input type="text" name="name" required></div>
        <div class="form-group"><label>Vehicle Type</label><input type="text" name="vehicle_type" placeholder="Bike / Van / Truck" required></div>
        <div class="form-group"><label>Phone</label><input type="tel" name="phone" required></div>
        <button class="btn btn-primary">Add Agent</button>
      </form>
    </div>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No delivery agents yet.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Name</th><th>Vehicle</th><th>Phone</th><th>Active Deliveries</th><th>Status</th><th>Actions</th></tr>
          <?php foreach ($items as $a): $fid = 'editAgentForm' . $a['id']; ?>
            <tr>
              <td><input type="text" name="name" value="<?= htmlspecialchars($a['name']) ?>" form="<?= $fid ?>" style="width:110px;"></td>
              <td><input type="text" name="vehicle_type" value="<?= htmlspecialchars($a['vehicle_type']) ?>" form="<?= $fid ?>" style="width:90px;"></td>
              <td><input type="tel" name="phone" value="<?= htmlspecialchars($a['phone']) ?>" form="<?= $fid ?>" style="width:110px;"></td>
              <td><?= $a['active_count'] ?></td>
              <td><span class="badge <?= $a['is_active'] ? 'badge-active' : 'badge-cancelled' ?>"><?= $a['is_active'] ? 'Active' : 'Inactive' ?></span></td>
              <td class="flex gap-8">
                <form id="<?= $fid ?>" method="post" action="<?= BASE_URL ?>/public/index.php?url=delivery/editAgent/<?= $a['id'] ?>" style="display:inline;">
                  <button type="submit" class="btn btn-sm btn-secondary">Save</button>
                </form>
                <a href="<?= BASE_URL ?>/public/index.php?url=delivery/toggleAgent/<?= $a['id'] ?>" class="btn btn-sm <?= $a['is_active'] ? 'btn-danger' : 'btn-primary' ?>"><?= $a['is_active'] ? 'Deactivate' : 'Activate' ?></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
