<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_delivery.php'; ?>
  <div class="main-content">
    <div class="page-title">Delivery Zones</div>
    <div class="card">
      <h3>Add New Zone</h3>
      <form method="post" action="<?= BASE_URL ?>/public/index.php?url=delivery/addZone" class="form-row" style="align-items:flex-end;">
        <div class="form-group"><label>Zone Name</label><input type="text" name="zone_name" required></div>
        <div class="form-group"><label>Delivery Fee (৳)</label><input type="number" step="0.01" name="delivery_fee" required></div>
        <div class="form-group"><label>Estimated Days</label><input type="number" name="estimated_days" min="1" required></div>
        <button class="btn btn-primary">Add Zone</button>
      </form>
    </div>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No delivery zones defined.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Zone</th><th>Fee</th><th>Est. Days</th><th>Actions</th></tr>
          <?php foreach ($items as $z): $fid = 'zoneForm' . $z['id']; ?>
            <tr>
              <td><input type="text" name="zone_name" value="<?= htmlspecialchars($z['zone_name']) ?>" form="<?= $fid ?>" style="width:130px;"></td>
              <td><input type="number" step="0.01" name="delivery_fee" value="<?= $z['delivery_fee'] ?>" form="<?= $fid ?>" style="width:90px;"></td>
              <td><input type="number" name="estimated_days" value="<?= $z['estimated_days'] ?>" form="<?= $fid ?>" style="width:70px;"></td>
              <td class="flex gap-8">
                <form id="<?= $fid ?>" method="post" action="<?= BASE_URL ?>/public/index.php?url=delivery/editZone/<?= $z['id'] ?>" style="display:inline;">
                  <button type="submit" class="btn btn-sm btn-secondary">Save</button>
                </form>
                <a href="<?= BASE_URL ?>/public/index.php?url=delivery/deleteZone/<?= $z['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this zone?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
