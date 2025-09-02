<?= $this->extend('auth/templates/index'); ?>
<?= $this->section('content'); ?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="auth-card">
        <div class="row g-0">
            <div class="col-lg-6">
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
                    <form action="<?= base_url('register') ?>" method="post">
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                        <input type="text" class="form-control" name="fullname" placeholder="Nama Lengkap" required>
                        <input type="email" class="form-control" name="email" placeholder="E-mail" required>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                        <button type="submit" class="btn btn-primary">Daftar</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
