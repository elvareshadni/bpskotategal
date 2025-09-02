<?= $this->extend('Template/index'); ?>
<?= $this->section('content'); ?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/user'); ?>">Beranda</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Profile</li>
        </ol>
    </nav>

    <h4 class="mb-4">My Profile</h4>

    <?php if (session()->getFlashdata('msg')): ?>
        <div class="alert alert-success small"><?= esc(session()->getFlashdata('msg')); ?></div>
    <?php endif; ?>

    <?php if ($errs = session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger small mb-4">
            <ul class="mb-0">
                <?php foreach ($errs as $e): ?>
                    <li><?= esc($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-3 p-4 shadow-sm">
        <!-- Avatar besar di tengah -->
        <div class="d-flex justify-content-center mb-4">
            <div class="border rounded-3 d-flex align-items-center justify-content-center"
                style="width: 180px; height: 220px;">
                <?php if (!empty($user['photo'])): ?>
                    <img id="avatarPreview" src="<?= base_url($user['photo']); ?>" alt="Foto Profil"
                        class="img-fluid h-100">
                <?php else: ?>
                    <svg id="avatarPreview" xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor"
                        class="bi bi-person" viewBox="0 0 16 16" style="color:#9ca3af;">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m4-3a4 4 0 1 1-8 0 4 4 0 0 1 8 0M14 14s-1-1.5-6-1.5S2 14 2 14s1-4 6-4 6 4 6 4" />
                    </svg>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form Data Profil -->
        <form action="<?= route_to('user.profile.update'); ?>" method="post" enctype="multipart/form-data" class="mx-auto" style="max-width: 520px;">
            <?= csrf_field(); ?>

            <div class="row g-3 align-items-center mb-2">
                <div class="col-4 text-end"><label class="col-form-label">Username:</label></div>
                <div class="col-8"><input type="text" name="username" class="form-control"
                        value="<?= old('username', $user['username'] ?? ''); ?>"></div>
            </div>

            <div class="row g-3 align-items-center mb-2">
                <div class="col-4 text-end"><label class="col-form-label">Fullname:</label></div>
                <div class="col-8"><input type="text" name="fullname" class="form-control"
                        value="<?= old('fullname', $user['fullname'] ?? ''); ?>"></div>
            </div>


            <div class="row g-3 align-items-center mb-2">
                <div class="col-4 text-end"><label class="col-form-label">Email:</label></div>
                <div class="col-8"><input type="email" name="email" class="form-control"
                        value="<?= old('email', $user['email'] ?? ''); ?>"></div>
            </div>

            <div class="row g-3 align-items-center mb-2">
                <div class="col-4 text-end"><label class="col-form-label">No. HP:</label></div>
                <div class="col-8"><input type="text" name="phone" class="form-control"
                        value="<?= old('phone', $user['phone'] ?? ''); ?>"></div>
            </div>

            <div class="row g-2 align-items-center mb-1">
                <div class="col-4 text-end"><label class="col-form-label">Upload foto profil:</label></div>
                <div class="col-8 d-flex align-items-center gap-2">
                    <input type="file" name="photo" id="photo" class="form-control form-control-sm" accept=".png,.jpg,.jpeg">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-4"></div>
                <div class="col-8">
                    <small class="text-muted">Format file: jpg, jpeg, atau png (maks 1MB).</small>
                </div>
            </div>

            <div class="text-center">
                <button class="btn btn-primary px-4">Simpan</button>
            </div>
        </form>
        
        <!-- Garis pemisah -->
        <hr class="my-5">

        <!-- Form Ubah Password -->
        <form action="<?= route_to('user.password.update'); ?>" method="post" class="mx-auto" style="max-width: 520px;">
            <?= csrf_field(); ?>

            <div class="row g-3 align-items-center mb-2">
                <div class="col-4 text-end"><label class="col-form-label">Password Sekarang:</label></div>
                <div class="col-8"><input type="password" name="current_password" class="form-control"></div>
            </div>

            <div class="row g-3 align-items-center mb-2">
                <div class="col-4 text-end"><label class="col-form-label">Password Baru:</label></div>
                <div class="col-8"><input type="password" name="new_password" class="form-control"></div>
            </div>

            <div class="row g-3 align-items-center mb-3">
                <div class="col-4 text-end"><label class="col-form-label">Konfirmasi Password Baru:</label></div>
                <div class="col-8"><input type="password" name="confirm_password" class="form-control"></div>
            </div>

            <div class="text-center">
                <button class="btn btn-primary px-4">Ubah Password</button>
            </div>
        </form>
        <div class="text-center mt-4">
            <a href="<?= route_to('logout'); ?>" class="btn btn-outline-danger px-4">
                Logout
            </a>
        </div>

    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    // Preview foto ke kotak avatar
    const input = document.getElementById('photo');
    if (input) {
        input.addEventListener('change', (e) => {
            const file = e.target.files?.[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (ev) => {
                const box = document.querySelector('#avatarPreview');
                if (box && box.tagName.toLowerCase() === 'img') {
                    box.src = ev.target.result;
                } else {
                    // ganti SVG placeholder menjadi <img>
                    const parent = document.querySelector('#avatarPreview')?.parentElement;
                    if (parent) {
                        parent.innerHTML = `<img id="avatarPreview" src="${ev.target.result}" class="img-fluid h-100" alt="Foto Profil">`;
                    }
                }
            };
            reader.readAsDataURL(file);
        });
    }
</script>
<?= $this->endSection(); ?>