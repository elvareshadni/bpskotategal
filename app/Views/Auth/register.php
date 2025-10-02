<?= $this->extend('Auth/Templates/index'); ?>
<?= $this->section('content'); ?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="auth-card">
        <div class="row g-0">
            <div class="col-lg-6 d-flex">
                <div class="auth-left">
                    <div>
                        <h1 class="auth-title">Selamat Datang Kembali!</h1>
                        <p class="auth-subtitle">
                            Sudah memiliki akun? Silahkan login di sini
                        </p>
                        <!-- Link ke halaman login -->
                        <a href="<?= base_url('login') ?>" class="btn-outline">Login</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="auth-right">
                    <h2 class="form-title">Daftar</h2>

                    <?= $this->include('partials/flash'); ?>

                    <form action="<?= base_url('register') ?>" method="post">
                        <?= csrf_field(); ?>
                        <input type="text" class="form-control" name="username" placeholder="Username" value="<?= old('username'); ?>" required>
                        <input type="text" class="form-control" name="fullname" placeholder="Nama Lengkap" value="<?= old('fullname'); ?>" required>
                        <input type="email" class="form-control" name="email" placeholder="E-mail" value="<?= old('email'); ?>" required>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                        <input type="password" class="form-control" name="confirm_password" placeholder="Konfirmasi Password" required>
                        <button type="submit" class="btn btn-primary">Daftar</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
