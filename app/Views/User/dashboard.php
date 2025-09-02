<?= $this->extend('Template/index'); ?>
<?= $this->section('content'); ?>

<!-- Fixed Carousel (Single Image) -->
<div class="carousel" style="height:450px;">
  <img src="<?= base_url('img/slide1.jpg'); ?>"
    class="d-block w-100"
    style="height:490px; object-fit:cover;"
    alt="Banner">
  <div class="carousel-caption d-flex flex-column justify-content-center align-items-center text-center"
    style="color:white; height:100%;">
    <h5 class="display-6 fw-bold">Pusat Data Statistik Kota Tegal</h5>
  </div>
</div>

<!-- Main Content -->
<div class="container mt-5" id="data-indikator">
  <div class="stats-container">
    <div class="row">
      <!-- Data Indicator Strategis -->
      <div class="col-lg-8">
        <h2 class="section-title mb-4">DATA INDIKATOR STRATEGIS</h2>
        <div id="indicator-placeholder" class="map-placeholder border rounded p-5 text-center bg-white shadow-sm">
          <div id="indicator-container" class="border rounded p-4 bg-white shadow-sm">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
              <div class="pe-2">
                <h3 id="indicator-title" class="mb-1">Pilih indikator di panel kanan</h3>
                <small id="indicator-subtitle" class="text-muted d-block"></small>
              </div>

              <div class="d-flex align-items-center gap-2 flex-wrap controls-wrap">
                <select id="subindicator-select" class="form-select form-select-sm">
                  <option value="">-</option>
                </select>

                <!-- dropdown tahun khusus pie distribusi -->
                <select id="year-left" class="form-select form-select-sm d-none"></select>
                <select id="year-right" class="form-select form-select-sm d-none"></select>

                <button id="refresh-btn" class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-rotate"></i>
                </button>
              </div>
            </div>

            <div class="row g-4">
              <div class="col-lg-6">
                <div class="chart-panel border rounded p-3 bg-white">
                  <canvas id="chartLine" height="220"></canvas>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="chart-panel border rounded p-3 bg-white">
                  <canvas id="chartBar" height="220"></canvas>
                </div>
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
            <i class="fas fa-hammer me-2 text-primary"></i>Ketenagakerjaan
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
        <?php foreach ($infografis as $item): ?>
          <div class="col-lg-3 col-md-6 mb-4">
            <a href="<?= base_url('user/detail/' . $item['id']); ?>" class="text-decoration-none">
              <div class="card h-100 shadow-sm border-0">
                <div class="p-3 pb-0 bg-white">
                  <img src="<?= base_url('img/' . $item['gambar']); ?>"
                    class="img-fluid rounded border border-white"
                    alt="<?= esc($item['judul']); ?>"
                    style="max-width: auto; height: 100%; object-fit: cover;">
                </div>
                <div class="card-body pt-0">
                  <small class="text-muted d-block mt-2 mb-0">
                    <?= date('d M Y', strtotime($item['tanggal'])); ?>
                  </small>
                  <h6 class="card-title text-dark"><?= esc($item['judul']); ?></h6>
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

<!-- Chart.js -->
<?= $this->section('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.0/chart.umd.min.js" integrity="sha512-Y51n9mtKTVBh3Jbx5pZSJNDDMyY+yGe77DGtBPzRlgsf/YLCh13kSZ3JmfHGzYFCmOndraf0sQgfM654b7dJ3w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
  // Global defaults biar rapi konsisten
  Chart.defaults.responsive = true;
  Chart.defaults.maintainAspectRatio = false;
  Chart.defaults.font.size = 12;
  Chart.defaults.elements.line.tension = 0.25;
  Chart.defaults.plugins.legend.position = 'bottom';
  Chart.defaults.plugins.legend.labels.usePointStyle = true;
  Chart.defaults.plugins.legend.labels.boxWidth = 10;
  Chart.defaults.plugins.legend.labels.boxHeight = 10;

  /** ================= CONFIG SUMBER CSV (dari Controller) ================= */
  const INDICATOR_SOURCES = <?= json_encode($csvMap ?? [], JSON_UNESCAPED_SLASHES) ?>;

  /** ================== KONFIGURASI MENU ==================
   * Tambah 'scope.kelompok' supaya baris terfilter sesuai kartu.
   * Untuk sheet pivot: patterns mencocokkan nama baris (SubIndikator/Kelompok).
   * type:
   *  - 'pivotRow'      : 1 baris -> 1 seri
   *  - 'pivotMulti'    : N baris -> N seri (mis. Laki-laki & Perempuan)
   *  - 'pieDualPivot'  : 3 baris -> 2 pie utk 2 tahun
   */
  const MENU = {
    'LUAS_WILAYAH': {
      key: 'LUAS_KEPENDUDUKAN',
      scope: {
        kelompok: /^luas\s*wilayah/i
      },
      options: [{
        label: 'Luas Wilayah',
        patterns: [/luas\s*wilayah/i],
        type: 'pivotRow',
      }]
    },
    'KEPENDUDUKAN': {
      key: 'LUAS_KEPENDUDUKAN',
      scope: {
        kelompok: /^kependudukan/i
      },
      options: [{
          label: 'Jumlah Penduduk',
          patterns: [/^jumlah\s*penduduk/i],
          type: 'pivotRow'
        },
        {
          label: 'Laki-laki & Perempuan',
          patterns: [/(\W|^)-?\s*laki/i, /(\W|^)-?\s*perempuan/i],
          type: 'pivotMulti'
        },
        {
          label: 'Angka Ketergantungan',
          patterns: [/ketergantungan/i],
          type: 'pivotRow'
        },
        {
          label: 'Kepadatan Penduduk',
          patterns: [/kepadatan/i],
          type: 'pivotRow'
        },
        {
          label: 'Sex Ratio',
          patterns: [/sex\s*ratio|rasio\s*jenis\s*kelamin/i],
          type: 'pivotRow'
        },
      ]
    },
    'KEMISKINAN': {
      key: 'ANGKA_KEMISKINAN',
      scope: {
        kelompok: /^angka\s*kemiskinan/i
      },
      options: [{
          label: 'Persentase Penduduk Miskin',
          patterns: [/persentase.*miskin/i],
          type: 'pivotRow'
        },
        {
          label: 'Indeks Kedalaman Kemiskinan (P1)',
          patterns: [/kedalaman.*kemiskinan|p1\b/i],
          type: 'pivotRow'
        },
        {
          label: 'Indeks Keparahan Kemiskinan (P2)',
          patterns: [/keparahan.*kemiskinan|p2\b/i],
          type: 'pivotRow'
        },
        {
          label: 'Garis Kemiskinan',
          patterns: [/garis.*kemiskinan/i],
          type: 'pivotRow'
        },
      ]
    },
    'INFLASI UMUM': {
      key: 'INFLASI_UMUM',
      scope: {
        kelompok: /^inflasi\s*umum/i
      },
      options: [{
          label: 'Data Inflasi',
          patterns: [/inflasi\s*umum/i],
          type: 'pivotRow'
        },
        {
          label: 'Makanan, Minuman, dan Tembakau',
          patterns: [/makanan.*minuman.*tembakau/i],
          type: 'pivotRow'
        },
        {
          label: 'Pakaian dan Alas Kaki',
          patterns: [/pakaian.*alas.*kaki/i],
          type: 'pivotRow'
        },
        {
          label: 'Perumahan, Air, Listrik, Gas, dan Bahan Bakar Rumah Tangga',
          patterns: [/perumahan|listrik|gas|bahan.*bakar.*rumah/i],
          type: 'pivotRow'
        },
        {
          label: 'Perlengkapan/Peralatan/Pemeliharaan Rutin',
          patterns: [/perlengkapan|peralatan|pemeliharaan.*rutin/i],
          type: 'pivotRow'
        },
        {
          label: 'Kesehatan',
          patterns: [/kesehatan/i],
          type: 'pivotRow'
        },
        {
          label: 'Transportasi',
          patterns: [/transportasi/i],
          type: 'pivotRow'
        },
        {
          label: 'Informasi, Komunikasi, dan Jasa Keuangan',
          patterns: [/informasi|komunikasi|jasa.*keuangan/i],
          type: 'pivotRow'
        },
        {
          label: 'Rekreasi, Olahraga, dan Budaya',
          patterns: [/rekreasi|olahraga|budaya/i],
          type: 'pivotRow'
        },
        {
          label: 'Pendidikan',
          patterns: [/pendidikan/i],
          type: 'pivotRow'
        },
        {
          label: 'Penyediaan Makanan dan Minuman/Restoran',
          patterns: [/restoran|penyediaan.*makanan.*minuman/i],
          type: 'pivotRow'
        },
        {
          label: 'Perawatan Pribadi dan Jasa Lainnya',
          patterns: [/perawatan.*pribadi|jasa.*lain/i],
          type: 'pivotRow'
        },
      ]
    },
    'INDEKS PEMBANGUNAN MANUSIA': {
      key: 'IPM',
      scope: {
        kelompok: /^indeks\s*pembangunan\s*manusia/i
      },
      options: [{
          label: 'IPM',
          patterns: [/indeks\s*pembangunan\s*manusia/i],
          type: 'pivotRow'
        },
        {
          label: 'Umur Harapan Hidup',
          patterns: [/umur\s*harapan\s*hidup/i],
          type: 'pivotRow'
        },
        {
          label: 'Harapan Lama Sekolah',
          patterns: [/harapan\s*lama\s*sekolah/i],
          type: 'pivotRow'
        },
        {
          label: 'Rata-rata Lama Sekolah (RLS)',
          patterns: [/rata.*lama.*sekolah|^rls$/i],
          type: 'pivotRow'
        },
        {
          label: 'Pengeluaran per Kapita yang Disesuaikan',
          patterns: [/pengeluaran.*disesuaikan/i],
          type: 'pivotRow'
        },
        {
          label: 'PPP (Poverty Power Parity)',
          patterns: [/ppp|purchasing.*power.*parity/i],
          type: 'pivotRow'
        },
      ]
    },
    'PDRB': {
      key: 'PDRB',
      scope: {
        kelompok: /^pdrb\b/i
      },
      options: [{
          label: 'Atas Dasar Harga Berlaku (ADHB)',
          patterns: [/harga\s*berlaku|adhb/i],
          type: 'pivotRow'
        },
        {
          label: 'Atas Dasar Harga Konstan (ADHK)',
          patterns: [/harga\s*konstan|adhk/i],
          type: 'pivotRow'
        },
        {
          label: 'Laju Pertumbuhan Ekonomi',
          patterns: [/laju.*pertumbuhan/i],
          type: 'pivotRow'
        },
        {
          label: 'PDRB Per Kapita',
          patterns: [/per\s*kapita/i],
          type: 'pivotRow'
        },
      ]
    },
    'KETENAGAKERJAAN': {
      key: 'KETENAGAKERJAAN',
      scope: {
        kelompok: /^ketenagakerjaan/i
      },
      options: [{
          label: 'Tingkat Pengangguran Terbuka (TPT)',
          patterns: [/pengangguran.*terbuka|tpt/i],
          type: 'pivotRow'
        },
        {
          label: 'Tingkat Partisipasi Angkatan Kerja (TPAK)',
          patterns: [/partisipasi.*angkatan.*kerja|tpak/i],
          type: 'pivotRow'
        },
      ]
    },
    'KESEJAHTERAAN': {
      key: 'KESEJAHTERAAN',
      scope: {
        kelompok: /^kesejahteraan/i
      },
      options: [{
          label: 'Gini Ratio',
          patterns: [/gini/i],
          type: 'pivotRow'
        },
        {
          label: 'Distribusi Pendapatan',
          patterns: [/40.*bawah/i, /40.*tengah/i, /20.*atas/i],
          type: 'pieDualPivot'
        },
      ]
    }
  };

  /** ============== STATE & UTIL ============== */
  let lineChart = null,
    barChart = null,
    lastRows = [],
    lastYearCols = [],
    lastCardKey = '';
  const $title = document.getElementById('indicator-title');
  const $subtitle = document.getElementById('indicator-subtitle');
  const $select = document.getElementById('subindicator-select');
  const $refresh = document.getElementById('refresh-btn');
  const $yearL = document.getElementById('year-left');
  const $yearR = document.getElementById('year-right');
  // Aktifkan 'charts-mode' pada placeholder jika #indicator-container ada
  const $placeholder = document.getElementById('indicator-placeholder');
  if ($placeholder && document.getElementById('indicator-container')) {
    $placeholder.classList.add('charts-mode');
  }


  const zwsRE = /\u00A0|\u200B/g;
  const sanitizeHeader = (h) => String(h || '').replace(/^\uFEFF/, '').replace(zwsRE, '').trim();
  const normText = (s) => sanitizeHeader(s).replace(/^-+\s*/, '').trim(); // buang '-' di awal
  const humanize = (s) => s.replace(/_/g, ' ').replace(/\b\w/g, m => m.toUpperCase());

  const toNumber = (v) => {
    if (v == null || v === '') return null;
    if (typeof v === 'number') return v;
    let s = String(v).replace(zwsRE, '').trim();
    if (/^\d{1,3}(\.\d{3})+(,\d+)?$/.test(s)) s = s.replace(/\./g, '').replace(',', '.');
    else if (/^\d{1,3}(,\d{3})+(\.\d+)?$/.test(s)) s = s.replace(/,/g, '');
    else if (/^\d+(,\d+)$/.test(s)) s = s.replace(',', '.');
    if (!/^\d+(\.\d+)?$/.test(s)) return null;
    const n = parseFloat(s);
    return Number.isNaN(n) ? null : n;
  };

  function resetCharts() {
    if (lineChart) {
      lineChart.destroy();
      lineChart = null;
    }
    if (barChart) {
      barChart.destroy();
      barChart = null;
    }
  }

  function ctx(id) {
    const el = document.getElementById(id);
    if (!el) throw new Error(`Canvas #${id} tidak ditemukan`);
    const c = el.getContext('2d');
    if (!c) throw new Error(`Gagal ambil context #${id}`);
    return c;
  }

  /** ================== FETCH ================== */
  async function fetchIndicator(key, force = false) {
    const url = `<?= base_url('api/indikator') ?>?key=${encodeURIComponent(key)}${force ? '&nocache=1' : ''}`;
    const res = await fetch(url, {
      cache: 'no-store'
    });
    const json = await res.json();
    if (!json.ok) throw new Error(json.error || 'Fetch gagal');
    return json; // { columns, rows }
  }

  /** Ambil label baris yang informatif (SubIndikator kalau ada, else Kelompok) */
  function getRowLabel(row) {
    const s1 = normText(row['SubIndikator'] ?? '');
    const s2 = normText(row['Kelompok'] ?? '');
    return s1 || s2 || '(tanpa label)';
  }

  /** Cari indeks baris yang cocok pola (cek SubIndikator ATAU Kelompok), dengan scoping Kelompok jika ada */
  function findRowIndexByPattern(rows, yearCols, pattern, scope) {
    for (let i = 0; i < rows.length; i++) {
      const r = rows[i];
      // baris harus ada angka pada minimal satu kolom tahun
      const hasNumber = yearCols.some(y => toNumber(r[y]) != null);
      if (!hasNumber) continue;

      // jika ada scope.kelompok → wajib match
      if (scope?.kelompok) {
        const k = normText(r['Kelompok'] ?? '');
        if (!scope.kelompok.test(k)) continue;
      }

      const hay = normText((r['SubIndikator'] ?? '') + ' ' + (r['Kelompok'] ?? ''));
      if (pattern.test(hay)) return i;
    }
    return -1;
  }

  function attachLegendClass(chart) {
    // Tambahkan kelas ke container legend untuk padding custom
    const id = chart.canvas?.parentElement;
    if (!id) return;
    const legends = id.querySelectorAll('legend');
    legends.forEach(l => l.classList.add('chartjs-legend-bottom'));
  }

  /** ============= RENDER ============= */
  function renderPivotSeries(rowIdxs, yearCols, customLabels = null) {
    const labels = yearCols.slice();
    const datasets = rowIdxs.map((idx, i) => {
      const r = lastRows[idx];
      return {
        label: customLabels ? customLabels[i] : getRowLabel(r),
        data: yearCols.map(y => toNumber(r[y])),
        tension: .25,
        borderWidth: 2,
        fill: true
      };
    });

    resetCharts();
    lineChart = new Chart(ctx('chartLine'), {
      type: 'line',
      data: {
        labels,
        datasets
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
        datasets: datasets.map(d => ({
          ...d,
          fill: false
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
    attachLegendClass(lineChart);
    attachLegendClass(barChart);
  }

  function renderPieDualPivot(rowIdxs, yearCols, yearLeft, yearRight) {
    // rowIdxs: 3 baris (40 bawah, 40 tengah, 20 atas)
    const labels = rowIdxs.map(idx => getRowLabel(lastRows[idx]));
    const yL = String(yearLeft),
      yR = String(yearRight);
    const dataL = rowIdxs.map(idx => toNumber(lastRows[idx][yL] ?? 0));
    const dataR = rowIdxs.map(idx => toNumber(lastRows[idx][yR] ?? 0));

    resetCharts();
    lineChart = new Chart(ctx('chartLine'), {
      type: 'pie',
      data: {
        labels,
        datasets: [{
          label: `Distribusi ${yL}`,
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
          label: `Distribusi ${yR}`,
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
    attachLegendClass(lineChart);
    attachLegendClass(barChart);
  }

  const ro = new ResizeObserver(() => {
    if (lineChart) lineChart.resize();
    if (barChart) barChart.resize();
  });
  document.querySelectorAll('.chart-panel').forEach(p => ro.observe(p));
  window.addEventListener('orientationchange', () => {
    if (lineChart) lineChart.resize();
    if (barChart) barChart.resize();
  });


  /** ============= DROPDOWN BUILDER (PIVOT-ONLY) ============= */
  function buildDropdown(cardKey, columns) {
    $select.innerHTML = '';
    $yearL.classList.add('d-none');
    $yearR.classList.add('d-none');

    const config = MENU[cardKey];
    if (!config) return {
      usableOptions: [],
      yearCols: []
    };

    const yearCols = columns.filter(c => /^\d{4}$/.test(sanitizeHeader(c)));
    lastYearCols = yearCols;

    const usable = [];
    config.options.forEach(opt => {
      if (opt.type === 'pieDualPivot') {
        // setiap pattern harus ketemu satu baris
        const rowIdxs = (opt.patterns || []).map(p => findRowIndexByPattern(lastRows, yearCols, p, config.scope));
        if (rowIdxs.every(i => i >= 0)) {
          usable.push({
            ...opt,
            rowIdxs,
            type: 'pieDualPivot'
          });
        }
      } else if (opt.type === 'pivotMulti') {
        const rowIdxs = (opt.patterns || []).map(p => findRowIndexByPattern(lastRows, yearCols, p, config.scope));
        if (rowIdxs.every(i => i >= 0)) {
          usable.push({
            ...opt,
            rowIdxs,
            type: 'pivotMulti'
          });
        }
      } else { // pivotRow (default)
        const idx = findRowIndexByPattern(lastRows, yearCols, opt.patterns?.[0] ?? /./, config.scope);
        if (idx >= 0) {
          usable.push({
            ...opt,
            rowIdxs: [idx],
            type: 'pivotRow'
          });
        }
      }
    });

    // isi dropdown
    const ph = document.createElement('option');
    ph.value = '';
    ph.textContent = '— Pilih Opsi —';
    $select.appendChild(ph);

    usable.forEach((u, i) => {
      const o = document.createElement('option');
      o.value = String(i); // pakai index usable
      o.textContent = u.label;
      $select.appendChild(o);
    });

    // kalau tak ada opsi -> kosongkan chart
    if (usable.length === 0) {
      resetCharts();
    }

    return {
      usableOptions: usable,
      yearCols
    };
  }

  /** ============= ON-CHANGE ============= */
  function renderFromSelection(usableOptions, yearCols) {
    const sel = $select.value;
    if (!sel) {
      resetCharts();
      return;
    }
    const idx = Number(sel);
    const opt = usableOptions[idx];
    if (!opt) {
      resetCharts();
      return;
    }

    if (opt.type === 'pieDualPivot') {
      $yearL.classList.remove('d-none');
      $yearR.classList.remove('d-none');
      $yearL.innerHTML = yearCols.map(y => `<option>${y}</option>`).join('');
      $yearR.innerHTML = yearCols.map(y => `<option>${y}</option>`).join('');
      if (yearCols.length >= 2) {
        $yearL.value = yearCols[yearCols.length - 2];
        $yearR.value = yearCols[yearCols.length - 1];
      }
      const draw = () => renderPieDualPivot(opt.rowIdxs, yearCols, $yearL.value, $yearR.value);
      draw();
      $yearL.onchange = draw;
      $yearR.onchange = draw;
      return;
    }

    $yearL.classList.add('d-none');
    $yearR.classList.add('d-none');

    // pivotRow / pivotMulti
    renderPivotSeries(opt.rowIdxs, yearCols);
  }

  /** ============= MAIN HANDLER ============= */
  async function loadIndicatorByCard(cardEl, force = false) {
    const cardKey = (cardEl.getAttribute('data-indicator') || '').toUpperCase();
    const conf = MENU[cardKey];
    if (!conf) return alert('Indikator belum dipetakan: ' + cardKey);

    lastCardKey = cardKey;
    $title.textContent = humanize(cardKey);
    $subtitle.textContent = '2019–2024';

    // bersihkan kontrol dulu
    resetCharts();
    $select.innerHTML = '<option value="">— Pilih Opsi —</option>';
    $yearL.classList.add('d-none');
    $yearR.classList.add('d-none');

    const {
      columns,
      rows
    } = await fetchIndicator(conf.key, force);
    lastRows = rows;

    const {
      usableOptions,
      yearCols
    } = buildDropdown(cardKey, columns);
    if (usableOptions.length) {
      $select.value = '0';
      renderFromSelection(usableOptions, yearCols);
    } else {
      // tampilkan placeholder bila dataset kosong
      const c1 = ctx('chartLine');
      c1.canvas.getContext && c1.canvas.getContext('2d');
      const c2 = ctx('chartBar');
      c2.canvas.getContext && c2.canvas.getContext('2d');
    }

    $select.onchange = () => renderFromSelection(usableOptions, yearCols);
    $refresh.onclick = () => loadIndicatorByCard(cardEl, true);
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