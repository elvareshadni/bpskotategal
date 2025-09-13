<?= $this->extend('Admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h5 class="mb-3">Form Subindikator</h5>

<form method="post" action="<?= base_url('admin/subindicator/save'); ?>" class="card p-3 mb-3">
    <?= csrf_field(); ?>
    <input type="hidden" name="id" value="<?= $row['id'] ?? '' ?>">
    <input type="hidden" name="indicator_id" value="<?= $indicator_id ?>">

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nama Subindikator</label>
            <input class="form-control" name="subindikator" required value="<?= esc($row['subindikator'] ?? '') ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label">Timeline</label>
            <?php $tl = $row['timeline'] ?? 'yearly'; ?>
            <select class="form-select" name="timeline">
                <option value="yearly" <?= $tl === 'yearly' ? 'selected' : '' ?>>Tahunan</option>
                <option value="quarterly" <?= $tl === 'quarterly' ? 'selected' : '' ?>>Triwulan</option>
                <option value="monthly" <?= $tl === 'monthly' ? 'selected' : '' ?>>Bulanan</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Bentuk Data</label>
            <?php $dt = $row['data_type'] ?? 'timeseries'; ?>
            <select class="form-select" name="data_type">
                <option value="timeseries" <?= $dt === 'timeseries' ? 'selected' : '' ?>>Data Timeseries (linechart)</option>
                <option value="jumlah_kategori" <?= $dt === 'jumlah_kategori' ? 'selected' : '' ?>>Data Jumlah Kategori (barchart)</option>
                <option value="proporsi" <?= $dt === 'proporsi' ? 'selected' : '' ?>>Data Proporsi (piechart)</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Satuan (opsional)</label>
            <input class="form-control" name="unit" value="<?= esc($row['unit'] ?? '') ?>">
        </div>

        <div class="col-12">
            <label class="form-label">Interpretasi (opsional)</label>
            <textarea class="form-control" name="interpretasi" rows="2" placeholder="Contoh: Semakin tinggi semakin baik / dll."><?= esc($row['interpretasi'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="d-flex gap-2 mt-3">
        <button class="btn btn-primary">Save</button>
        <a class="btn btn-light" href="<?= base_url('admin/indicators'); ?>">Cancel</a>
    </div>
</form>

<?php if (!empty($row)): ?>
    <div class="card mb-3">
        <div class="card-body">

            <!-- Toolbar atas -->
            <div class="d-flex flex-wrap gap-2 mb-2 align-items-center">
                <div class="me-auto">
                    <span class="badge bg-secondary">Kode BPS: <?= esc($regionCode ?: '-'); ?></span>
                </div>
                <div class="d-flex flex-wrap gap-2 ms-auto">
                    <button class="btn btn-outline-primary btn-sm" id="btnAddYear">Tambah Tahun</button>
                    <button class="btn btn-outline-danger btn-sm" id="btnDelYears">Hapus Tahun (terpilih)</button>
                </div>
            </div>

            <!-- Panel Tahun (selalu ada) -->
            <div class="mb-2" id="yearPanel" style="display:none">
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th style="width:40px"><input type="checkbox" id="chkYearAll"></th>
                                <th style="width:120px">Tahun</th>
                                <th>Periode (sesuai timeline)</th>
                            </tr>
                        </thead>
                        <tbody id="yearRows"></tbody>
                    </table>
                </div>
            </div>

            <?php if (($row['data_type'] ?? '') === 'jumlah_kategori' || ($row['data_type'] ?? '') === 'proporsi'): ?>
                <!-- Toolbar Variabel -->
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <input type="text" id="newVar" class="form-control form-control-sm" placeholder="Nama variabel baru" style="width:220px">
                    <button class="btn btn-outline-primary btn-sm" id="btnAddVar">Tambah Variabel</button>
                    <button class="btn btn-outline-success btn-sm" id="btnSaveVarNames">Simpan Perubahan Nama Variabel</button>
                    <button class="btn btn-outline-danger btn-sm" id="btnDelVars">Hapus Variabel (terpilih)</button>
                </div>

                <!-- Panel Variabel (wrap table DI DALAM varPanel) -->
                <div class="mb-2" id="varPanel" style="display:none">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th style="width:40px"><input type="checkbox" id="chkVarAll"></th>
                                    <th>Nama Variabel (klik untuk edit)</th>
                                    <th style="width:120px">Urutan</th>
                                </tr>
                            </thead>
                            <tbody id="varRows"></tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Grid -->
            <div id="grid"></div>

        </div><!-- /.card-body -->
    </div><!-- /.card -->
<?php endif; ?>


<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<?php if (!empty($row)): ?>
    <link href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator.min.css" rel="stylesheet">
    <script src="https://unpkg.com/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function() {
            const rowId = <?= (int)$row['id']; ?>;
            const regionId = <?= (int)$regionId; ?>;
            const dtype = "<?= esc($row['data_type']); ?>"; // timeseries | jumlah_kategori | proporsi

            // --- State ---
            let meta = null; // meta kolom/vars/timeline dari server
            let grid = null; // Tabulator instance
            let data = []; // rows yang tampil di grid (hanya tahun yang dipilih admin)
            let selectedYears = []; // daftar tahun yang dipilih admin (manual)

            // --- Elemen DOM ---
            const yearPanel = document.getElementById('yearPanel');
            const yearRows = document.getElementById('yearRows');
            const chkYearAll = document.getElementById('chkYearAll');
            const btnAddYear = document.getElementById('btnAddYear');
            const btnDelYears = document.getElementById('btnDelYears');

            const newVar = document.getElementById('newVar');
            const btnAddVar = document.getElementById('btnAddVar');
            const btnSaveVarNames = document.getElementById('btnSaveVarNames');
            const btnDelVars = document.getElementById('btnDelVars');
            const varPanel = document.getElementById('varPanel');
            const varRows = document.getElementById('varRows');
            const chkVarAll = document.getElementById('chkVarAll');

            // ====== Utils ======
            function keyOf(y, q, m) {
                return `${y}|${q||0}|${m||0}`;
            }

            function sortRows(a, b) {
                if (a.year !== b.year) return a.year - b.year;
                if ((a.quarter || 0) !== (b.quarter || 0)) return (a.quarter || 0) - (b.quarter || 0);
                return (a.month || 0) - (b.month || 0);
            }

            function uniq(arr) {
                return Array.from(new Set(arr));
            }

            function toInt(v) {
                const n = parseInt(v, 10);
                return Number.isFinite(n) ? n : null;
            }

            // ====== Ambil META satu kali (pakai tahun sekarang hanya untuk ambil struktur) ======
            async function fetchMetaOnce() {
                const y = new Date().getFullYear();
                const qs = new URLSearchParams({
                    region_id: regionId,
                    row_id: rowId,
                    year_from: y,
                    year_to: y
                });
                const res = await fetch(`<?= base_url('admin/data-indikator/grid/fetch'); ?>?` + qs.toString());
                const json = await res.json();
                if (!json.ok) {
                    Swal.fire('Gagal', 'Gagal memuat metadata', 'error');
                    return;
                }
                meta = json.meta || {};
                // siapkan grid kosong (tanpa rows)
                buildGrid([]);
                // kalau tipe pakai variabel, tampilkan panel variabel
                if (dtype !== 'timeseries') {
                    const v = await fetchVars();
                    renderVarPanel(v);
                    varPanel && (varPanel.style.display = '');
                }
                // panel tahun tetap hidden sampai ada pilihan
                yearPanel && (yearPanel.style.display = 'none');
            }

            // ====== Ambil data VAR (untuk jumlah_kategori/proporsi) ======
            async function fetchVars() {
                const res = await fetch(`<?= base_url('admin/subindicator/var/list'); ?>/` + rowId);
                const js = await res.json();
                return js.ok ? (js.data || []) : [];
            }

            // ====== Build Grid ======
            function buildColumns() {
                const cols = [{
                        title: 'Periode',
                        field: 'period',
                        frozen: true,
                        width: 140,
                        headerSort: false
                    },
                    {
                        title: '_year',
                        field: 'year',
                        visible: false
                    },
                    {
                        title: '_q',
                        field: 'quarter',
                        visible: false
                    },
                    {
                        title: '_m',
                        field: 'month',
                        visible: false
                    },
                ];
                (meta?.vars || []).forEach(v => {
                    cols.push({
                        title: v.name,
                        field: v.col,
                        editor: 'number',
                        validator: ['numeric'],
                        hozAlign: 'right',
                        headerHozAlign: 'right',
                        headerSort: false,
                        cellEdited: onCellEdited,
                    });
                });
                return cols;
            }

            function buildGrid(initialData) {
                if (grid) {
                    grid.destroy();
                    grid = null;
                }
                grid = new Tabulator('#grid', {
                    data: initialData || [],
                    columns: buildColumns(),
                    layout: 'fitColumns',
                    reactiveData: true,
                    height: '560px',
                    clipboard: true,
                    history: true,
                });
                data = initialData || [];
            }

            // ====== Simpan sel yang diedit (debounced) ======
            let pending = [];
            let timer = null;

            function queueSave(entry) {
                pending.push(entry);
                if (timer) clearTimeout(timer);
                timer = setTimeout(flush, 500);
            }
            async function flush() {
                if (!pending.length) return;
                const entries = pending.splice(0, pending.length);
                await fetch(`<?= base_url('admin/data-indikator/grid/save'); ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        region_id: regionId,
                        row_id: rowId,
                        entries
                    })
                });
            }

            function parseVarId(field) {
                if (field === 'val__single') return '';
                const m = /^val__([0-9]+)$/.exec(field);
                return m ? parseInt(m[1], 10) : '';
            }

            function onCellEdited(cell) {
                const row = cell.getRow().getData();
                const field = cell.getColumn().getField();
                const var_id = parseVarId(field);
                let val = cell.getValue();
                if (val === '' || val === null || typeof val === 'undefined') val = null;
                else val = Number(String(val).replace(',', '.'));
                queueSave({
                    year: toInt(row.year) || 0,
                    quarter: toInt(row.quarter) || 0,
                    month: toInt(row.month) || 0,
                    var_id: var_id,
                    value: Number.isFinite(val) ? val : null
                });
            }

            // ====== Panel Tahun (mirip Variabel) ======
            function timelineLabel() {
                if (!meta || !meta.timeline) return '-';
                if (meta.timeline === 'yearly') return '1x (Tahunan)';
                if (meta.timeline === 'quarterly') return 'Q1–Q4 (Triwulan)';
                return 'Jan–Des (Bulanan)';
            }

            function renderYearPanel() {
                if (!selectedYears.length) {
                    yearPanel && (yearPanel.style.display = 'none');
                    yearRows.innerHTML = '';
                    return;
                }
                yearPanel && (yearPanel.style.display = '');
                const years = [...selectedYears].sort((a, b) => a - b);
                yearRows.innerHTML = years.map(y => `
      <tr data-year="${y}">
        <td><input type="checkbox" class="chkYearRow"></td>
        <td>${y}</td>
        <td>${timelineLabel()}</td>
      </tr>
    `).join('');
                if (chkYearAll) {
                    chkYearAll.checked = false;
                    chkYearAll.onclick = () => {
                        document.querySelectorAll('#yearRows .chkYearRow').forEach(c => c.checked = chkYearAll.checked);
                    };
                }
            }

            // Buat rows kosong sesuai timeline (kalau server tidak punya data)
            function makePeriodRowsForYear(y) {
                const rows = [];
                if (meta.timeline === 'yearly') {
                    rows.push({
                        period: String(y),
                        year: y,
                        quarter: 0,
                        month: 0
                    });
                } else if (meta.timeline === 'quarterly') {
                    [1, 2, 3, 4].forEach(q => rows.push({
                        period: `${y} Q${q}`,
                        year: y,
                        quarter: q,
                        month: 0
                    }));
                } else {
                    for (let m = 1; m <= 12; m++) rows.push({
                        period: `${y}-${String(m).padStart(2,'0')}`,
                        year: y,
                        quarter: 0,
                        month: m
                    });
                }
                // tambah kolom nilai null
                (meta?.vars || []).forEach(v => {
                    rows.forEach(r => r[v.col] = null);
                });
                return rows;
            }

            // Merge rows baru ke data dengan de-dup per (y,q,m)
            function mergeRows(newRows) {
                const map = new Map(data.map(r => [keyOf(r.year, r.quarter, r.month), r]));
                newRows.forEach(nr => {
                    map.set(keyOf(nr.year, nr.quarter, nr.month), {
                        ...map.get(keyOf(nr.year, nr.quarter, nr.month)),
                        ...nr
                    });
                });
                data = Array.from(map.values()).sort(sortRows);
                grid.setData(data);
            }

            // Ambil data server untuk 1 tahun (jika ada), kalau kosong pakai rows null
            async function addYear(y) {
                // sudah ada?
                if (selectedYears.includes(y)) {
                    Swal.fire('Info', 'Tahun tersebut sudah ditambahkan.', 'info');
                    return;
                }
                // fetch 1 tahun dari server
                const qs = new URLSearchParams({
                    region_id: regionId,
                    row_id: rowId,
                    year_from: y,
                    year_to: y
                });
                const res = await fetch(`<?= base_url('admin/data-indikator/grid/fetch'); ?>?` + qs.toString());
                const json = await res.json();
                if (!json.ok) {
                    Swal.fire('Gagal', 'Gagal memuat data tahun ' + y, 'error');
                    return;
                }
                // meta mungkin sama; pakai yang sudah ada
                const rows = (json.rows && json.rows.length) ? json.rows : makePeriodRowsForYear(y);
                selectedYears = uniq([...selectedYears, y]);
                renderYearPanel();
                mergeRows(rows);
            }

            // ====== Events Tahun ======
            if (btnAddYear) {
                btnAddYear.addEventListener('click', async () => {
                    const {
                        value: yearStr,
                        isConfirmed
                    } = await Swal.fire({
                        title: 'Tambah Tahun',
                        input: 'number',
                        inputLabel: 'Masukkan tahun (contoh: 2025)',
                        inputAttributes: {
                            min: 1900,
                            step: 1
                        },
                        confirmButtonText: 'Tambah',
                        showCancelButton: true,
                        inputValidator: (val) => {
                            if (!val) return 'Tahun wajib diisi';
                            const y = parseInt(val, 10);
                            if (!Number.isFinite(y) || y < 1900 || y > 9999) return 'Tahun tidak valid';
                            return null;
                        }
                    });
                    if (!isConfirmed) return;
                    const y = parseInt(yearStr, 10);
                    await addYear(y);
                });
            }

            if (btnDelYears) {
                btnDelYears.addEventListener('click', async () => {
                    const picked = Array.from(document.querySelectorAll('#yearRows .chkYearRow:checked'))
                        .map(chk => parseInt(chk.closest('tr').dataset.year, 10));
                    if (!picked.length) {
                        Swal.fire('Info', 'Silakan pilih tahun yang akan dihapus.', 'info');
                        return;
                    }
                    const {
                        isConfirmed
                    } = await Swal.fire({
                        title: 'Hapus Data Tahun Terpilih?',
                        text: 'Semua nilai pada tahun tersebut (sesuai timeline) akan dihapus dari database.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    });
                    if (!isConfirmed) return;

                    // Hapus di DB (kalau memang ada data)
                    const res = await fetch(`<?= base_url('admin/data-indikator/grid/delete-years'); ?>`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            region_id: regionId,
                            row_id: rowId,
                            years: picked
                        })
                    });
                    const json = await res.json().catch(() => ({
                        ok: true
                    })); // kalau tidak ada data pun ok kita lanjutkan

                    if (json && json.ok === false) {
                        Swal.fire('Gagal', json.error || 'Gagal menghapus', 'error');
                        return;
                    }

                    // Hapus dari state & grid
                    selectedYears = selectedYears.filter(y => !picked.includes(y));
                    renderYearPanel();

                    const delKey = new Set(picked.map(y => `${y}|`)); // awalan cocok (y|quarter|month)
                    data = data.filter(r => !picked.includes(parseInt(r.year, 10)));
                    grid.setData(data);

                    Swal.fire('Berhasil', 'Tahun terpilih dihapus.', 'success');
                });
            }

            // ====== Panel Variabel ======
            function escapeHtml(s) {
                return String(s ?? '')
                    .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
            }

            function renderVarPanel(vars) {
                if (!varPanel) return;
                varPanel.style.display = '';
                varRows.innerHTML = (vars || []).map(v => `
      <tr data-id="${v.id}">
        <td><input type="checkbox" class="chkVar"></td>
        <td><input type="text" class="form-control form-control-sm inpVarName" value="${escapeHtml(v.name)}"></td>
        <td><input type="number" class="form-control form-control-sm inpVarOrder" value="${v.sort_order ?? 0}" disabled></td>
      </tr>
    `).join('');

                if (chkVarAll) {
                    chkVarAll.checked = false;
                    chkVarAll.addEventListener('change', () => {
                        document.querySelectorAll('#varRows .chkVar').forEach(c => c.checked = chkVarAll.checked);
                    });
                }
            }

            if (btnAddVar) {
                btnAddVar.addEventListener('click', async () => {
                    const nm = (newVar.value || '').trim();
                    if (!nm) return;
                    const body = new URLSearchParams({
                        row_id: rowId,
                        name: nm
                    });
                    const res = await fetch(`<?= base_url('admin/subindicator/var/create'); ?>`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body
                    });
                    const json = await res.json().catch(() => ({
                        ok: true
                    }));
                    if (json && json.ok === false) {
                        Swal.fire('Gagal', json.error || 'Gagal menambah variabel', 'error');
                        return;
                    }
                    newVar.value = '';
                    const v = await fetchVars();
                    renderVarPanel(v);
                    Swal.fire('Berhasil', 'Variabel ditambahkan.', 'success');
                });
            }

            if (btnSaveVarNames) {
                btnSaveVarNames.addEventListener('click', async () => {
                    const domRows = Array.from(document.querySelectorAll('#varRows tr'));
                    const current = await fetchVars();
                    const curMap = Object.fromEntries(current.map(x => [String(x.id), x.name]));
                    const updates = [];
                    domRows.forEach(tr => {
                        const id = tr.dataset.id;
                        const val = (tr.querySelector('.inpVarName').value || '').trim();
                        if (val && val !== (curMap[id] || '')) updates.push({
                            id,
                            name: val
                        });
                    });
                    if (!updates.length) {
                        Swal.fire('Info', 'Tidak ada perubahan nama variabel.', 'info');
                        return;
                    }
                    const {
                        isConfirmed
                    } = await Swal.fire({
                        title: 'Simpan perubahan nama variabel?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal'
                    });
                    if (!isConfirmed) return;

                    for (const u of updates) {
                        const body = new URLSearchParams({
                            name: u.name
                        });
                        await fetch(`<?= base_url('admin/subindicator/var/update'); ?>/${u.id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body
                        });
                    }
                    const v = await fetchVars();
                    renderVarPanel(v);
                    Swal.fire('Berhasil', 'Perubahan nama tersimpan.', 'success');
                });
            }

            if (btnDelVars) {
                btnDelVars.addEventListener('click', async () => {
                    const ids = Array.from(document.querySelectorAll('#varRows .chkVar:checked'))
                        .map(chk => parseInt(chk.closest('tr').dataset.id, 10));
                    if (!ids.length) {
                        Swal.fire('Info', 'Pilih variabel yang akan dihapus.', 'info');
                        return;
                    }
                    const {
                        isConfirmed
                    } = await Swal.fire({
                        title: 'Hapus variabel terpilih?',
                        text: 'Semua nilai terkait variabel juga akan ikut terhapus.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    });
                    if (!isConfirmed) return;

                    const form = new FormData();
                    ids.forEach(id => form.append('ids[]', id));
                    const res = await fetch(`<?= base_url('admin/subindicator/var/delete-bulk'); ?>`, {
                        method: 'POST',
                        body: form
                    });
                    const json = await res.json();
                    if (!json.ok) {
                        Swal.fire('Gagal', json.error || 'Gagal menghapus variabel', 'error');
                        return;
                    }

                    const v = await fetchVars();
                    renderVarPanel(v);
                    Swal.fire('Berhasil', 'Variabel terpilih dihapus.', 'success');
                });
            }

            // ====== Init: ambil meta saja, grid kosong ======
            (async function init() {
                await fetchMetaOnce();
                // grid tetap kosong sampai admin menambah tahun secara manual
            })();

        })();
    </script>


<?php endif; ?>
<?= $this->endSection(); ?>