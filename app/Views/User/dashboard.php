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
      <!-- Sidebar Indikator (DINAMIS) -->
      <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="bg-primary text-white p-3 rounded-top d-flex justify-content-between align-items-center">
          <h5 class="mb-0">INDIKATOR</h5>
          <select id="region-select" class="form-select form-select-sm w-auto bg-white text-dark"></select>
        </div>
        <div class="border border-top-0 p-3 rounded-bottom bg-light" id="indicator-list">
          <div class="text-muted">Memuat...</div>
        </div>
      </div>
    </div>
    <div class="mt-3">
      <div class="card">
        <div class="card-header bg-light">Deskripsi & Interpretasi</div>
        <div class="card-body">
          <div id="desc-text" class="mb-2 text-muted"></div>
          <div id="interpret-text" class="small"></div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.0/chart.umd.min.js" integrity="sha512-Y51n9m..." crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
  let lineChart = null,
    barChart = null,
    pieLeft = null,
    pieRight = null;
  const $title = document.getElementById('indicator-title');
  const $subtitle = document.getElementById('indicator-subtitle');
  const $select = document.getElementById('subindicator-select');
  const $yearL = document.getElementById('year-left');
  const $yearR = document.getElementById('year-right');
  const $refresh = document.getElementById('refresh-btn');
  const $region = document.getElementById('region-select');
  const $indicatorList = document.getElementById('indicator-list');
  const $desc = document.getElementById('desc-text');
  const $interp = document.getElementById('interpret-text');

  let currentIndicator = null,
    rowsCache = [],
    regionId = null,
    currentRow = null,
    currentRowMeta = null;

  function ctx(id) {
    return document.getElementById(id).getContext('2d');
  }

  function resetCharts() {
    [lineChart, barChart, pieLeft, pieRight].forEach(c => {
      if (c) {
        c.destroy();
      }
    });
    lineChart = barChart = pieLeft = pieRight = null;
  }

  function human(s) {
    return (s || '').replace(/_/g, ' ').replace(/\b\w/g, m => m.toUpperCase());
  }

  async function fetchJSON(url) {
    const r = await fetch(url, {
      cache: 'no-store'
    });
    return r.json();
  }

  async function loadRegions() {
    const j = await fetchJSON('<?= base_url('api/regions'); ?>');
    $region.innerHTML = '';
    if (!j.ok) {
      $region.innerHTML = '<option>Gagal</option>';
      return;
    }
    j.regions.forEach(r => {
      const o = document.createElement('option');
      o.value = r.id;
      o.textContent = r.name + (r.is_default ? ' (default)' : '');
      if (r.is_default && !regionId) regionId = r.id;
      $region.appendChild(o);
    });
    if (regionId) $region.value = regionId;
  }

  async function loadIndicators() {
    const j = await fetchJSON('<?= base_url('api/indicators'); ?>');
    if (!j.ok) {
      $indicatorList.innerHTML = '<div class="text-danger">Gagal memuat indikator</div>';
      return;
    }
    $indicatorList.innerHTML = '';
    j.indicators.forEach(ind => {
      const d = document.createElement('div');
      d.className = 'indicator-card mb-2';
      d.textContent = ind.name;
      d.style.cursor = 'pointer';
      d.onclick = () => selectIndicator(ind);
      $indicatorList.appendChild(d);
    });
  }

  async function selectIndicator(ind) {
    currentIndicator = ind;
    $title.textContent = ind.name;
    $subtitle.textContent = '';
    resetCharts();
    $select.innerHTML = '<option value="">— Pilih SubIndikator —</option>';
    $yearL.classList.add('d-none');
    $yearR.classList.add('d-none');

    const j = await fetchJSON('<?= base_url('api/rows'); ?>?indicator_id=' + ind.id);
    rowsCache = j.ok ? j.rows : [];
    rowsCache.forEach((r, i) => {
      const o = document.createElement('option');
      const label = r.subindikator || r.kelompok || ('Baris #' + r.id);
      o.value = r.id;
      o.textContent = label + ' — [' + (r.data_type === 'PROPORTION' ? 'Proporsi' : 'Biasa') + ', ' + (r.timeline === 'YEAR' ? 'Tahunan' : (r.timeline === 'QUARTER' ? 'Triwulan' : 'Bulanan')) + ']';
      $select.appendChild(o);
    });
  }

  async function selectRow() {
    resetCharts();
    const rid = +$select.value;
    currentRow = rowsCache.find(r => r.id === rid);
    currentRowMeta = currentRow || null;
    if (!currentRow) {
      $desc.textContent = '';
      $interp.textContent = '';
      return;
    }

    if (currentRow.data_type === 'SINGLE') {
      // filter timeline UI
      if (currentRow.timeline === 'YEAR') {
        // tampilkan filter window: all / last3 / last5
        $yearL.classList.remove('d-none');
        $yearR.classList.add('d-none');
        $yearL.innerHTML = '<option value="all">Semua Data</option><option value="last3">3 Tahun Terakhir</option><option value="last5">5 Tahun Terakhir</option>';
        $yearL.onchange = drawSeries;
      } else {
        // pilih tahun (untuk triwulan/bulanan)
        const years = await probeYears(currentRow.id, regionId);
        $yearL.classList.remove('d-none');
        $yearR.classList.add('d-none');
        $yearL.innerHTML = years.map(y => `<option>${y}</option>`).join('');
        $yearL.onchange = drawSeries;
      }
      await drawSeries();
    } else {
      // PROPORTION: tampilkan 2 selector periode kiri/kanan
      const years = await probeYears(currentRow.id, regionId);
      $yearL.classList.remove('d-none');
      $yearR.classList.remove('d-none');
      if (currentRow.timeline === 'YEAR') {
        $yearL.innerHTML = years.map(y => `<option>${y}</option>`).join('');
        $yearR.innerHTML = years.map(y => `<option>${y}</option>`).join('');
        $yearL.value = years[Math.max(0, years.length - 2)] || '';
        $yearR.value = years[Math.max(0, years.length - 1)] || '';
        $yearL.onchange = drawProportion;
        $yearR.onchange = drawProportion;
      } else if (currentRow.timeline === 'QUARTER') {
        // Tahun + triwulan via prompt sederhana (bisa diganti dropdown ganda bila mau)
        $yearL.innerHTML = years.map(y => `<option>${y}</option>`).join('');
        $yearR.innerHTML = years.map(y => `<option>${y}</option>`).join('');
        $yearL.onchange = drawProportion;
        $yearR.onchange = drawProportion;
      } else {
        $yearL.innerHTML = years.map(y => `<option>${y}</option>`).join('');
        $yearR.innerHTML = years.map(y => `<option>${y}</option>`).join('');
        $yearL.onchange = drawProportion;
        $yearR.onchange = drawProportion;
      }
      await drawProportion();
    }
  }

  async function probeYears(rowId, regionId) {
    // ambil semua tahun yg ada nilai (pakai /api/series window=all untuk SINGLE, atau /api/proportion untuk PROPORTION dengan sweep)
    // Supaya ringan, ambil dari series SINGLE (var 0) — backend sudah urutkan
    const q = await fetchJSON('<?= base_url('api/series'); ?>?row_id=' + rowId + '&region_id=' + regionId + '&window=all');
    if (q.ok && q.meta.timeline === 'YEAR') {
      return (q.labels || []).map(Number);
    }
    // fallback: range default
    const now = new Date().getFullYear();
    return [now - 5, now - 4, now - 3, now - 2, now - 1, now];
  }

  async function drawSeries() {
    const windowVal = $yearL.classList.contains('d-none') ? 'all' : $yearL.value || 'all';
    const j = await fetchJSON('<?= base_url('api/series'); ?>?row_id=' + currentRow.id + '&region_id=' + regionId + '&window=' + encodeURIComponent(windowVal));
    if (!j.ok) {
      alert(j.error || 'Gagal');
      return;
    }

    $desc.textContent = j.meta.desc || '';
    $interp.textContent = j.meta.interpretasi || '';
    const unit = j.meta.unit ? (' (' + j.meta.unit + ')') : '';

    resetCharts();
    lineChart = new Chart(ctx('chartLine'), {
      type: 'line',
      data: {
        labels: j.labels,
        datasets: [{
          label: (currentRow.subindikator || currentRow.kelompok) + unit,
          data: j.values,
          borderWidth: 2,
          tension: .25,
          fill: true
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
      type: 'bar',
      data: {
        labels: j.labels,
        datasets: [{
          label: (currentRow.subindikator || currentRow.kelompok) + unit,
          data: j.values
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

  async function drawProportion() {
    // Dapatkan periode kiri/kanan
    const t = currentRow.timeline;
    let leftQ = '',
      rightQ = '',
      leftM = '',
      rightM = '';
    let yL = parseInt($yearL.value || new Date().getFullYear());
    let yR = parseInt($yearR.value || new Date().getFullYear());

    if (t === 'QUARTER') {
      leftQ = prompt('Triwulan kiri (1-4)?', '1') || '1';
      rightQ = prompt('Triwulan kanan (1-4)?', '4') || '4';
    } else if (t === 'MONTH') {
      leftM = prompt('Bulan kiri (1-12)?', '1') || '1';
      rightM = prompt('Bulan kanan (1-12)?', '12') || '12';
    }

    const paramsL = new URLSearchParams({
      row_id: currentRow.id,
      region_id: regionId,
      year: yL
    });
    const paramsR = new URLSearchParams({
      row_id: currentRow.id,
      region_id: regionId,
      year: yR
    });
    if (t === 'QUARTER') {
      paramsL.set('quarter', leftQ);
      paramsR.set('quarter', rightQ);
    }
    if (t === 'MONTH') {
      paramsL.set('month', leftM);
      paramsR.set('month', rightM);
    }

    const [a, b] = await Promise.all([
      fetchJSON('<?= base_url('api/proportion'); ?>?' + paramsL.toString()),
      fetchJSON('<?= base_url('api/proportion'); ?>?' + paramsR.toString())
    ]);
    if (!a.ok || !b.ok) {
      alert('Gagal memuat proporsi');
      return;
    }

    $desc.textContent = a.meta.desc || '';
    $interp.textContent = a.meta.interpretasi || '';

    resetCharts();
    pieLeft = new Chart(ctx('chartLine'), {
      type: 'pie',
      data: {
        labels: a.labels,
        datasets: [{
          label: 'Kiri',
          data: a.values
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
    pieRight = new Chart(ctx('chartBar'), {
      type: 'pie',
      data: {
        labels: b.labels,
        datasets: [{
          label: 'Kanan',
          data: b.values
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

    $subtitle.textContent = (t === 'YEAR' ? ('Perbandingan ' + yL + ' vs ' + yR) :
      t === 'QUARTER' ? ('Perbandingan ' + yL + '-Q' + leftQ + ' vs ' + yR + '-Q' + rightQ) :
      ('Perbandingan ' + yL + '-' + leftM + ' vs ' + yR + '-' + rightM));
  }

  $region.onchange = async () => {
    regionId = +$region.value;
    await loadIndicators();
    resetUI();
  };
  $select.onchange = selectRow;
  $refresh.onclick = () => {
    if (currentIndicator) selectIndicator(currentIndicator);
  };

  function resetUI() {
    $title.textContent = 'Pilih indikator di panel kanan';
    $subtitle.textContent = '';
    $select.innerHTML = '<option value="">-</option>';
    resetCharts();
    $desc.textContent = '';
    $interp.textContent = '';
    $yearL.classList.add('d-none');
    $yearR.classList.add('d-none');
  }

  (async function init() {
    await loadRegions();
    if (!regionId && $region.options.length) regionId = +$region.options[0].value;
    await loadIndicators();
  })();
</script>


<?= $this->endSection(); ?>