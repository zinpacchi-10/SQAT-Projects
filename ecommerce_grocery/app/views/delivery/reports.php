<div class="dashboard-shell">
  <?php require APP_ROOT . '/app/views/layouts/sidebar_delivery.php'; ?>
  <div class="main-content">
    <div class="page-title">Delivery Reports</div>

    <div class="stat-grid">
      <div class="stat-card"><div class="num"><?= $daily['delivered'] ?></div><div class="label">Delivered Today</div></div>
      <div class="stat-card"><div class="num"><?= $daily['failed'] ?></div><div class="label">Failed Today</div></div>
      <div class="stat-card"><div class="num"><?= $daily['in_transit'] ?></div><div class="label">In Transit Today</div></div>
      <div class="stat-card"><div class="num"><?= $weekly['delivered'] ?></div><div class="label">Delivered This Week</div></div>
      <div class="stat-card"><div class="num"><?= $weekly['failed'] ?></div><div class="label">Failed This Week</div></div>
    </div>

    <div class="card">
      <h3>Agent Performance</h3>
      <div class="table-wrap"><table>
        <tr><th>Agent</th><th>Delivered</th><th>Failed</th><th>Total Assignments</th><th>Avg Delivery Time (hrs)</th><th>Failure Rate</th></tr>
        <?php foreach ($agentPerformance as $ap): $rate = $ap['total_assignments'] > 0 ? round($ap['failed_count'] / $ap['total_assignments'] * 100, 1) : 0; ?>
          <tr>
            <td><?= htmlspecialchars($ap['name']) ?></td>
            <td><?= $ap['delivered_count'] ?></td>
            <td><?= $ap['failed_count'] ?></td>
            <td><?= $ap['total_assignments'] ?></td>
            <td><?= $ap['avg_hours'] ?? '-' ?></td>
            <td><?= $rate ?>%</td>
          </tr>
        <?php endforeach; ?>
      </table></div>
    </div>

    <div class="card">
      <h3>Zone Performance</h3>
      <div class="table-wrap"><table>
        <tr><th>Zone</th><th>Total Deliveries</th><th>Avg Delivery Time (hrs)</th></tr>
        <?php foreach ($zonePerformance as $zp): ?>
          <tr><td><?= htmlspecialchars($zp['zone_name']) ?></td><td><?= $zp['total_deliveries'] ?></td><td><?= $zp['avg_hours'] ?? '-' ?></td></tr>
        <?php endforeach; ?>
      </table></div>
    </div>
  </div>
</div>
