<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_customer.php'; ?>
  <div class="main-content">
    <div class="page-title">Saved Addresses</div>
    <div class="card">
      <h3>Add New Address</h3>
      <form method="post" action="<?= BASE_URL ?>/public/index.php?url=customer/addAddress" class="form-row" style="align-items:flex-end;">
        <div class="form-group"><label>Label</label><input type="text" name="label" placeholder="Home / Office" value="Home"></div>
        <div class="form-group" style="flex:2;"><label>Full Address</label><input type="text" name="full_address" required></div>
        <div class="form-group"><label>City</label><input type="text" name="city"></div>
        <div class="form-group" style="flex:0;"><label><input type="checkbox" name="is_default" value="1"> Default</label></div>
        <button class="btn btn-primary">Add</button>
      </form>
    </div>
    <div class="card">
      <?php if (empty($items)): ?>
        <div class="empty-state">No saved addresses yet.</div>
      <?php else: ?>
        <div class="table-wrap"><table>
          <tr><th>Label</th><th>Address</th><th>City</th><th>Default</th><th></th></tr>
          <?php foreach ($items as $a): ?>
            <tr>
              <td><?= htmlspecialchars($a['label']) ?></td>
              <td><?= htmlspecialchars($a['full_address']) ?></td>
              <td><?= htmlspecialchars($a['city']) ?></td>
              <td><?= $a['is_default'] ? '✅' : '' ?></td>
              <td class="flex gap-8">
                <?php if (!$a['is_default']): ?><a class="btn btn-sm btn-secondary" href="<?= BASE_URL ?>/public/index.php?url=customer/setDefaultAddress/<?= $a['id'] ?>">Set Default</a><?php endif; ?>
                <a class="btn btn-sm btn-danger" href="<?= BASE_URL ?>/public/index.php?url=customer/deleteAddress/<?= $a['id'] ?>" onclick="return confirm('Delete this address?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </table></div>
      <?php endif; ?>
    </div>
  </div>
</div>
