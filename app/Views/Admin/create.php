<?= $this->extend('admin/templates/index'); ?>

<?= $this->section('content'); ?>

<!-- Sidebar -->
<div class="container" style="margin-top: 80px; margin-bottom: 80px;">
  <div class="row">

    <!-- Form Profil -->
      <div class="" style="background: rgb(240, 240, 255); 
        border-radius: 5px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); margin-left: 25px; font-size: 15px;">
        <h4 class="text-center mb-4 fw-bold">Form Tambah Infografis</h4>
        <?php if (session()->getFlashdata('validation')): ?>
          <div class="alert alert-danger">
            <?php foreach (session()->getFlashdata('validation')->getErrors() as $error): ?>
              <li><?= esc($error) ?></li>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <form action="<?= base_url('/admin/save'); ?>" method="post">
          <?= csrf_field(); ?>
          <div class="mb-3">
            <label for="name" class="form-label">Nama Infografis</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="address" class="form-label">Alamat</label>
            <input type="text" class="form-control" id="address" name="address" required>
          </div>
          <div class="mb-3">
            <label for="type" class="form-label">Jenis Infografis</label>
            <select class="form-select" id="type" name="type" required>
              <option value="PUTRA">Laki-laki</option>
              <option value="PUTRI">Perempuan</option>
              <option value="CAMPUR">Campur</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="price" class="form-label">Harga per Bulan</label>
            <input type="number" class="form-control" id="price" name="price" required>
          </div>
          <div class="mb-3">
            <label for="capacity" class="form-label">Kapasitas Kamar</label>
            <input type="number" class="form-control" id="capacity" name="capacity" required>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label for="image" class="form-label">Foto Infografis</label>
            <input type="file" class="form-control" id="image" name="image">
          </div>
          <div class="mb-3 text-end">
            <button type="submit" class="btn btn-primary">Simpan Infografis</button>
          </div>
      </form>
      </div>
  </div>
</div>

<?= $this->endSection(); ?>