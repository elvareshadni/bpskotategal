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
          <a href="<?= base_url('/admin/profile'); ?>"
            class="nav-link active d-flex align-items-center" aria-current="page" 
            style="background-color: rgb(222, 164, 173); border-radius: 5px;">
            <img src="<?= base_url('/img/icon/profil.png');?>" alt="profil" class="me-2"
              style="width: 17px; height: 17px; background-color: rgb(222, 164, 173); border-radius: 50%;">
            Profile
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url('/admin/infografis'); ?>" class="nav-link link-dark d-flex align-items-center" 
            style="border-radius: 5px;">
            <img src="<?= base_url('/img/icon/daftarinfografis.png');?>" alt="daftarinfografis" class="me-2"
              style="width: 17px; height: 17px; border-radius: 50%;">
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

    <!-- Form Profil -->
      <div class="col-md-8 p-3" style="max-width: 900px; background: rgb(240, 240, 255); 
        border-radius: 5px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); margin-left: 25px; font-size: 15px;">
        <h4 class="text-center mb-4 fw-bold">Profil</h4>
        <?php if(session()->getFlashdata('pesan')): ?>
          <div class="alert alert-success">
              <?= session()->getFlashdata('pesan'); ?>
          </div>
        <?php endif; ?>
        <form action="<?= base_url('/admin/updateUser'); ?>" method="post" enctype="multipart/form-data">
         <input type="hidden" name="id" value="<?= $user['id']; ?>">
          <div class="mb-3">
              <label for="fullname" class="form-label">Nama Lengkap sesuai KTP *</label>
              <input type="text" id="fullname" name="fullname" class="form-control" 
                    value="<?= $user['fullname'] ?>" placeholder="Masukkan nama lengkap">
          </div>
          <div class="mb-3">
              <label for="phone_number" class="form-label">Nomor HP *</label>
              <input type="text" id="phone_number" name="phone_number" class="form-control" 
                    value="<?= $user['phone_number'] ?>" placeholder="Masukkan nomor HP">
          </div>
          <div class="mb-3">
              <label for="email" class="form-label">E-mail *</label>
              <input type="email" id="email" name="email" class="form-control" 
                    value="<?= $user['email'] ?>" placeholder="Masukkan email">
          </div>
          <div class="mb-3">
              <p>Foto Diri:</p>
              <div class="small font-italic text-muted mb-4">* Catatan: Ukuran foto maksimal 3MB</div>
          </div>
          <div class="input-group mb-3">
              <input type="file" class="form-control" name="user_image" id="user_image">
              <label class="input-group-text" for="user_image">Upload</label>
          </div>
          <div class="mb-3">
              <label for="gender" class="form-label">Jenis Kelamin *</label>
              <select id="gender" name="gender" class="form-select">
                  <option value="L" <?= $user['gender'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                  <option value="P" <?= $user['gender'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
              </select>
          </div>
          <div class="mb-3">
              <label for="nik" class="form-label">NIK *</label>
              <input type="text" id="nik" name="nik" class="form-control" 
                    value="<?= $user['nik'] ?>" placeholder="Masukkan NIK">
          </div>
          <div class="mb-3">
              <p>Foto Identitas:</p>
              <div class="small font-italic text-muted mb-4">* Catatan: Ukuran foto maksimal 3MB</div>
          </div>
          <div class="input-group mb-3">
              <input type="file" class="form-control" name="id_image" id="id_image">
              <label class="input-group-text" for="id_image">Upload</label>
          </div>
          <button type="submit" class="btn btn-primary text-right">Simpan</button>
        </form>
      </div>
  </div>
</div>

<?= $this->endSection(); ?>