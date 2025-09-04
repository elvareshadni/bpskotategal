<?= $this->extend('Auth/Templates/index'); ?>
<?= $this->section('content'); ?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="auth-card">
    <div class="row g-0">
      <div class="col-lg-6">
        <div class="auth-right">
          <h2 class="form-title">Reset Password</h2>

          <?= $this->include('partials/flash'); ?>

          <form action="<?= base_url('reset-password') ?>" method="post">
            <input type="hidden" name="email" value="<?= esc($email) ?>">
            <input type="hidden" name="token" value="<?= esc($token) ?>">

            <input type="password" class="form-control" name="new_password" placeholder="Password Baru" required>
            <input type="password" class="form-control" name="confirm_password" placeholder="Konfirmasi Password Baru" required>

            <button type="submit" class="btn btn-primary">Simpan Password</button>
          </form>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="auth-left">
          <div>
            <h1 class="auth-title">Halo, Sobat!</h1>
            <p class="auth-subtitle">Atur password baru Anda dengan aman.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>
