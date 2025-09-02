<?= $this->extend('auth/templates/index'); ?>
<?= $this->section('content'); ?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="auth-card">
        <div class="row g-0">
            <div class="col-lg-6">
                <div class="auth-right">
                    <h2 class="form-title">Masuk</h2>
                    <!-- Action diarahkan ke dashboard -->
                    <form action="<?= base_url('/login'); ?>" method="post">
                        <?= csrf_field(); ?>

                        <div class="mb-3">
                            <input type="text" name="login" id="login" class="form-control"
                                placeholder="Username atau E-Mail" value="<?= old('login'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                        </div>
                        
                        <a href="<?= base_url('forget') ?>" class="forgot-password">Lupa password?</a>
                        
                        <button type="submit" class="btn btn-primary w-100">Masuk</button>
                        </form>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="auth-left">
                    <div>
                        <h1 class="auth-title">Halo, Sobat!</h1>
                        <p class="auth-subtitle">
                            Belum memiliki akun? Buat akun di sini
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
