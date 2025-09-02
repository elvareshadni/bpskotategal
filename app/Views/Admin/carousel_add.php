<!-- <?= $this->extend('admin/templates/index'); ?>
<?= $this->section('content'); ?>

<div class="container mt-5">
  <h3>Tambah Carousel</h3>
    <form action="<?= base_url('admin/carouselSave'); ?>" method="post" enctype="multipart/form-data">
  <div class="mb-3">
    <label for="judulcarousel" class="form-label">Judul</label>
    <input type="text" name="judulcarousel" class="form-control" required>
  </div>

  <div class="mb-3">
    <label for="carouselImage" class="form-label">Gambar</label>
    <input type="file" name="carouselImage" class="form-control" required>
  </div>

  <button type="submit" class="btn btn-primary">Simpan</button>
</form>
</div>

<?= $this->endSection(); ?> -->
