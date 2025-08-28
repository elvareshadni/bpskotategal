<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>

<div class="container py-4">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= base_url('user/beranda'); ?>">Beranda</a></li>
      <li class="breadcrumb-item"><a href="<?= base_url('user/list'); ?>">Infografis</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?= esc($item['judul']); ?></li>
    </ol>
  </nav>

  <h2 class="mb-3"><?= esc($item['judul']); ?></h2>
  <p><strong>Tanggal Rilis:</strong> <?= date('d M Y', strtotime($item['tanggal'])); ?></p>
  <p><strong>Ukuran File:</strong> <?= esc($item['ukuran_file'] ?? '-'); ?> MB</p>

  <h5>Abstraksi</h5>
  <p><?= esc($item['abstraksi'] ?? 'Belum ada deskripsi.'); ?></p>

  <div class="text-center">
    <img src="<?= base_url('img/' . $item['gambar']); ?>" 
         class="img-fluid shadow-sm" 
         alt="<?= esc($item['judul']); ?>">
  </div>
</div>

<?= $this->endSection(); ?>
