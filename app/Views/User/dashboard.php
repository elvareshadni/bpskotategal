<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>

<!-- Carousel -->
<div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
  <!-- Indicators -->
  <div class="carousel-indicators">
    <?php foreach ($carousel as $index => $slide): ?>
      <button type="button" data-bs-target="#carouselExampleIndicators" 
              data-bs-slide-to="<?= $index ?>" 
              class="<?= $index == 0 ? 'active' : '' ?>" 
              aria-current="<?= $index == 0 ? 'true' : 'false' ?>" 
              aria-label="Slide <?= $index+1 ?>"></button>
    <?php endforeach; ?>
  </div>

  <!-- Slides -->
  <div class="carousel-inner">
  <?php foreach ($carousel as $index => $slide): ?>
    <div class="carousel-item <?= $index == 0 ? 'active' : '' ?>" style="height: 490px;">
      <img src="<?= base_url('img/' . $slide['gambar']); ?>" class="d-block w-100" alt="<?= esc($slide['judul']); ?>">
      <div class="carousel-caption d-flex flex-column justify-content-center align-items-<?= $slide['posisi'] ?> text-<?= $slide['posisi'] ?>" style="color:white; height:100%;">
        <h5 class="display-6 fw-bold"><?= esc($slide['judul']); ?></h5>
      </div>
    </div>
  <?php endforeach; ?>
</div>


  <!-- Controls -->
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>


<!-- Main Content -->
<div class="container mt-5" id="data-indikator">
  <div class="stats-container">
    <div class="row">
      <!-- Data Indicator Strategis -->
      <div class="col-lg-8">
        <h2 class="section-title mb-4">DATA INDIKATOR STRATEGIS</h2>
        <div id="indicator-placeholder" class="map-placeholder border rounded p-5 text-center bg-white shadow-sm">
          <i class="fas fa-map fa-3x mb-3 text-primary"></i>
          <div>Data Indikator Strategis Kota Tegal</div>
        </div>
      </div>

      <!-- Sidebar Indikator -->
      <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="bg-primary text-white p-3 rounded-top">
          <h5 class="mb-0">INDIKATOR</h5>
        </div>
        <div class="border border-top-0 p-3 rounded-bottom bg-light">
          <div class="indicator-card mb-2" data-indicator="luas_wilayah">
          <i class="fas fa-home me-2 text-primary"></i>Luas Wilayah
        </div>
        <div class="indicator-card mb-2" data-indicator="kependudukan">
          <i class="fas fa-users me-2 text-primary"></i>Kependudukan
        </div>
        <div class="indicator-card mb-2" data-indicator="kemiskinan">
          <i class="fas fa-chart-line me-2 text-primary"></i>Angka Kemiskinan
        </div>
        <div class="indicator-card mb-2" data-indicator="inflasi umum">
          <i class="fas fa-money-bill-wave me-2 text-primary"></i>Inflasi Umum
        </div>
        <div class="indicator-card mb-2" data-indicator="indeks pembangunan manusia">
          <i class="fas fa-building me-2 text-primary"></i>Indeks Pembangunan Manusia
        </div>
        <div class="indicator-card mb-2" data-indicator="PDRB">
          <i class="fas fa-chart-pie me-2 text-primary"></i>PDRB
        </div>
        <div class="indicator-card mb-2" data-indicator="ketenagakerjaan">
          <i class="fas fa-heart me-2 text-primary"></i>Ketenagakerjaan
        </div>
        <div class="indicator-card mb-2" data-indicator="kesejahteraan">
          <i class="fas fa-heart me-2 text-primary"></i>Kesejahteraan
        </div>
        </div>
      </div>
    </div>
  </div>
</div>

<section class="py-5 bg-light" id="infografis">
  <div class="container">
    <h2 class="section-title mb-4">INFOGRAFIS</h2>
    <div class="row g-3 mb-3">
      <?php if (!empty($infografis)): ?>
        <?php foreach($infografis as $item): ?>
          <div class="col-lg-3 col-md-6 mb-4">
            <a href="<?= base_url('user/detail/' . $item['id']); ?>" class="text-decoration-none">
              <div class="card h-100 shadow-sm border-0">
                <div class="p-3 pb-0 bg-white">
                  <img src="<?= base_url('img/' . $item['gambar']); ?>" 
                       class="img-fluid rounded border border-white p-1" 
                       alt="<?= esc($item['judul']); ?>" 
                       style="max-width: 100%; height: 200px; object-fit: cover;">
                </div>
                <div class="card-body">
                  <small class="text-muted"><?= date('d M Y', strtotime($item['tanggal'])); ?></small>
                  <h6 class="card-title mt-2 text-dark"><?= esc($item['judul']); ?></h6>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-muted">Belum ada data infografis.</p>
      <?php endif; ?>
    </div>

    <div class="text-center mt-3">
      <a href="<?= base_url('user/list'); ?>" class="btn btn-primary">Infografis Lainnya</a>
    </div>
  </div>
</section>

<?= $this->endSection(); ?>
