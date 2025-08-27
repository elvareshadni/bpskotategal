<?= $this->extend('user/index'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 text-gray-800 mb-2">Carousell</h1>
<h2 class="h5 text-gray-600 mb-4">Tambah Carousell</h2>

<div class="card shadow mb-4">
  <div class="card-body">
    <form action="#" method="post" enctype="multipart/form-data">
      <?= function_exists('csrf_field') ? csrf_field() : '' ?>

      <div class="form-group">
        <label for="judulCarousell">Judul</label>
        <input type="text" class="form-control" id="judulCarousell" name="judulCarousell" placeholder="Masukkan judul">
      </div>

      <div class="form-group">
        <label for="carousellImage">Upload gambar Carousell (JPG/PNG)</label>
        <input type="file" class="form-control-file" id="carousellImage" name="carousellImage" accept=".jpg,.jpeg,.png">
      </div>

      <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <hr class="my-4">

    <a href="<?= base_url('edit-carousell/list'); ?>" class="btn btn-outline-secondary">
      Edit Carousell yang Sudah Ada
    </a>
  </div>
</div>

<?= $this->endSection(); ?>
