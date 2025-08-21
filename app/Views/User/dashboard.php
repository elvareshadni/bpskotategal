<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>

<!-- Carousel Slides -->
<div id="carouselExampleCaptions" class="carousel carousel-dark slide mb-5" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" 
            class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" 
            aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" 
            aria-label="Slide 3"></button>
  </div>

  <div class="carousel-inner">
    <div class="carousel-item active" style="height: 580px;">
      <img src="<?= base_url('/img/slide1.jpg'); ?>" class="d-block w-100" alt="Slide 1">
      <div class="carousel-caption d-flex flex-column justify-content-center align-items-center text-center" 
           style="color:white; height: 100%;">
        <h2 class="display-5 fw-bold">Jumlah Usaha/Perusahaan</h2>
      </div>
    </div>
    <div class="carousel-item" style="height: 580px;">
      <img src="https://via.placeholder.com/1200x580/5ba0f2/ffffff?text=Data+Indikator" 
           class="d-block w-100" alt="Slide 2">
      <div class="carousel-caption d-flex flex-column justify-content-center align-items-end text-end" 
           style="color:white; height: 100%;">
        <h5 class="display-6 fw-bold">Data Indikator</h5>
        <p class="fw-bold">Di Wilayah Kota Tegal</p>
      </div>
    </div>
    <div class="carousel-item" style="height: 580px;">
      <img src="https://via.placeholder.com/1200x580/6bb0f4/ffffff?text=Statistik+Tegal" 
           class="d-block w-100" alt="Slide 3">
      <div class="carousel-caption d-none d-md-block" style="color: white;">
        <h5 class="display-6 fw-bold">Data Indikator</h5>
        <p>Di Wilayah Kota Tegal</p>
      </div>
    </div>
  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>

<!-- Main Content -->
<div class="container">
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
        </div>
      </div>
    </div>
  </div>
</div>

<!-- News Section -->
<section class="py-5 bg-light">
  <div class="container">
    <h2 class="section-title mb-4">BERITA</h2>
    <div class="row">
      <div class="col-lg-3 col-md-6 mb-4">
        <div class="news-card bg-white shadow-sm rounded p-3">
          <div class="news-image text-center mb-3">
            <i class="fas fa-newspaper fa-3x text-primary"></i>
          </div>
          <small class="text-muted">14 Januari 2025</small>
          <h6 class="mt-2 mb-3">Refreshing Petugas SHP 2025</h6>
          <p class="text-muted small">Kegiatan refreshing untuk meningkatkan kualitas data statistik...</p>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-4">
        <div class="news-card bg-white shadow-sm rounded p-3">
          <div class="news-image text-center mb-3">
            <i class="fas fa-chart-bar fa-3x text-primary"></i>
          </div>
          <small class="text-muted">14 Januari 2025</small>
          <h6 class="mt-2 mb-3">Pelatihan Survei Statistik</h6>
          <p class="text-muted small">Program pelatihan berkelanjutan untuk petugas survei...</p>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-4">
        <div class="news-card bg-white shadow-sm rounded p-3">
          <div class="news-image text-center mb-3">
            <i class="fas fa-users fa-3x text-primary"></i>
          </div>
          <small class="text-muted">14 Januari 2025</small>
          <h6 class="mt-2 mb-3">Sosialisasi Metodologi Survei</h6>
          <p class="text-muted small">Sosialisasi metodologi survei terbaru kepada tim lapangan...</p>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-4">
        <div class="news-card bg-white shadow-sm rounded p-3">
          <div class="news-image text-center mb-3">
            <i class="fas fa-clipboard-check fa-3x text-primary"></i>
          </div>
          <small class="text-muted">14 Januari 2025</small>
          <h6 class="mt-2 mb-3">Evaluasi Kapasitas Petugas</h6>
          <p class="text-muted small">Evaluasi dan peningkatan kapasitas petugas statistik...</p>
        </div>
      </div>
    </div>

    <div class="text-center mt-4">
      <button class="btn btn-primary">Berita Lainnya</button>
    </div>
  </div>
</section>

<?= $this->endSection(); ?>
