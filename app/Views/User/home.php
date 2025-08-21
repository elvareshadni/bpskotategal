<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPS Kota Tegal - Badan Pusat Statistik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwJSIgaGVpZ2h0PSI0MDBweCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8ZGVmcz4KICAgIDxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQiIHgxPSIwJSIgeTE9IjAlIiB4Mj0iMTAwJSIgeTI9IjEwMCUiPgogICAgICA8c3RvcCBvZmZzZXQ9IjAlIiBzdHlsZT0ic3RvcC1jb2xvcjojNEY5NENEO3N0b3Atb3BhY2l0eToxIiAvPgogICAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0eWxlPSJzdG9wLWNvbG9yOiM4N0NFRkE7c3RvcC1vcGFjaXR5OjEiIC8+CiAgICA8L2xpbmVhckdyYWRpZW50PgogIDwvZGVmcz4KICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyYWRpZW50KSIgLz4KICA8IS0tIEJ1aWxkaW5nIFNpbGhvdWV0dGVzIC0tPgogIDxyZWN0IHg9IjUwIiB5PSIyMDAiIHdpZHRoPSI2MCIgaGVpZ2h0PSIyMDAiIGZpbGw9InJnYmEoMCwwLDAsMC4zKSIgLz4KICA8cmVjdCB4PSIxNTAiIHk9IjE1MCIgd2lkdGg9IjgwIiBoZWlnaHQ9IjI1MCIgZmlsbD0icmdiYSgwLDAsMCwwLjQpIiAvPgogIDxyZWN0IHg9IjI4MCIgeT0iMTAwIiB3aWR0aD0iMTAwIiBoZWlnaHQ9IjMwMCIgZmlsbD0icmdiYSgwLDAsMCwwLjMpIiAvPgogIDxyZWN0IHg9IjQyMCIgeT0iMTMwIiB3aWR0aD0iNzAiIGhlaWdodD0iMjcwIiBmaWxsPSJyZ2JhKDAsMCwwLDAuNCkiIC8+CiAgPHJlY3QgeD0iNTMwIiB5PSI4MCIgd2lkdGg9IjkwIiBoZWlnaHQ9IjMyMCIgZmlsbD0icmdiYSgwLDAsMCwwLjMpIiAvPgogIDxyZWN0IHg9IjY2MCIgeT0iMTEwIiB3aWR0aD0iODAiIGhlaWdodD0iMjkwIiBmaWxsPSJyZ2JhKDAsMCwwLDAuNCkiIC8+CiAgPHJlY3QgeD0iNzgwIiB5PSIxNjAiIHdpZHRoPSI2MCIgaGVpZ2h0PSIyNDAiIGZpbGw9InJnYmEoMCwwLDAsMC4zKSIgLz4KPC9zdmc+');
            background-size: cover;
            background-position: center;
            min-height: 400px;
            position: relative;
            color: white;
        }
        
        .navbar-brand img {
            height: 40px;
        }
        
        .navbar {
            background: linear-gradient(135deg, #1e40af, #3b82f6) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .indicator-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #3b82f6;
            transition: all 0.3s ease;
        }
        
        .indicator-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .news-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 280px;
        }
        
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        
        .news-image {
            width: 100%;
            height: 150px;
            background: linear-gradient(45deg, #e5e7eb, #f3f4f6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
        }
        
        .footer {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white;
            padding: 40px 0 20px 0;
        }
        
        .section-title {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: #3b82f6;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
        }
        
        .stats-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 30px;
            margin-top: -80px;
            position: relative;
            z-index: 10;
        }
        
        .map-placeholder {
            background: linear-gradient(45deg, #f8fafc, #e2e8f0);
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiNGRkZGRkYiLz4KPHBhdGggZD0iTTEwIDEwSDE1VjMwSDEwVjEwWiIgZmlsbD0iIzM5NEJGRiIvPgo8cGF0aCBkPSJNMTcgMTVIMjJWMzBIMTdWMTVaIiBmaWxsPSIjNjM5REZGIi8+CjxwYXRoIGQ9Ik0yNCA4SDI5VjMwSDI0VjhaIiBmaWxsPSIjMzk0QkZGIi8+Cjx0ZXh0IHg9IjIwIiB5PSIzNiIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjQiIGZpbGw9IiMzOTRCRkYiIHRleHQtYW5jaG9yPSJtaWRkbGUiPkJQUzwvdGV4dD4KPC9zdmc+" alt="BPS Logo" class="me-2">
                <div>
                    <div style="font-size: 14px; font-weight: 600;">BADAN PUSAT STATISTIK</div>
                    <div style="font-size: 12px; opacity: 0.9;">KOTA TEGAL</div>
                </div>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Layanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Halo, User</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Data Statistik Kota Tegal</h1>
                    <p class="lead mb-4">Menyediakan data statistik terpercaya untuk pembangunan daerah yang berkelanjutan</p>
                    <button class="btn btn-primary btn-lg">
                        <i class="fas fa-chart-bar me-2"></i>Lihat Data Terbaru
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container">
        <div class="stats-container">
            <div class="row">
                <!-- Data Indicator Strategis -->
                <div class="col-lg-8">
                    <h2 class="section-title">DATA INDIKATOR STRATEGIS</h2>
                    <div class="map-placeholder">
                        <div class="text-center">
                            <i class="fas fa-map fa-3x mb-3"></i>
                            <div>Peta Indikator Strategis Kota Tegal</div>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar Indikator -->
                <div class="col-lg-4">
                    <div class="bg-primary text-white p-3 rounded-top">
                        <h5 class="mb-0">INDIKATOR</h5>
                    </div>
                    <div class="border border-top-0 p-3 rounded-bottom bg-light">
                        <div class="indicator-card">
                            <i class="fas fa-home me-2 text-primary"></i>Luas Wilayah
                        </div>
                        <div class="indicator-card">
                            <i class="fas fa-users me-2 text-primary"></i>Kependudukan
                        </div>
                        <div class="indicator-card">
                            <i class="fas fa-chart-line me-2 text-primary"></i>Angka Kemiskinan
                        </div>
                        <div class="indicator-card">
                            <i class="fas fa-money-bill-wave me-2 text-primary"></i>Inflasi Umum
                        </div>
                        <div class="indicator-card">
                            <i class="fas fa-building me-2 text-primary"></i>Indeks Pembangunan Manusia
                        </div>
                        <div class="indicator-card">
                            <i class="fas fa-chart-pie me-2 text-primary"></i>PDRB
                        </div>
                        <div class="indicator-card">
                            <i class="fas fa-briefcase me-2 text-primary"></i>Ketenagakerjaan
                        </div>
                        <div class="indicator-card">
                            <i class="fas fa-heart me-2 text-primary"></i>Kesejahteraan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- News Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title">BERITA</h2>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="news-card">
                        <div class="news-image">
                            <i class="fas fa-newspaper fa-3x"></i>
                        </div>
                        <div class="p-3">
                            <small class="text-muted">14 Januari 2025</small>
                            <h6 class="mt-2 mb-3">Refreshing Petugas SHP 2025</h6>
                            <p class="text-muted small">Kegiatan refreshing untuk meningkatkan kualitas data statistik...</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="news-card">
                        <div class="news-image">
                            <i class="fas fa-chart-bar fa-3x"></i>
                        </div>
                        <div class="p-3">
                            <small class="text-muted">14 Januari 2025</small>
                            <h6 class="mt-2 mb-3">Refreshing Petugas SHP 2025</h6>
                            <p class="text-muted small">Program pelatihan berkelanjutan untuk petugas survei...</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="news-card">
                        <div class="news-image">
                            <i class="fas fa-users fa-3x"></i>
                        </div>
                        <div class="p-3">
                            <small class="text-muted">14 Januari 2025</small>
                            <h6 class="mt-2 mb-3">Refreshing Petugas SHP 2025</h6>
                            <p class="text-muted small">Sosialisasi metodologi survei terbaru kepada tim lapangan...</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="news-card">
                        <div class="news-image">
                            <i class="fas fa-clipboard-check fa-3x"></i>
                        </div>
                        <div class="p-3">
                            <small class="text-muted">14 Januari 2025</small>
                            <h6 class="mt-2 mb-3">Refreshing Petugas SHP 2025</h6>
                            <p class="text-muted small">Evaluasi dan peningkatan kapasitas petugas statistik...</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <button class="btn btn-primary">Berita Lainnya</button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center mb-3">
                        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiNGRkZGRkYiLz4KPHBhdGggZD0iTTEwIDEwSDE1VjMwSDEwVjEwWiIgZmlsbD0iIzM5NEJGRiIvPgo8cGF0aCBkPSJNMTcgMTVIMjJWMzBIMTdWMTVaIiBmaWxsPSIjNjM5REZGIi8+CjxwYXRoIGQ9Ik0yNCA4SDI5VjMwSDI0VjhaIiBmaWxsPSIjMzk0QkZGIi8+Cjx0ZXh0IHg9IjIwIiB5PSIzNiIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjQiIGZpbGw9IiMzOTRCRkYiIHRleHQtYW5jaG9yPSJtaWRkbGUiPkJQUzwvdGV4dD4KPC9zdmc+" alt="BPS Logo" class="me-3">
                        <div>
                            <h5 class="mb-0">BADAN PUSAT STATISTIK</h5>
                            <div>KOTA TEGAL</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="mb-2">
                            <i class="fas fa-building me-2"></i>
                            Badan Pusat Statistik (BPS-Statistics of Tegal Municipality)
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Jl. Nakula Nomor 36A Tegal
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            52124 Provinsi Jawa Tengah
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-phone me-2"></i>
                            Telp/Faks (62-283) 351593
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            E-mail : bps3376@bps.go.id
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Menu Utama</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Beranda</a></li>
                                <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Produk</a></li>
                                <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Layanan</a></li>
                                <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Berita</a></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Media Sosial</h6>
                            <div class="d-flex gap-3">
                                <a href="#" class="text-white-50"><i class="fab fa-facebook-f fa-lg"></i></a>
                                <a href="#" class="text-white-50"><i class="fab fa-twitter fa-lg"></i></a>
                                <a href="#" class="text-white-50"><i class="fab fa-instagram fa-lg"></i></a>
                                <a href="#" class="text-white-50"><i class="fab fa-youtube fa-lg"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4 opacity-25">
            
            <div class="text-center">
                <small>Hak Cipta © 2023 Badan Pusat Statistik</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
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

        // Add animation on scroll
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

        // Observe news cards
        document.querySelectorAll('.news-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });

        // Observe indicator cards
        document.querySelectorAll('.indicator-card').forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateX(-20px)';
            card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
            observer.observe(card);
        });
    </script>
</body>
</html>