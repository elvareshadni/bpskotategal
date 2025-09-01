<?= $this->extend('Template/index'); ?>
<?= $this->section('content'); ?>

<div class="container py-4">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= base_url('/user'); ?>">Beranda</a></li>
      <li class="breadcrumb-item"><a href="<?= base_url('user/list'); ?>">Infografis</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?= esc($item['judul']); ?></li>
    </ol>
  </nav>

  <h2 class="mb-3"><?= esc($item['judul']); ?></h2>
  <p><strong>Tanggal Rilis:</strong> <?= date('d M Y', strtotime($item['tanggal'])); ?></p>
  <div class="row mt-3">
    <div class="col-md-3 text-left">
      <img src="<?= base_url('img/' . $item['gambar']); ?>"
        class="img-fluid shadow-sm rounded"
        alt="<?= esc($item['judul']); ?>"
        style="max-height: 300px; width: auto; object-fit: cover;">
    </div>

    <div class="col-md-7 d-flex px-3">
    <p><?= esc($item['deskripsi'] ?? 'Belum ada deskripsi.'); ?></p>
  </div>
  </div>
</div>

<?= $this->endSection(); ?>
