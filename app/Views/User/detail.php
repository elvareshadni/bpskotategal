<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>

<body>
<div class="container py-4">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="list.html">Beranda</a></li>
      <li class="breadcrumb-item"><a href="list.html">Produk - Berita Resmi Statistik</a></li>
      <li class="breadcrumb-item active" aria-current="page">Perkembangan Statistik Pariwisata Kota Tegal Juni 2025</li>
    </ol>
  </nav>

  <h2 class="mb-3">Perkembangan Statistik Pariwisata Kota Tegal Juni 2025</h2>
  <p><strong>Tanggal Rilis:</strong> 1 Agustus 2025</p>
  <p><strong>Ukuran File:</strong> 1.213387 MB</p>

  <h5>Abstraksi</h5>
  <p>Tingkat penghunian kamar (TPK) Hotel bintang di Kota Tegal pada bulan Juni 2025 sebesar 30,77 persen.</p>

  <div class="text-center">
    <img src="<?= base_url('/img/cover2.jpg'); ?>" class="img-fluid shadow-sm" alt="Infografis">
  </div>

</div>

<?= $this->endSection(); ?>
