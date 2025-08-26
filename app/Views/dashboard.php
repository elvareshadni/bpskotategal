<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>

<!-- Carousel -->
<div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
  <!-- Indicators -->
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>

  <!-- Slides -->
  <div class="carousel-inner">
    <!-- Slide 1 -->
    <div class="carousel-item active" style="height: 490px;">
      <img src="<?= base_url('/img/slide1.jpg'); ?>" class="d-block w-100" alt="Slide 1">
      <div class="carousel-caption d-flex flex-column justify-content-center align-items-center text-center" 
           style="color:white; height: 100%;">
        <h2 class="display-5 fw-bold">Jumlah Usaha/Perusahaan</h2>
      </div>
    </div>

    <!-- Slide 2 -->
    <div class="carousel-item" style="height: 490px;">
      <img src="<?= base_url('/img/slide1.jpg'); ?>" class="d-block w-100" alt="Slide 2">
      <div class="carousel-caption d-flex flex-column justify-content-center align-items-end text-end" 
           style="color:white; height: 100%;">
        <h5 class="display-6 fw-bold">Data Indikator</h5>
        <p class="fw-bold">Di Wilayah Kota Tegal</p>
      </div>
    </div>

    <!-- Slide 3 -->
    <div class="carousel-item" style="height: 490px;">
      <img src="<?= base_url('/img/slide1.jpg'); ?>" class="d-block w-100" alt="Slide 3">
      <div class="carousel-caption d-none d-md-block" style="color: white;">
        <h5 class="display-6 fw-bold">Data Indikator</h5>
        <p>Di Wilayah Kota Tegal</p>
      </div>
    </div>
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
    <div class="row">

  <div class="row">
    <!-- Card 1 -->
    <div class="col-lg-3 col-md-6 mb-4">
    <a href="<?= base_url('user/list'); ?>" target="_blank" class="text-decoration-none">
      <div class="card h-100 shadow-sm border-0">
        <div class="p-3 pb-0 bg-white">
          <img src="<?= base_url('/img/cover2.jpg'); ?>" 
              class="img-fluid rounded border border-white p-1" 
              alt="Poster 1" 
              style="max-width: 100%; height: auto; object-fit: cover;">
        </div>
        <div class="card-body">
          <small class="text-muted">14 Januari 2025</small>
          <h6 class="card-title mt-2 text-dark">Refreshing Petugas SHP 2025</h6>
        </div>
      </div>
    </a>
  </div>

    <!-- Card 2 -->
    <div class="col-lg-3 col-md-6 mb-4">
      <a href="https://example.com/poster1" target="_blank" class="text-decoration-none">
        <div class="card h-100 shadow-sm border-0">
          <div class="p-3 pb-0 bg-white">
            <img src="<?= base_url('/img/cover2.jpg'); ?>" 
                class="img-fluid rounded" 
                alt="Poster 1" 
                style="max-width: 100%; height: auto; object-fit: cover;">
          </div>
          <div class="card-body">
            <small class="text-muted">14 Januari 2025</small>
            <h6 class="card-title mt-2 text-dark">Refreshing Petugas SHP 2025</h6>
          </div>
        </div>
      </a>
    </div>

    <!-- Card 3 -->
    <div class="col-lg-3 col-md-6 mb-4">
      <a href="https://example.com/poster1" target="_blank" class="text-decoration-none">
        <div class="card h-100 shadow-sm border-0">
          <div class="p-3 pb-0 bg-white">
            <img src="<?= base_url('/img/cover2.jpg'); ?>" 
                class="img-fluid rounded" 
                alt="Poster 1" 
                style="max-width: 100%; height: auto; object-fit: cover;">
          </div>
          <div class="card-body">
            <small class="text-muted">14 Januari 2025</small>
            <h6 class="card-title mt-2 text-dark">Refreshing Petugas SHP 2025</h6>
          </div>
        </div>
      </a>
    </div>

    <!-- Card 4 -->
    <div class="col-lg-3 col-md-6 mb-4">
      <a href="https://example.com/poster1" target="_blank" class="text-decoration-none">
        <div class="card h-100 shadow-sm border-0">
          <div class="p-3 pb-0 bg-white">
            <img src="<?= base_url('/img/cover2.jpg'); ?>" 
                class="img-fluid rounded" 
                alt="Poster 1" 
                style="max-width: 100%; height: auto; object-fit: cover;">
          </div>
          <div class="card-body">
            <small class="text-muted">14 Januari 2025</small>
            <h6 class="card-title mt-2 text-dark">Refreshing Petugas SHP 2025</h6>
          </div>
        </div>
      </a>
    </div>
    <div class="text-center mt-3">
      <a href="<?= base_url('user/list'); ?>" class="text-decoration-none">
          <button class="btn btn-primary">Infografis Lainnya</button>
      </a>

    </div>
</section>
<?= $this->endSection(); ?>
