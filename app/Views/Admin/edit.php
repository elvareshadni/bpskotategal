<?= $this->extend('admin/templates/index'); ?>

<?= $this->section('content'); ?>

<div class="container" style="margin-top: 80px; margin-bottom: 80px;">
  <div class="row">
    <div class="col-md-8 p-2 mx-auto" style="background: rgb(240, 240, 255); border-radius: 5px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); font-size: 13px;">
      <h4 class="text-center mb-4 fw-bold">Edit infografis</h4>
      <?php if (session()->getFlashdata('success')): ?>
          <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
      <?php endif; ?>
      <form action="<?= base_url('/admin/update/' . $infografis['id']); ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field(); ?>
        <div class="mb-3">
          <label for="name" class="form-label">Nama infografis</label>
          <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $infografis['name']) ?>" required>
        </div>
        <div class="mb-3">
          <label for="address" class="form-label">Alamat</label>
          <input type="text" class="form-control" id="address" name="address" value="<?= old('address', $infografis['address']) ?>" required>
        </div>
        <div class="mb-3">
          <label for="type" class="form-label">Jenis infografis</label>
          <select class="form-select" id="type" name="type" required>
            <option value="PUTRA" <?= $infografis['type'] == 'PUTRA' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="PUTRI" <?= $infografis['type'] == 'PUTRI' ? 'selected' : '' ?>>Perempuan</option>
            <option value="CAMPUR" <?= $infografis['type'] == 'CAMPUR' ? 'selected' : '' ?>>Campur</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="price" class="form-label">Harga per Bulan</label>
          <input type="number" class="form-control" id="price" name="price" value="<?= old('price', $infografis['price']) ?>" required>
        </div>
        <div class="mb-3">
          <label for="capacity" class="form-label">Kapasitas Kamar</label>
          <input type="number" class="form-control" id="capacity" name="capacity" value="<?= old('capacity', $infografis['capacity']) ?>" required>
        </div>
        <div class="mb-3">
          <label for="description" class="form-label">Deskripsi</label>
          <textarea class="form-control" id="description" name="description" rows="3"><?= old('description', $infografis['description']) ?></textarea>
        </div>
        <div class="mb-3">
          <label for="image" class="form-label">Foto infografis</label>
          <input type="file" class="form-control" id="image" name="image">
          <?php if ($infografis['image']): ?>
            <img src="<?= base_url('img/infografis/' . $infografis['image']); ?>" alt="Foto infografis" width="100">
          <?php endif; ?>
        </div>
        <div class="mb-3 text-end">
          <button type="submit" class="btn btn-primary">Update infografis</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>