<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="page-title">Product Categories</div>
    <div class="card">
      <h3>Add Category</h3>
      <form method="post" action="<?= BASE_URL ?>/public/index.php?url=admin/addCategory" class="form-row" style="align-items:flex-end;">
        <div class="form-group"><label>Name</label><input type="text" name="name" required></div>
        <div class="form-group"><label>Parent Category (optional)</label>
          <select name="parent_id">
            <option value="">— Top Level —</option>
            <?php foreach ($topLevel as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group" style="flex:2;"><label>Description</label><input type="text" name="description"></div>
        <button class="btn btn-primary">Add</button>
      </form>
    </div>
    <div class="card">
      <div class="table-wrap"><table>
        <tr><th>Name</th><th>Parent</th><th>Description</th><th></th></tr>
        <?php foreach ($items as $c):
          $parentName = '-';
          if ($c['parent_id']) { foreach ($items as $p2) if ($p2['id'] == $c['parent_id']) $parentName = $p2['name']; }
        ?>
          <tr>
            <td><?= htmlspecialchars($c['name']) ?></td>
            <td><?= htmlspecialchars($parentName) ?></td>
            <td><?= htmlspecialchars($c['description']) ?></td>
            <td><a href="<?= BASE_URL ?>/public/index.php?url=admin/deleteCategory/<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">Delete</a></td>
          </tr>
        <?php endforeach; ?>
      </table></div>
    </div>
  </div>
</div>
