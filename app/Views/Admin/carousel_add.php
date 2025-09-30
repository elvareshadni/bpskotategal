<?= $this->extend('Admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 text-gray-800 mb-2">Carousel</h1>
<h2 class="h5 text-gray-600 mb-4">Tambah Slide</h2>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="<?= base_url('admin/carousel/save'); ?>" method="post" enctype="multipart/form-data">
            <?= function_exists('csrf_field') ? csrf_field() : '' ?>

            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" class="form-control" name="judul" required placeholder="Judul slide">
            </div>

            <div class="mb-3">
                <label class="form-label">Posisi Teks</label>
                <select class="form-select" name="posisi">
                    <option value="start">Start (kiri)</option>
                    <option value="center" selected>Center (tengah)</option>
                    <option value="end">End (kanan)</option>
                </select>
                <small class="text-muted">Hanya sebagai metadata, sesuaikan kebutuhan tampilan.</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Gambar (JPG/PNG, maks 2MB)</label>
                <input type="file" class="form-control" name="gambar" accept=".jpg,.jpeg,.png" required id="gambar">
                <div class="mt-2" id="previewBox" style="display:none;">
                    <img id="imgPreview" alt="Preview" style="height:100px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">
                </div>
            </div>

            <script>
                (function() {
                    const input = document.getElementById('gambar');
                    const box = document.getElementById('previewBox');
                    const img = document.getElementById('imgPreview');
                    input.addEventListener('change', (e) => {
                        const file = e.target.files && e.target.files[0];
                        if (!file) {
                            box.style.display = 'none';
                            img.src = '';
                            return;
                        }
                        const url = URL.createObjectURL(file);
                        img.src = url;
                        box.style.display = '';
                    });
                })();
            </script>

            <div class="mb-3">
                <label class="form-label">Link URL (opsional)</label>
                <input type="url" class="form-control" name="link_url" placeholder="https://contoh.go.id/halaman">
                <small class="text-muted">Jika diisi, klik di area gambar akan membuka tautan ini.</small>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('admin/carousel'); ?>" class="btn btn-secondary">Kembali</a>
        </form>

        <hr class="my-4">

        <a href="<?= base_url('admin/carousel'); ?>" class="btn btn-outline-secondary">
            Edit Carousel yang Sudah Ada
        </a>
    </div>
</div>

<?= $this->endSection(); ?>