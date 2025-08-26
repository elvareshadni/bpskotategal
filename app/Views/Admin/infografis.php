<?= $this->extend('admin/templates/index'); ?>

<?= $this->section('content'); ?>

<!-- Sidebar -->
<div class="container" style="margin-top: 80px; margin-bottom: 80px;">
  <div class="row">
    <div class="col-md-3 p-3"
         style="background: rgb(255, 214, 221); border-radius: 5px;">
      <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
        <i class="bx bx-home-alt me-2" style="font-size: 18px;"></i>
        <span class="fs-4">Menu</span>
      </a>
      <hr>
      <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
          <a href="<?= base_url('/admin/profile'); ?>" class="nav-link link-dark d-flex align-items-center" 
             style="border-radius: 5px;">
            <img src="<?= base_url('/img/icon/profil.png');?>" alt="profil" class="me-2"
                 style="width: 17px; height: 17px; border-radius: 50%;">
            Profile
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url('/admin/infografis'); ?>"
            class="nav-link active d-flex align-items-center" aria-current="page" 
            style="background-color: rgb(222, 164, 173); border-radius: 5px;">
            <img src="<?= base_url('/img/icon/daftarinfografis.png');?>" alt="daftarinfografis" class="me-2"
              style="width: 17px; height: 17px; background-color: rgb(222, 164, 173); border-radius: 50%;">
            Daftar infografis
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url('/admin/report'); ?>" class="nav-link link-dark d-flex align-items-center" 
            style="border-radius: 5px;">
            <img src="<?= base_url('/img/icon/report.png');?>" alt="report" class="me-2"
              style="width: 17px; height: 17px; border-radius: 50%;">
            Report
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= url_to('logout'); ?>" 
             class="nav-link link-dark d-flex align-items-center" 
             style="border-radius: 5px;">
            <img src="<?= base_url('/img/icon/keluar.png');?>" 
                 alt="keluar" 
                 class="me-2"
                 style="width: 17px; height: 17px; border-radius: 50%;">
            Keluar
          </a>
        </li>
      </ul>
      <hr>
    </div>

    <!-- Form Daftar Infografis -->
    <div class="col-md-8 p-2 mx-auto" style="background: rgb(240, 240, 255); border-radius: 5px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); font-size: 13px;">
      <h4 class="text-center mb-4 fw-bold">Daftar Infografis</h4>
      <?php if (session()->getFlashdata('success')): ?>
          <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
      <?php endif; ?>
      <form>
        <div class="d-flex justify-content-between mb-3">
          <input type="text" class="form-control w-50" placeholder="Search...">
          <a type="button" href="<?= base_url('/admin/create');?>" class="btn btn-primary">+ Tambah Infografis</a>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead class="table-light text-center">
              <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Tipe infografis</th>
                <th>Harga</th>
                <th>Kapasitas Kamar</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $i=1; ?>
              <?php foreach ($infografis as $k) : ?>
              <tr>
                <td><?= $i++; ?></td>
                <td><?= $k['name'] ?></td>
                <td><?= $k['address'] ?></td>
                <td><?= $k['type'] ?></td>
                <td><?= $k['price'] ?></td>
                <td><?= $k['capacity'] ?></td>
                <td>
                  <button class="btn btn-primary btn-sm">Detail</button>
                  <a href="<?= base_url('/admin/edit/' . $k['id']); ?>" class="btn btn-success btn-sm">Edit</a>
                  <a href="<?= base_url('/admin/delete/' . $k['id']); ?>" 
                  class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus infografis ini?')">Hapus</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>