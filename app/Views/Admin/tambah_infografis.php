<?= $this->extend('admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 text-gray-800 mb-2">Infografis</h1>
<h2 class="h5 text-gray-600 mb-4">Tambah Infografis</h2>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="<?= base_url('admin/infografis/save'); ?>" method="post" enctype="multipart/form-data">
            <?= function_exists('csrf_field') ? csrf_field() : '' ?>

            <div class="form-group">
                <label for="judulInfografis">Judul</label>
                <input type="text" class="form-control" id="judulInfografis" name="judulInfografis" placeholder="Masukkan judul" required>
            </div>

            <div class="form-group">
                <label for="deskripsiInfografis">Deskripsi</label>
                <input type="text" class="form-control" id="deskripsiInfografis" name="deskripsiInfografis" placeholder="Masukkan deskripsi singkat" required>
            </div>

            <div class="form-group">
                <label for="infografisImage">Upload gambar (JPG/PNG)</label>
                <input type="file" class="form-control-file" id="infografisImage" name="infografisImage" accept=".jpg,.jpeg,.png" onchange="previewInfografis(event)" required>
                <div class="mt-2" id="previewBox" style="display:none;">
                    <img id="previewImg" alt="Preview" style="height:100px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">
                </div>
            </div>

            <?php /* taruh di bagian paling bawah view ini, sebelum endSection */ ?>
            <script>
                function previewInfografis(e) {
                    const file = e.target.files?.[0];
                    const box = document.getElementById('previewBox');
                    const img = document.getElementById('previewImg');
                    if (!file) {
                        box.style.display = 'none';
                        img.src = '';
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = (ev) => {
                        img.src = ev.target.result;
                        box.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            </script>


            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <hr class="my-4">

        <a href="<?= base_url('admin/edit-infografis/list'); ?>" class="btn btn-outline-secondary">
            Edit Infografis yang Sudah Ada
        </a>
    </div>
</div>

<?= $this->endSection(); ?>