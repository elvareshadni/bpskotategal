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
                <input type="text" class="form-control" id="judulInfografis" name="judulInfografis" placeholder="Masukkan judul">
            </div>

            <div class="form-group">
                <label for="deskripsiInfografis">Deskripsi</label>
                <input type="text" class="form-control" id="deskripsiInfografis" name="deskripsiInfografis" placeholder="Masukkan deskripsi singkat">
            </div>

            <div class="form-group">
                <label for="infografisImage">Upload gambar Carousell (JPG/PNG)</label>
                <input type="file" class="form-control-file" id="infografisImage" name="infografisImage" accept=".jpg,.jpeg,.png">
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <hr class="my-4">

        <a href="<?= base_url('admin/edit-infografis/list'); ?>" class="btn btn-outline-secondary">
            Edit Infografis yang Sudah Ada
        </a>
    </div>
</div>

<?= $this->endSection(); ?>