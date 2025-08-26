<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title) ?></title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

</head>

<body style="background-color: #f8f9fa;">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary py-3" style="font-size: 0.9rem;">
  <div class="container">
    <a class="navbar-brand" href="<?= base_url('/user'); ?>">
      <img src="<?= base_url('/img/logobpskotategal.png'); ?>" height="40" alt="BPS Logo">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
            data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" 
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse text-right" id="navbarText">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 me-3">
        <li class="nav-item"><a class="nav-link active" href="<?= base_url('/user'); ?>">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="#data-indikator">Data Indikator</a></li>
        <li class="nav-item"><a class="nav-link" href="#infografis">Infografis</a></li>

      </ul>
      <span class="text-white fw-semibold me-2" style="font-size: 0.85rem;">
          <?= session()->get('username') ?? 'User'; ?>
      </span>
      <img src="<?= base_url('/img/default.png'); ?>" 
           class="rounded-circle" width="32" height="32" alt="User Avatar">
    </div>
  </div>
</nav>

  <!-- Konten Dinamis -->
  <?= $this->renderSection('content'); ?>

  <!-- Footer -->
  <footer class="footer bg-dark text-white mt-5 pt-4 pb-3">
    <div class="container">
      <div class="row">
        <!-- Info -->
        <div class="col-lg-6 mb-4">
          <a class="navbar-brand d-inline-block mb-3" href="<?= base_url('/user'); ?>">
            <img src="<?= base_url('/img/logobpskotategal.png'); ?>" height="40" alt="BPS Logo">
          </a>
          <div class="text-start text-footer text-white-50">
            <div>Badan Pusat Statistik (BPS-Statistics of Tegal Municipality)</div>
            <div>Jl. Nakula Nomor 36A Tegal</div>
            <div>52124 Provinsi Jawa Tengah</div>
            <div>Telp/Faks (62-283) 351593</div>
            <div>E-mail : bps3376@bps.go.id</div>
          </div>
        </div>

      <!-- Menu & Sosial -->
      <div class="col-lg-6">
        <div class="row">
          <div class="col-md-6 mb-3">
            <h6 class="mb-3">Menu Utama</h6>
            <ul class="list-unstyled">
              <li><a href="<?= base_url('/user'); ?>" class="text-white-50 text-decoration-none">Beranda</a></li>
              <li class="nav-item"><a class="nav-link" href="#data-indikator">Data Indikator</a></li>
              <li class="nav-item"><a class="nav-link" href="#infografis">Infografis</a></li>
            </ul>
          </div>
          <div class="col-md-6 mb-3">
            <h6 class="mb-3">Media Sosial</h6>
            <div class="d-flex gap-3">
              <a href="https://www.facebook.com/bps.kotategal.3" class="text-white-50"><i class="fab fa-facebook-f fa-lg"></i></a>
              <a href="https://www.instagram.com/bpskotategal/" class="text-white-50"><i class="fab fa-instagram fa-lg"></i></a>
              <a href="https://x.com/bps_statistics" class="text-white-50"><i class="fab fa-twitter fa-lg"></i></a>
              <a href="https://www.youtube.com/channel/UCQ3MpAVvEEtiL7hBoxT_U4A" class="text-white-50"><i class="fab fa-youtube fa-lg"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

      <hr class="my-4 opacity-25">
      <div class="text-left">
        <small>Hak Cipta © <?= date('Y'); ?> Badan Pusat Statistik</small>
      </div>
    </div>
  </footer>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Custom indikator
    document.addEventListener("DOMContentLoaded", function() {
      // >>> ADD GUARD: kalau ada #indicator-container (mode chart), skip script ini
      if (document.getElementById('indicator-container')) return;

      const cards = document.querySelectorAll(".indicator-card");
      const placeholder = document.getElementById("indicator-placeholder");

      if (!placeholder) return;

      const apiBase = "https://script.googleusercontent.com/macros/echo?user_content_key=XXXX&lib=YYYY";

      cards.forEach(card => {
        card.style.cursor = "pointer";
        card.addEventListener("click", () => {
          const indicator = card.getAttribute("data-indicator");
          placeholder.innerHTML = `<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>`;
          fetch(`${apiBase}?indicator=${indicator}`)
            .then(response => response.json())
            .then(data => {
              placeholder.innerHTML = `
              <h4 class="fw-bold text-primary">${card.innerText}</h4>
              <p class="mt-3">${data.value ?? "Data tidak tersedia"}</p>
            `;
            })
            .catch(error => {
              placeholder.innerHTML = `<div class="text-danger">Gagal memuat data!</div>`;
              console.error(error);
            });
        });
      });
    });

    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });

    // Animasi saat scroll
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, observerOptions);

    document.querySelectorAll('.news-card').forEach(card => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
      observer.observe(card);
    });

    document.querySelectorAll('.indicator-card').forEach((card, index) => {
      card.style.opacity = '0';
      card.style.transform = 'translateX(-20px)';
      card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
      observer.observe(card);
    });
  </script>
  <?= $this->renderSection('scripts') ?>
</body>

</html>