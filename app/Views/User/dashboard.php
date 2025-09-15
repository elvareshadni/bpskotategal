<?= $this->extend('Template/index'); ?>
<?= $this->section('content'); ?>

<!-- Banner -->
<div class="carousel" style="height:420px;">
  <img src="<?= base_url('img/slide1.jpg'); ?>" class="d-block w-100" style="height:420px; object-fit:cover;" alt="Banner">
  <div class="carousel-caption d-flex flex-column justify-content-center align-items-center text-center" style="color:white; height:100%;">
    <h5 class="display-6 fw-bold">Pusat Data Statistik Kota Tegal</h5>
  </div>
</div>

<!-- DATA INDIKATOR STRATEGIS -->
<div class="container mt-5" id="data-indikator">
  <div class="stats-container stats-blue rounded-3 p-3 p-md-4 shadow-sm">
    <div class="row g-4">
      <!-- KIRI: CHART AREA -->
      <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
          <div>
            <h2 class="section-title mb-1 text-dark">DATA INDIKATOR STRATEGIS</h2>
            <small class="text-muted">Pilih lokasi & indikator di panel kanan untuk melihat grafik</small>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <!-- Kontrol periode dinamis -->
            <select id="window-select" class="form-select form-select-sm w-auto d-none">
              <option value="all">Semua Tahun</option>
              <option value="last3">3 Tahun Terakhir</option>
              <option value="last5">5 Tahun Terakhir</option>
            </select>
            <select id="year-select" class="form-select form-select-sm w-auto d-none"></select>
            <select id="q-select" class="form-select form-select-sm w-auto d-none">
              <option value="1">Q1</option>
              <option value="2">Q2</option>
              <option value="3">Q3</option>
              <option value="4">Q4</option>
            </select>
            <select id="m-select" class="form-select form-select-sm w-auto d-none">
              <?php for ($i = 1; $i <= 12; $i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?>
            </select>
            <button id="btn-refresh" class="btn btn-outline-primary btn-sm"><i class="fas fa-rotate"></i></button>
          </div>
        </div>

        <div class="card bg-white border-0 shadow chart-card">
          <div class="card-body">
            <h5 id="chart-title" class="mb-1">–</h5>
            <small id="chart-sub" class="text-muted d-block mb-2">–</small>
            <canvas id="bigChart" height="150"></canvas>

            <div class="d-flex flex-wrap gap-2 mt-3">
              <button id="btn-download-chart" class="btn btn-primary btn-sm">Download Chart</button>
              <button id="btn-download-data" class="btn btn-secondary btn-sm">Download Data (.xlsx)</button>
            </div>

            <hr class="my-3">
            <div class="row g-3">
              <div class="col-md-6">
                <div class="small text-muted">Satuan</div>
                <div id="unit-box" class="fw-semibold">–</div>
              </div>
              <div class="col-md-6">
                <div class="small text-muted">Deskripsi / Interpretasi</div>
                <div id="interpret-box" class="small">–</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- KANAN: PANEL INDIKATOR -->
      <div class="col-lg-4">
        <div class="side-panel rounded-3 overflow-hidden">
          <div class="bg-primary text-white p-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">INDIKATOR</h5>
            <select id="region-select" class="form-select form-select-sm w-auto bg-white text-dark"></select>
          </div>
          <div class="p-3 border-bottom bg-light">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
              <input id="panel-search" type="text" class="form-control" placeholder="Cari indikator / subindikator…">
            </div>
          </div>
          <div id="indicator-tree" class="panel-list p-2">
            <div class="text-muted small">Memuat…</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
  // Elemen UI
  let chart;
  const $region = document.getElementById('region-select');
  const $tree = document.getElementById('indicator-tree');
  const $search = document.getElementById('panel-search');

  const $win = document.getElementById('window-select');
  const $year = document.getElementById('year-select');
  const $q = document.getElementById('q-select');
  const $m = document.getElementById('m-select');
  const $title = document.getElementById('chart-title');
  const $sub = document.getElementById('chart-sub');
  const $unit = document.getElementById('unit-box');
  const $interp = document.getElementById('interpret-box');
  const $refresh = document.getElementById('btn-refresh');

  // State
  let regionId = null;
  let indicatorCache = []; // [{id,name,code,region_id}]
  let rowsCacheByInd = {}; // {indicatorId: [rows]}
  let current = {
    indicatorId: null,
    row: null
  }; // row = rowMeta terpilih
  let searchSeq = 0; // token anti-race untuk pencarian & region switch

  // Helpers
  async function j(u) {
    const r = await fetch(u, {
      cache: 'no-store'
    });
    return r.json();
  }

  function resetChart() {
    if (chart) {
      chart.destroy();
      chart = null;
    }
  }

  function shortLabel(s, max = 64) {
    s = String(s || '');
    return s.length > max ? (s.slice(0, max - 1) + '…') : s;
  }

  function setOptions(sel, items) {
    sel.innerHTML = '';
    items.forEach(v => {
      const o = document.createElement('option');
      o.value = v;
      o.textContent = v;
      sel.appendChild(o);
    });
  }

  // ------------- LOADERS -------------
  async function loadRegions() {
    const js = await j('<?= base_url('api/regions'); ?>');
    if (!js.ok) {
      $region.innerHTML = '<option>Gagal</option>';
      return;
    }
    $region.innerHTML = '';
    js.regions.forEach(r => {
      const o = document.createElement('option');
      o.value = r.id;
      o.textContent = r.name + (r.is_default ? ' (default)' : '');
      $region.appendChild(o);
      if (r.is_default && !regionId) regionId = r.id;
    });
    if (!regionId && js.regions.length) regionId = js.regions[0].id;
    if (regionId) $region.value = regionId;
  }

  async function loadIndicators() {
    const js = await j('<?= base_url('api/indicators'); ?>?region_id=' + regionId);
    indicatorCache = js.ok ? js.indicators : [];
  }

  async function ensureRows(indId) {
    if (rowsCacheByInd[indId]) return rowsCacheByInd[indId];
    const js = await j('<?= base_url('api/rows'); ?>?indicator_id=' + indId);
    rowsCacheByInd[indId] = js.ok ? js.rows : [];
    return rowsCacheByInd[indId];
  }

  // ------------- RENDER PANEL -------------
  function renderTree(filtered = null) {
    const items = filtered ?? indicatorCache;
    if (!items.length) {
      $tree.innerHTML = '<div class="text-muted small">Tidak ada indikator.</div>';
      return;
    }

    const wrap = document.createElement('div');
    wrap.className = 'tree-root';
    items.forEach(ind => {
      const indDiv = document.createElement('div');
      indDiv.className = 'ind-item';
      indDiv.dataset.id = ind.id;

      indDiv.innerHTML = `
        <div class="ind-header d-flex align-items-center">
          <button class="btn btn-sm btn-toggle me-2" aria-label="toggle"><i class="fa-solid fa-chevron-right"></i></button>
          <div class="ind-name flex-1">${ind.name}</div>
        </div>
        <div class="sub-list d-none"></div>
      `;
      // toggle
      indDiv.querySelector('.btn-toggle').onclick = async (e) => {
        e.stopPropagation();
        await toggleSub(indDiv, ind.id);
      };
      // klik nama indikator => juga toggle
      indDiv.querySelector('.ind-name').onclick = async () => {
        await toggleSub(indDiv, ind.id);
      };

      wrap.appendChild(indDiv);
    });
    $tree.innerHTML = '';
    $tree.appendChild(wrap);
  }

  async function toggleSub(indDiv, indId) {
    const btn = indDiv.querySelector('.btn-toggle i');
    const sub = indDiv.querySelector('.sub-list');
    const isOpen = !sub.classList.contains('d-none');

    if (isOpen) {
      sub.classList.add('d-none');
      btn.classList.replace('fa-chevron-down', 'fa-chevron-right');
      return;
    }
    // open: load rows
    const rows = await ensureRows(indId);
    sub.innerHTML = rows.length ? rows.map(r => `
      <div class="sub-item" data-row-id="${r.id}" title="${r.subindikator}">
        <i class="fa-regular fa-circle-dot me-2"></i>
        <span class="sub-text">${r.subindikator}</span>
        <span class="badge bg-light text-dark ms-2">${r.data_type}</span>
        <span class="badge bg-light text-dark ms-1">${r.timeline}</span>
      </div>
    `).join('') : '<div class="text-muted small px-2">Belum ada subindikator.</div>';

    sub.querySelectorAll('.sub-item').forEach(el => {
      el.onclick = () => {
        sub.querySelectorAll('.sub-item.active').forEach(x => x.classList.remove('active'));
        el.classList.add('active');
        const rid = +el.dataset.rowId;
        const rowMeta = rows.find(r => r.id === rid);
        current.indicatorId = indId;
        current.row = rowMeta;
        setupPeriodControls(rowMeta);
        draw();
      };
    });

    sub.classList.remove('d-none');
    btn.classList.replace('fa-chevron-right', 'fa-chevron-down');
  }

  // ------------- SEARCH -------------
  async function searchTree(q) {
    q = (q || '').trim().toLowerCase();
    
    // batalin proses search sebelumnya
    const mySeq = ++searchSeq;
    
    if (!q) {
      renderTree();
      return;
    }
    // Prefetch rows untuk SEMUA indikator aktif di region saat ini (paralel)
    // agar pencarian subindikator tidak meleset
    const ids = indicatorCache.map(ind => ind.id);
    await Promise.all(ids.map(id => ensureRows(id)));

    // Jika selama prefetch region berubah / pencarian baru masuk, hentikan hasil lama
    if (mySeq !== searchSeq) return;

    // filter indikator / subindikator yang cocok
    const out = [];
    indicatorCache.forEach(ind => {
      const matchInd = (ind.name || '').toLowerCase().includes(q);
      const rows = rowsCacheByInd[ind.id] || [];
      const subHit = rows.filter(r => (r.subindikator || '').toLowerCase().includes(q));
      if (matchInd || subHit.length) {
        out.push({
          ...ind,
          __hits: subHit
        });
      }
    });

    // Render hasil
    const wrap = document.createElement('div');
    if (!out.length) {
      wrap.innerHTML = `
      <div class="p-2">
        <div class="alert alert-warning py-2 px-3 mb-0 small">
          <i class="fa-regular fa-face-frown me-1"></i>
          Indikator/Subindikator yang Anda cari tidak ditemukan
        </div>
      </div>`;
      $tree.innerHTML = '';
      $tree.appendChild(wrap);
      return;
    }

    out.forEach(ind => {
      const indDiv = document.createElement('div');
      indDiv.className = 'ind-item';
      indDiv.dataset.id = ind.id;
      indDiv.innerHTML = `
      <div class="ind-header d-flex align-items-center">
        <button class="btn btn-sm btn-toggle me-2" aria-label="toggle"><i class="fa-solid fa-chevron-down"></i></button>
        <div class="ind-name flex-1">${highlight(ind.name,q)}</div>
      </div>
      <div class="sub-list">
        ${
          (ind.__hits && ind.__hits.length)
          ? ind.__hits.map(r=>`
              <div class="sub-item" data-row-id="${r.id}" title="${r.subindikator}">
                <i class="fa-regular fa-circle-dot me-2"></i>
                <span class="sub-text">${highlight(r.subindikator,q)}</span>
                <span class="badge bg-light text-dark ms-2">${r.data_type}</span>
                <span class="badge bg-light text-dark ms-1">${r.timeline}</span>
              </div>
            `).join('')
          : '<div class="text-muted small px-2">Tidak ada subindikator cocok.</div>'
        }
      </div>
    `;
      // Click handler sub saat hasil search
      indDiv.querySelectorAll('.sub-item').forEach(el => {
        el.onclick = () => {
          wrap.querySelectorAll('.sub-item.active').forEach(x => x.classList.remove('active'));
          el.classList.add('active');
          const rid = +el.dataset.rowId;
          const rows = rowsCacheByInd[ind.id] || [];
          const rowMeta = rows.find(r => r.id === rid);
          current.indicatorId = ind.id;
          current.row = rowMeta;
          setupPeriodControls(rowMeta);
          draw();
        };
      });
      wrap.appendChild(indDiv);
    });

    $tree.innerHTML = '';
    $tree.appendChild(wrap);
  }

  function highlight(text, q) {
    if (!q) return text;
    const esc = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    return String(text).replace(new RegExp('(' + esc + ')', 'ig'), '<mark>$1</mark>');
  }

  // ------------- PERIODE & DRAW -------------
  function setupPeriodControls(rowMeta) {
    // reset
    [$win, $year, $q, $m].forEach(el => el.classList.add('d-none'));
    if (!rowMeta) return;

    const t = rowMeta.timeline; // YEARLY|QUARTERLY|MONTHLY
    const dt = rowMeta.data_type; // TIMESERIES|JUMLAH_KATEGORI|PROPORSI
    if (dt === 'PROPORSI') {
      $year.classList.remove('d-none');
      if (t === 'QUARTERLY') $q.classList.remove('d-none');
      if (t === 'MONTHLY') $m.classList.remove('d-none');
    } else {
      if (t === 'YEARLY') $win.classList.remove('d-none');
      else $year.classList.remove('d-none');
    }
  }

  async function probeYearsForRow(rowId) {
    const js = await j('<?= base_url('api/series'); ?>?row_id=' + rowId + '&region_id=' + regionId + '&window=all');
    if (js.ok && (js.meta.timeline === 'YEAR' || js.meta.timeline === 'YEARLY')) {
      const ys = (js.labels || []).map(x => parseInt(x, 10)).filter(Boolean);
      if (ys.length) {
        setOptions($year, ys);
        $year.value = ys[ys.length - 1];
        return;
      }
    }
    const now = (new Date()).getFullYear();
    const ys = Array.from({
      length: 6
    }, (_, i) => now - 5 + i);
    setOptions($year, ys);
    $year.value = ys[ys.length - 1];
  }

  async function draw() {
    resetChart();
    const row = current.row;
    if (!row) return;

    // set year default bila perlu
    if (!$year.classList.contains('d-none') && !$year.value) {
      await probeYearsForRow(row.id);
    }

    // build URL
    let url = '',
      chartType = 'line',
      subTxt = '';
    if (row.data_type === 'PROPORSI') {
      const p = new URLSearchParams({
        row_id: row.id,
        region_id: regionId,
        year: $year.value || new Date().getFullYear()
      });
      if (row.timeline === 'QUARTERLY') p.set('quarter', $q.value || 1);
      if (row.timeline === 'MONTHLY') p.set('month', $m.value || 1);
      url = '<?= base_url('api/proportion'); ?>?' + p.toString();
      chartType = 'pie';
      subTxt = 'Data Proporsi (Pie Chart)';
    } else {
      const p = new URLSearchParams({
        row_id: row.id,
        region_id: regionId
      });
      if (row.timeline === 'YEARLY') p.set('window', $win.value || 'all');
      else p.set('year', $year.value || new Date().getFullYear());
      url = '<?= base_url('api/series'); ?>?' + p.toString();
      chartType = (row.data_type === 'JUMLAH_KATEGORI' ? 'bar' : 'line');
      subTxt = (chartType === 'bar' ? 'Data Jumlah Kategori (Bar Chart)' : 'Data Timeseries (Line Chart)');
    }

    const js = await j(url);
    if (!js.ok) {
      alert('Gagal memuat data');
      return;
    }

    // Judul & meta
    const unit = js.meta?.unit || '';
    $title.textContent = shortLabel(row.subindikator, 88);
    $sub.textContent = subTxt;
    $unit.textContent = unit || '-';
    $interp.textContent = js.meta?.interpretasi || (js.meta?.desc || '-');

    const ctx = document.getElementById('bigChart').getContext('2d');
    chart = new Chart(ctx, {
      type: chartType,
      data: {
        labels: js.labels,
        datasets: [{
          label: shortLabel(row.subindikator + (unit ? ` (${unit})` : ''), 60),
          data: js.values,
          borderWidth: 2,
          tension: .25,
          fill: (chartType === 'line')
        }]
      },
      options: {
        plugins: {
          legend: {
            position: 'bottom'
          },
          tooltip: {
            callbacks: {
              label: (c) => {
                const v = (typeof c.parsed === 'object') ? c.parsed.y : c.parsed;
                const lbl = c.dataset.label ? c.dataset.label + ': ' : '';
                return lbl + (v ?? '-') + (unit ? ` ${unit}` : '');
              }
            }
          }
        },
        scales: (chartType === 'pie') ? {} : {
          y: {
            beginAtZero: false,
            ticks: {
              callback: (v) => v + (unit ? ` ${unit}` : '')
            }
          }
        }
      }
    });

    // siapkan URL export sesuai tampilan sekarang
    chart.__meta = {
      title: row.subindikator,
      unit: unit,
      interpretasi: js.meta?.interpretasi || '',
      exportUrl: '<?= base_url('api/export/xlsx'); ?>?jenis=' + (row.data_type === 'PROPORSI' ? 'proportion' : 'series') + '&' + url.split('?')[1]
    };
  }

  // ------------- EVENTS -------------
  document.getElementById('btn-download-data').onclick = () => {
    if (chart?.__meta?.exportUrl) window.location.href = chart.__meta.exportUrl;
  };
  document.getElementById('btn-download-chart').onclick = () => {
    if (!chart) return;
    const canvas = chart.canvas,
      pad = 24,
      txtH = 16,
      blockH = pad * 2 + txtH * 3;
    const out = document.createElement('canvas');
    out.width = canvas.width;
    out.height = canvas.height + blockH;
    const g = out.getContext('2d');
    g.fillStyle = '#fff';
    g.fillRect(0, 0, out.width, out.height);
    g.fillStyle = '#111';
    g.font = 'bold 16px sans-serif';
    g.fillText(chart.__meta?.title || 'Chart', 16, 24);
    g.font = '12px sans-serif';
    g.fillStyle = '#444';
    g.fillText('Satuan: ' + (chart.__meta?.unit || '-'), 16, 24 + txtH + 4);
    const text = 'Interpretasi: ' + (chart.__meta?.interpretasi || '-'),
      maxW = out.width - 32;
    let line = '',
      y = 24 + txtH + 4 + txtH + 8;
    text.split(' ').forEach(w => {
      const t = line ? line + ' ' + w : w;
      if (g.measureText(t).width > maxW) {
        g.fillText(line, 16, y);
        y += txtH + 4;
        line = w;
      } else {
        line = t;
      }
    });
    if (line) g.fillText(line, 16, y);
    g.drawImage(canvas, 0, blockH);
    const a = document.createElement('a');
    a.href = out.toDataURL('image/png');
    a.download = 'chart_' + Date.now() + '.png';
    a.click();
  };

  [$win, $year, $q, $m].forEach(sel => sel.addEventListener('change', draw));
  $refresh.addEventListener('click', draw);

  $region.onchange = async () => {
    regionId = +$region.value;
    // Reset semua state terkait data & pencarian
    rowsCacheByInd = {};
    indicatorCache = [];
    current = {
      indicatorId: null,
      row: null
    };
    $search.value = '';
    searchSeq++; // batalkan pencarian yang mungkin masih berjalan

    // Tampilkan “Memuat…” agar UI terasa responsif
    $tree.innerHTML = '<div class="text-muted small p-2">Memuat…</div>';

    await loadIndicators();
    renderTree();
  };

  $search.addEventListener('input', () => {
    const q = $search.value;
    if (!q) {
      renderTree();
      return;
    }
    // untuk hasil lebih akurat, pastikan semua rows sudah prefetched sekali
    // (opsional) — di sini kita pakai yang sudah ada saja.
    searchTree(q);
  });

  // ------------- INIT -------------
  (async function init() {
    await loadRegions();
    await loadIndicators();
    renderTree();
  })();
</script>

<?= $this->endSection(); ?>