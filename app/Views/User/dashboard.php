<?= $this->extend('Template/index'); ?>
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
          <div id="indicator-container" class="border rounded p-4 bg-white shadow-sm">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
              <div>
                <h3 id="indicator-title" class="mb-1">Pilih indikator di panel kanan</h3>
                <small id="indicator-subtitle" class="text-muted"></small>
              </div>

              <div class="d-flex align-items-center gap-2 flex-wrap">
                <select id="subindicator-select" class="form-select">
                  <option value="">-</option>
                </select>

                <!-- dropdown tahun khusus pie distribusi -->
                <select id="year-left" class="form-select d-none"></select>
                <select id="year-right" class="form-select d-none"></select>

                <button id="refresh-btn" class="btn btn-outline-primary">
                  <i class="fas fa-rotate"></i>
                </button>
              </div>
            </div>

            <div class="row g-4">
              <div class="col-lg-6">
                <canvas id="chartLine" height="220"></canvas>
              </div>
              <div class="col-lg-6">
                <canvas id="chartBar" height="220"></canvas>
              </div>
            </div>
          </div>


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

<!-- Chart.js -->
<?= $this->section('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.0/chart.umd.min.js" integrity="sha512-Y51n9mtKTVBh3Jbx5pZSJNDDMyY+yGe77DGtBPzRlgsf/YLCh13kSZ3JmfHGzYFCmOndraf0sQgfM654b7dJ3w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
  /** ================= CONFIG SUMBER CSV (dari Controller) ================= */
  const INDICATOR_SOURCES = <?= json_encode($csvMap ?? [], JSON_UNESCAPED_SLASHES) ?>;

  /** ================== KONFIGURASI MENU ==================
   * Setiap kartu indikator memetakan ke satu CSV key + daftar opsi.
   * Masing2 opsi berisi:
   *  - label     : teks di dropdown
   *  - patterns  : array regex utk mencari kolom aktual (1 regex = 1 seri)
   *  - type      : 'series' (line/bar) atau 'pieDual' (dua pie utk 2 tahun)
   */
  const MENU = {
    'LUAS_WILAYAH': {
      key: 'LUAS_KEPENDUDUKAN',
      options: [{
        label: 'Luas Wilayah (km2)',
        patterns: [/luas.*wilayah/i],
        type: 'series'
      }, ]
    },
    'KEPENDUDUKAN': {
      key: 'LUAS_KEPENDUDUKAN',
      options: [{
          label: 'Jumlah penduduk',
          patterns: [/jumlah.*penduduk/i],
          type: 'series'
        },
        {
          label: 'Jumlah penduduk per gender',
          patterns: [/laki/i, /perempuan/i],
          type: 'series'
        },
        {
          label: 'Angka Ketergantungan',
          patterns: [/ketergantungan/i],
          type: 'series'
        },
        {
          label: 'Kepadatan Penduduk',
          patterns: [/kepadatan/i],
          type: 'series'
        },
        {
          label: 'Sex Ratio',
          patterns: [/sex.*ratio/i],
          type: 'series'
        },
      ]
    },
    'KEMISKINAN': {
      key: 'ANGKA_KEMISKINAN',
      options: [{
          label: 'Persentase Penduduk Miskin',
          patterns: [/persentase.*miskin/i],
          type: 'series'
        },
        {
          label: 'Indeks Kedalaman Kemiskinan',
          patterns: [/kedalaman.*kemiskinan/i],
          type: 'series'
        },
        {
          label: 'Indeks Keparahan Kemiskinan',
          patterns: [/keparahan.*kemiskinan/i],
          type: 'series'
        },
        {
          label: 'Garis Kemiskinan',
          patterns: [/garis.*kemiskinan/i],
          type: 'series'
        },
      ]
    },
    'INFLASI UMUM': {
      key: 'INFLASI_UMUM',
      options: [{
          label: 'Data Inflasi',
          patterns: [/inflasi.*umum/i],
          type: 'series'
        },
        {
          label: 'Makanan, Minuman, dan Tembakau',
          patterns: [/makanan.*minuman.*tembakau/i],
          type: 'series'
        },
        {
          label: 'Pakaian dan Alas Kaki',
          patterns: [/pakaian.*alas.*kaki/i],
          type: 'series'
        },
        {
          label: 'Perumahan, Air, Listrik, Gas, dan Bahan Bakar Rumah Tangga',
          patterns: [/perumahan|listrik|gas|bahan.*bakar.*rumah/i],
          type: 'series'
        },
        {
          label: 'Perlengkapan, Peralatan, dan Pemeliharaan Rutin Rumah Tangga',
          patterns: [/perlengkapan|peralatan|pemeliharaan.*rutin/i],
          type: 'series'
        },
        {
          label: 'Kesehatan',
          patterns: [/kesehatan/i],
          type: 'series'
        },
        {
          label: 'Transportasi',
          patterns: [/transportasi/i],
          type: 'series'
        },
      ]
    },
    'INDEKS PEMBANGUNAN MANUSIA': {
      key: 'IPM',
      options: [{
          label: 'IPM',
          patterns: [/^ipm$/i, /indeks.*pembangunan.*manusia/i],
          type: 'series'
        },
        {
          label: 'Umur Harapan Hidup',
          patterns: [/umur.*harapan.*hidup/i],
          type: 'series'
        },
        {
          label: 'Harapan Lama Sekolah',
          patterns: [/harapan.*lama.*sekolah/i],
          type: 'series'
        },
        {
          label: 'RLS',
          patterns: [/^rls$/i, /rata.*lama.*sekolah/i],
          type: 'series'
        },
        {
          label: 'PPP Poverty Power Parity',
          patterns: [/ppp|parity|purchasing/i],
          type: 'series'
        },
      ]
    },
    'PDRB': {
      key: 'PDRB',
      options: [{
          label: 'Atas Dasar Harga Berlaku',
          patterns: [/harga.*berlaku|adhb/i],
          type: 'series'
        },
        {
          label: 'Atas Dasar Harga Konstan',
          patterns: [/harga.*konstan|adhk/i],
          type: 'series'
        },
        {
          label: 'Laju Pertumbuhan Ekonomi',
          patterns: [/laju.*pertumbuhan/i],
          type: 'series'
        },
        {
          label: 'PDRB per Kapita (ADHB)',
          patterns: [/kapita|adhb.*kapita/i],
          type: 'series'
        },
      ]
    },
    'KETENAGAKERJAAN': {
      key: 'KETENAGAKERJAAN',
      options: [{
          label: 'Tingkat Pengangguran Terbuka',
          patterns: [/pengangguran.*terbuka/i],
          type: 'series'
        },
        {
          label: 'Tingkat Partisipasi Angkatan Kerja',
          patterns: [/partisipasi.*angkatan.*kerja/i],
          type: 'series'
        },
      ]
    },
    'KESEJAHTERAAN': {
      key: 'KESEJAHTERAAN',
      options: [{
          label: 'Gini Ratio',
          patterns: [/gini/i],
          type: 'series'
        },
        // Pie dua tahun (kiri & kanan)
        {
          label: 'Distribusi Pendapatan',
          patterns: [/40.*bawah/i, /40.*tengah/i, /20.*atas/i],
          type: 'pieDual'
        },
      ]
    }
  };

  /** ============== STATE & UTIL ============== */
  let lineChart, barChart, lastRows = [],
    lastYearCol = 'Tahun',
    lastCardKey = '';
  const $title = document.getElementById('indicator-title');
  const $subtitle = document.getElementById('indicator-subtitle');
  const $select = document.getElementById('subindicator-select');
  const $refresh = document.getElementById('refresh-btn');
  const $yearL = document.getElementById('year-left');
  const $yearR = document.getElementById('year-right');

  const humanize = (s) => s.replace(/_/g, ' ').replace(/\b\w/g, m => m.toUpperCase());
  const zwsRE = /\u00A0|\u200B/g; // NBSP/zero‑width
  const toNumber = (v) => {
    if (v == null || v === "") return null;
    if (typeof v === "number") return v;

    let s = String(v).replace(zwsRE, "").trim();

    // 1) 12.345,67 -> 12345.67
    if (/^\d{1,3}(\.\d{3})+(,\d+)?$/.test(s)) {
      s = s.replace(/\./g, "").replace(",", ".");
      const n = parseFloat(s);
      return Number.isNaN(n) ? null : n;
    }
    // 2) 12,345.67 -> 12345.67
    if (/^\d{1,3}(,\d{3})+(\.\d+)?$/.test(s)) {
      s = s.replace(/,/g, "");
      const n = parseFloat(s);
      return Number.isNaN(n) ? null : n;
    }
    // 3) 12345,67 -> 12345.67
    if (/^\d+(,\d+)$/.test(s)) {
      s = s.replace(",", ".");
      const n = parseFloat(s);
      return Number.isNaN(n) ? null : n;
    }
    // 4) 12345.67 atau 12345
    if (/^\d+(\.\d+)?$/.test(s)) {
      const n = parseFloat(s);
      return Number.isNaN(n) ? null : n;
    }
    return null;
  };

  function sanitizeHeader(h) {
    return String(h || '')
      .replace(/^\uFEFF/, '') // BOM
      .replace(/\u00A0|\u200B/g, '') // NBSP/ZWSP
      .trim();
  }

  /** Cari nama kolom sebenarnya berdasarkan regex patterns */
  function resolveFields(columns, patterns) {
    const cleanCols = columns.map(sanitizeHeader);
    const found = [];
    patterns.forEach(re => {
      const idx = cleanCols.findIndex(c => re.test(c));
      if (idx !== -1) found.push(columns[idx]); // return nama kolom asli utk akses nilai
    });
    return found;
  }

  /** Dataset series untuk Chart.js */
  function makeDatasets(rows, fields) {
    return fields.map((field) => ({
      label: field,
      data: rows.map(r => toNumber(r[field])),
      tension: .25,
      borderWidth: 2
    }));
  }

  /** Helpers: ctx canvas */
  function ctx(id) {
    const el = document.getElementById(id);
    if (!el) throw new Error(`Canvas #${id} tidak ditemukan`);
    const c = el.getContext('2d');
    if (!c) throw new Error(`Gagal ambil context #${id}`);
    return c;
  }

  /** Render dari BARIS (tahun ada di kolom-kolom 4 digit) */
  function renderRowAcrossYears(rows, yearCols, row, label) {
    const labels = yearCols;
    const values = yearCols.map(y => toNumber(row[y]));

    if (lineChart) lineChart.destroy();
    if (barChart) barChart.destroy();

    lineChart = new Chart(ctx('chartLine'), {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label,
          data: values,
          tension: .25,
          borderWidth: 2,
          fill: true
        }]
      },
      options: {
        plugins: {
          legend: {
            position: 'bottom'
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            }
          }
        }
      }
    });

    barChart = new Chart(ctx('chartBar'), {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label,
          data: values,
          borderWidth: 1
        }]
      },
      options: {
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });
  }


  /** Render LINE + BAR */
  function renderSeries(rows, yearCol, fields) {
    const labels = rows.map(r => r[yearCol] ?? r['Tahun'] ?? r['tahun']);
    const ds = makeDatasets(rows, fields);

    if (lineChart) lineChart.destroy();
    if (barChart) barChart.destroy();

    lineChart = new Chart(ctx('chartLine'), {
      type: 'line',
      data: {
        labels,
        datasets: ds
      },
      options: {
        plugins: {
          legend: {
            position: 'bottom'
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            }
          }
        }
      }
    });
    barChart = new Chart(ctx('chartBar'), {
      type: 'bar',
      data: {
        labels,
        datasets: ds.map(d => ({
          ...d
        }))
      },
      options: {
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });
  }

  /** Render 2 PIE (Distribusi Pendapatan) */
  function renderPieDual(rows, yearCol, fields, yearLeft, yearRight) {
    // labels adalah nama-nama field (40% bawah, 40% tengah, 20% atas)
    const labels = fields;
    const rowL = rows.find(r => String(r[yearCol]) === String(yearLeft));
    const rowR = rows.find(r => String(r[yearCol]) === String(yearRight));
    const dataL = fields.map(f => toNumber(rowL?.[f] ?? 0));
    const dataR = fields.map(f => toNumber(rowR?.[f] ?? 0));

    if (lineChart) lineChart.destroy();
    if (barChart) barChart.destroy();

    lineChart = new Chart(ctx('chartLine'), {
      type: 'pie',
      data: {
        labels,
        datasets: [{
          label: `Distribusi ${yearLeft}`,
          data: dataL
        }]
      },
      options: {
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });
    barChart = new Chart(ctx('chartBar'), {
      type: 'pie',
      data: {
        labels,
        datasets: [{
          label: `Distribusi ${yearRight}`,
          data: dataR
        }]
      },
      options: {
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });
  }

  /** Ambil data CSV via API proxy */
  async function fetchIndicator(key, force = false) {
    const url = `<?= base_url('api/indikator') ?>?key=${encodeURIComponent(key)}${force ? '&nocache=1' : ''}`;
    const res = await fetch(url, {
      cache: 'no-store'
    });
    const json = await res.json();
    if (!json.ok) throw new Error(json.error || 'Fetch gagal');
    return json;
  }

  /** Bangun dropdown berdasarkan config MENU + kolom aktual */
  function buildDropdown(cardKey, columns) {
    $select.innerHTML = '';
    $yearL.classList.add('d-none');
    $yearR.classList.add('d-none');

    const config = MENU[cardKey];
    const hasYearCol = columns.some(c => /^tahun$/i.test(sanitizeHeader(c)));
    const yearCols = columns.filter(c => /^\d{4}$/.test(sanitizeHeader(c))); // ["2019",...]
    const isPivot = !hasYearCol && yearCols.length >= 2;

    // ===== MODE 1: Struktur lama (ada kolom "Tahun") =====
    if (!isPivot) {
      const yearCol = columns.find(c => /^tahun$/i.test(sanitizeHeader(c))) || 'Tahun';
      const usable = [];
      for (const opt of config.options) {
        const fields = resolveFields(columns, opt.patterns);
        if (fields.length === opt.patterns.length) {
          usable.push({
            ...opt,
            fields
          });
          const o = document.createElement('option');
          o.value = opt.label;
          o.textContent = opt.label;
          $select.appendChild(o);
        }
      }
      if (usable.length) $select.value = usable[0].label;
      return {
        yearCol,
        usableOptions: usable,
        isPivot: false,
        yearCols: []
      };
    }

    // ===== MODE 2: PIVOT (tahun sebagai kolom) =====
    // Dropdown diisi dari baris yang punya data numerik minimal di salah satu kolom tahun
    const options = [];
    lastRows.forEach((row, idx) => {
      const hasNumber = yearCols.some(y => toNumber(row[y]) != null);
      if (!hasNumber) return;

      // ambil label yang paling informatif
      const lbl = (row['SubIndikator'] && String(row['SubIndikator']).trim()) ||
        (row['SubIndikator'] && String(row['SubIndikator']).trim()) // jaga-jaga ejaan
        ||
        (row['SubIndikator'] ?? '') ||
        (row['Kelompok'] ?? '') ||
        `Baris ${idx+1}`;

      const text = String(lbl || '').trim();
      options.push({
        label: text,
        rowIndex: idx,
        type: 'pivotRow'
      });
    });

    // Jika MENU spesifik memilih subset, kamu bisa filter di sini berdasarkan cardKey
    // contoh sederhana (opsional):
    // if (cardKey === 'LUAS_WILAYAH') options = options.filter(o => /luas/i.test(o.label));

    // isi dropdown
    options.forEach(o => {
      const opt = document.createElement('option');
      opt.value = String(o.rowIndex);
      opt.textContent = o.label;
      $select.appendChild(opt);
    });
    if (options.length) $select.value = String(options[0].rowIndex);

    return {
      yearCol: null,
      usableOptions: options,
      isPivot: true,
      yearCols
    };
  }


  /** Saat menggambar dari pilihan dropdown */
  function renderFromSelection(usableOptions, yearCol, meta = {}) {
    const {
      isPivot = false, yearCols = []
    } = meta;

    if (isPivot) {
      // value dropdown = index baris
      const idx = Number($select.value);
      const opt = usableOptions.find(o => Number(o.rowIndex) === idx) || usableOptions[0];
      if (!opt) return;
      const row = lastRows[idx];
      renderRowAcrossYears(lastRows, yearCols, row, opt.label);
      return;
    }

    // mode normal (ada kolom "Tahun")
    const label = $select.value;
    const opt = usableOptions.find(o => o.label === label);
    if (!opt) return;

    if (opt.type === 'pieDual') {
      $yearL.classList.remove('d-none');
      $yearR.classList.remove('d-none');
      const years = lastRows.map(r => r[yearCol]);
      $yearL.innerHTML = years.map(y => `<option>${y}</option>`).join('');
      $yearR.innerHTML = years.map(y => `<option>${y}</option>`).join('');
      if (years.length >= 2) {
        $yearL.value = years[years.length - 2];
        $yearR.value = years[years.length - 1];
      }
      const draw = () => renderPieDual(lastRows, yearCol, opt.fields, $yearL.value, $yearR.value);
      draw();
      $yearL.onchange = draw;
      $yearR.onchange = draw;
    } else {
      $yearL.classList.add('d-none');
      $yearR.classList.add('d-none');
      renderSeries(lastRows, yearCol, opt.fields);
    }
  }


  /** Handler klik kartu indikator */
  async function loadIndicatorByCard(cardEl, force = false) {
    const cardKey = (cardEl.getAttribute('data-indicator') || '').toUpperCase();
    const conf = MENU[cardKey];
    if (!conf) return alert('Indikator belum dipetakan: ' + cardKey);

    lastCardKey = cardKey;

    $title.textContent = humanize(cardKey);
    $subtitle.textContent = '2019-2024';

    const {
      columns,
      rows
    } = await fetchIndicator(conf.key, force);
    lastRows = rows;
    const {
      yearCol,
      usableOptions,
      isPivot,
      yearCols
    } = buildDropdown(cardKey, columns);
    renderFromSelection(usableOptions, yearCol, {
      isPivot,
      yearCols
    });

    $select.onchange = () => renderFromSelection(usableOptions, yearCol, {
      isPivot,
      yearCols
    });
    $refresh.onclick = () => loadIndicatorByCard(cardEl, true); // refetch untuk auto-update
  }

  /** Pasang listener untuk semua .indicator-card */
  document.querySelectorAll('.indicator-card').forEach(el => {
    el.style.cursor = 'pointer';
    el.addEventListener('click', () => loadIndicatorByCard(el).catch(err => {
      console.error(err);
      alert('Gagal memuat data: ' + err.message);
    }));
  });

  // auto pilih kartu pertama saat halaman siap
  window.addEventListener('DOMContentLoaded', () => {
    const first = document.querySelector('.indicator-card');
    if (first) first.click();
  });
</script>
<?= $this->endSection(); ?>