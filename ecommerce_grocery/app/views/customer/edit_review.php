<div class="page container" style="max-width:500px;">
  <div class="card">
    <h2 class="page-title">Edit Review</h2>
    <form method="post" action="<?= BASE_URL ?>/public/index.php?url=customer/editReview/<?= $review['id'] ?>">
      <div class="form-group">
        <label>Rating</label>
        <select name="rating">
          <?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= $i ?>" <?= $review['rating'] == $i ? 'selected' : '' ?>><?= $i ?> star<?= $i > 1 ? 's' : '' ?></option><?php endfor; ?>
        </select>
      </div>
      <div class="form-group"><label>Review</label><textarea name="review_text"><?= htmlspecialchars($review['review_text']) ?></textarea></div>
      <button class="btn btn-primary">Update Review</button>
    </form>
  </div>
</div>
