<?= $this->extend('auth/templates/index'); ?>
<?= $this->section('content'); ?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="auth-card">
    <div class="row g-0">
      <div class="col-lg-6">
        <div class="auth-right">
          <h2 class="form-title">Lupa Password</h2>

          <?= $this->include('partials/flash'); ?>

          <form action="<?= base_url('forget') ?>" method="post">
            <input type="email" class="form-control" name="email" placeholder="E-mail" value="<?= old('email') ?>" required>
            <p class="auth-subtitle">Masukkan e-mail untuk menerima tautan reset.</p>
            <button type="submit" class="btn btn-primary">Kirim Tautan Reset</button>
          </form>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="auth-left">
          <div>
            <h1 class="auth-title">Halo, Sobat!</h1>
            <p class="auth-subtitle">Ingin login menggunakan akun lain?</p>
            <!-- Link ke halaman register -->
            <a href="<?= base_url('login') ?>" class="btn-outline">Login</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>
