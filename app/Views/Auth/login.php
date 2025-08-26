<?= $this->extend('auth/templates/index'); ?>
<?= $this->section('content'); ?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="auth-card">
        <div class="row g-0">
            <div class="col-lg-6">
                <div class="auth-right">
                    <h2 class="form-title">Masuk</h2>
                    <!-- Action diarahkan ke dashboard -->
                    <form action="<?= base_url('login') ?>" method="post">
                        <input type="email" class="form-control" name="email" placeholder="E-mail" required>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                        <a href="<?= base_url('forget') ?>" class="forgot-password">Lupa password?</a>
                        <button type="submit" class="btn btn-primary">Masuk</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="auth-left">
                    <div>
                        <h1 class="auth-title">Halo, Sobat!</h1>
                        <p class="auth-subtitle">
                            Masukkan detail pribadi Anda dan deteksi suaramu secara instan
                        </p>
                        <!-- Link ke halaman register -->
                        <a href="<?= base_url('register') ?>" class="btn-outline">Daftar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
