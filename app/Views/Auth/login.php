<?= $this->extend('Auth/Templates/index'); ?>
<?= $this->section('content'); ?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="auth-card">
        <div class="row g-0">
            <div class="col-lg-6">
                <div class="auth-right">
                    <h2 class="form-title">Masuk</h2>

                    <?= $this->include('partials/flash'); ?>

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
                        <!-- Tombol Sign in with Google -->
                        <div class="mt-3">
                            <a href="<?= base_url('auth/google/redirect'); ?>" class="btn btn-light border w-100 d-flex align-items-center justify-content-center gap-2" style="height:44px;">
                                <!-- logo google kecil -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
                                    <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.8 32.9 29.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.7 1.1 7.7 3l5.7-5.7C34.4 6.1 29.5 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20c10.4 0 19-8.4 19-19 0-1.3-.1-2.2-.4-3.5z" />
                                    <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.6 16 18.9 14 24 14c3 0 5.7 1.1 7.7 3l5.7-5.7C34.4 6.1 29.5 4 24 4 15.7 4 8.6 8.6 6.3 14.7z" />
                                    <path fill="#4CAF50" d="M24 44c5.2 0 10-2 13.5-5.3l-6.2-5.1C29.3 36 26.8 37 24 37c-5.2 0-9.6-3.4-11.2-8l-6.6 5.1C8.6 39.4 15.7 44 24 44z" />
                                    <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1 2.9-3.1 5.2-5.9 6.6l6.2 5.1c-.4.3 7.4-4.2 7.4-15.7 0-1.3-.1-2.2-.4-3.5z" />
                                </svg>
                                <span>Sign in with Google</span>
                            </a>
                        </div>
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