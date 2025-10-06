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
                <option value="yearly" <?= $tl === 'yearly'     ? 'selected' : '' ?>>Tahunan</option>
                <option value="quarterly" <?= $tl === 'quarterly'  ? 'selected' : '' ?>>Triwulan</option>
                <option value="monthly" <?= $tl === 'monthly'    ? 'selected' : '' ?>>Bulanan</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Bentuk Data</label>
            <?php $dt = $row['data_type'] ?? 'timeseries'; ?>
            <select class="form-select" name="data_type">
                <option value="timeseries" <?= $dt === 'timeseries'       ? 'selected' : '' ?>>Data Timeseries (linechart)</option>
                <option value="jumlah_kategori" <?= $dt === 'jumlah_kategori'  ? 'selected' : '' ?>>Data Jumlah Kategori (barchart)</option>
                <option value="proporsi" <?= $dt === 'proporsi'         ? 'selected' : '' ?>>Data Proporsi (piechart)</option>
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

            <!-- Panel Tahun -->
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

                <!-- Panel Variabel -->
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

            <!-- Tabel Data -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 mb-2 align-items-center">
                        <div class="me-auto"><strong>Data</strong></div>
                        <button class="btn btn-primary btn-sm" id="btnSaveAll">Save</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="gridTable">
                            <thead>
                                <tr id="gridHead">
                                    <th>Periode</th>
                                </tr>
                            </thead>
                            <tbody id="gridBody">
                                <tr>
                                    <td class="text-muted">Belum ada data/ tahun belum dipilih.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
<?php endif; ?>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<?php if (!empty($row)): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function() {
            const rowId = <?= (int)$row['id']; ?>;
            const regionId = <?= (int)$regionId; ?>;
            const dtype = "<?= esc($row['data_type']); ?>"; // timeseries | jumlah_kategori | proporsi

            let meta = null; // dari grid/fetch (kolom/vars/timeline)
            let selectedYears = []; // tahun yg dipilih
            let tableRows = []; // {period,year,quarter,month, values:{col=>val}}

            // DOM
            const yearPanel = document.getElementById('yearPanel');
            const yearRows = document.getElementById('yearRows');
            const chkYearAll = document.getElementById('chkYearAll');
            const btnAddYear = document.getElementById('btnAddYear');
            const btnDelYears = document.getElementById('btnDelYears');

            const varPanel = document.getElementById('varPanel');
            const varRows = document.getElementById('varRows');
            const chkVarAll = document.getElementById('chkVarAll');
            const newVar = document.getElementById('newVar');
            const btnAddVar = document.getElementById('btnAddVar');
            const btnSaveVarNames = document.getElementById('btnSaveVarNames');
            const btnDelVars = document.getElementById('btnDelVars');

            const gridHead = document.getElementById('gridHead');
            const gridBody = document.getElementById('gridBody');
            const btnSaveAll = document.getElementById('btnSaveAll');

            // ===== Helpers =====
            function uniq(arr) {
                return Array.from(new Set(arr));
            }

            function key(y, q, m) {
                return `${y}|${q||0}|${m||0}`;
            }

            function escapeHtml(s) {
                return String(s ?? '').replace(/[&<>"']/g, m => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                } [m]));
            }

            // terima angka desimal (titik/koma) dan minus; kosong = valid
            function isNumericText(s) {
                if (s === '' || s === null || typeof s === 'undefined') return true;
                s = String(s).trim();
                if (s === '') return true;
                return /^-?\d+(?:[.,]\d+)?$/.test(s);
            }

            function parseNumber(v) {
                if (v === '' || v === null || typeof v === 'undefined') return null;
                const num = Number(String(v).replace(',', '.'));
                return Number.isFinite(num) ? num : null;
            }

            function makeEmptyRowsForYear(y) {
                const out = [];
                if (meta.timeline === 'yearly') {
                    out.push({
                        period: String(y),
                        year: y,
                        quarter: 0,
                        month: 0,
                        values: {}
                    });
                } else if (meta.timeline === 'quarterly') {
                    [1, 2, 3, 4].forEach(q => out.push({
                        period: `${y} Q${q}`,
                        year: y,
                        quarter: q,
                        month: 0,
                        values: {}
                    }));
                } else {
                    for (let m = 1; m <= 12; m++) out.push({
                        period: `${y}-${String(m).padStart(2,'0')}`,
                        year: y,
                        quarter: 0,
                        month: m,
                        values: {}
                    });
                }
                return out;
            }

            function mergeServerRows(rows) {
                const map = new Map(tableRows.map(r => [key(r.year, r.quarter, r.month), r]));
                rows.forEach(r => {
                    const k = key(r.year, r.quarter, r.month);
                    const old = map.get(k) || {
                        period: r.period,
                        year: r.year,
                        quarter: r.quarter,
                        month: r.month,
                        values: {}
                    };
                    const vals = {
                        ...old.values
                    };
                    (meta.vars || []).forEach(v => {
                        vals[v.col] = (r[v.col] === undefined || r[v.col] === null) ? null : Number(r[v.col]);
                    });
                    map.set(k, {
                        ...old,
                        values: vals
                    });
                });
                tableRows = Array.from(map.values()).sort((a, b) => {
                    if (a.year !== b.year) return a.year - b.year;
                    if ((a.quarter || 0) !== (b.quarter || 0)) return (a.quarter || 0) - (b.quarter || 0);
                    return (a.month || 0) - (b.month || 0);
                });
            }

            function renderTable() {
                // Header
                gridHead.innerHTML = '<th style="width:160px">Periode</th>' + (meta.vars || []).map(v => `<th class="text-end">${escapeHtml(v.name)}</th>`).join('');
                // Body
                if (!tableRows.length) {
                    gridBody.innerHTML = `<tr><td colspan="${1+(meta.vars||[]).length}" class="text-center text-muted">Tidak ada data. Tambahkan tahun terlebih dahulu.</td></tr>`;
                    return;
                }
                let html = '';
                tableRows.forEach(r => {
                    html += `<tr data-y="${r.year}" data-q="${r.quarter||0}" data-m="${r.month||0}">
                <td>${escapeHtml(r.period)}</td>
                ${(meta.vars||[]).map(v=>`
                    <td class="text-end">
                        <input type="text" inputmode="decimal" autocomplete="off"
                            class="form-control form-control-sm text-end inp-cell"
                            data-col="${v.col}" value="${r.values[v.col]??''}" placeholder="-" />
                        <div class="invalid-feedback">Hanya angka (boleh desimal).</div>
                    </td>
                `).join('')}
            </tr>`;
                });
                gridBody.innerHTML = html;
            }

            // tampilkan error di semua sel & kembalikan daftar deskripsi error
            function validateAllCells() {
                const bad = [];
                document.querySelectorAll('#gridBody tr').forEach(tr => {
                    const period = tr.querySelector('td')?.textContent?.trim() ?? '';
                    tr.querySelectorAll('.inp-cell').forEach(inp => {
                        const ok = isNumericText(inp.value);
                        inp.classList.toggle('is-invalid', !ok);
                        if (!ok) {
                            const colKey = inp.dataset.col;
                            const v = (meta?.vars || []).find(x => x.col === colKey);
                            bad.push(`${period} → ${v ? v.name : colKey} = "${inp.value}"`);
                        }
                    });
                });
                return bad;
            }

            async function fetchMeta() {
                const y = new Date().getFullYear();
                const qs = new URLSearchParams({
                    region_id: regionId,
                    row_id: rowId,
                    year_from: y,
                    year_to: y
                });
                const res = await fetch(`<?= base_url('admin/data-indikator/grid/fetch'); ?>?` + qs);
                const js = await res.json();
                if (!js.ok) {
                    await Swal.fire('Gagal', 'Gagal memuat metadata', 'error');
                    return;
                }
                meta = js.meta || {};
                if (dtype !== 'timeseries') {
                    if (varPanel) varPanel.style.display = '';
                    renderVarPanel(await fetchVars());
                }
                if (yearPanel) yearPanel.style.display = 'none';
            }

            async function fetchVars() {
                const res = await fetch(`<?= base_url('admin/subindicator/var/list'); ?>/` + rowId);
                const js = await res.json();
                return js.ok ? (js.data || []) : [];
            }

            function renderVarPanel(vars) {
                if (!varPanel) return;
                varRows.innerHTML = (vars || []).map(v => `
            <tr data-id="${v.id}">
                <td><input type="checkbox" class="chkVar"></td>
                <td><input type="text" class="form-control form-control-sm inpVarName" value="${escapeHtml(v.name)}"></td>
                <td style="width:120px"><input type="number" class="form-control form-control-sm" value="${v.sort_order??0}" disabled></td>
            </tr>
        `).join('');
                if (chkVarAll) {
                    chkVarAll.checked = false;
                    chkVarAll.onchange = () => document.querySelectorAll('#varRows .chkVar').forEach(c => c.checked = chkVarAll.checked);
                }
            }

            function renderYearPanel() {
                if (!selectedYears.length) {
                    if (yearPanel) yearPanel.style.display = 'none';
                    yearRows.innerHTML = '';
                    return;
                }
                if (yearPanel) yearPanel.style.display = '';
                const sorted = [...selectedYears].sort((a, b) => a - b);
                yearRows.innerHTML = sorted.map(y => `
            <tr data-year="${y}">
                <td><input type="checkbox" class="chkYearRow"></td>
                <td>${y}</td>
                <td>${meta.timeline==='yearly'?'1x (Tahunan)': meta.timeline==='quarterly'?'Q1–Q4 (Triwulan)':'Jan–Des (Bulanan)'}</td>
            </tr>
        `).join('');
                if (chkYearAll) {
                    chkYearAll.checked = false;
                    chkYearAll.onclick = () => document.querySelectorAll('#yearRows .chkYearRow').forEach(c => c.checked = chkYearAll.checked);
                }
            }

            async function addYear(y) {
                if (selectedYears.includes(y)) {
                    Swal.fire('Info', 'Tahun tersebut sudah ditambahkan.', 'info');
                    return;
                }
                const qs = new URLSearchParams({
                    region_id: regionId,
                    row_id: rowId,
                    year_from: y,
                    year_to: y
                });
                const res = await fetch(`<?= base_url('admin/data-indikator/grid/fetch'); ?>?` + qs);
                const js = await res.json();
                if (!js.ok) {
                    Swal.fire('Gagal', 'Gagal memuat data tahun ' + y, 'error');
                    return;
                }
                const rows = (js.rows && js.rows.length) ? js.rows : makeEmptyRowsForYear(y);
                selectedYears = uniq([...selectedYears, y]);
                renderYearPanel();
                mergeServerRows(rows);
                renderTable();
            }

            // === Events ===
            btnAddYear && btnAddYear.addEventListener('click', async () => {
                const {
                    value: isr,
                    isConfirmed
                } = await Swal.fire({
                    title: 'Tambah Tahun',
                    input: 'number',
                    inputLabel: 'Masukkan tahun (misal 2025)',
                    inputAttributes: {
                        min: 1900,
                        step: 1
                    },
                    showCancelButton: true,
                    inputValidator: v => !v ? 'Wajib diisi' : (parseInt(v, 10) < 1900 ? 'Tidak valid' : null)
                });
                if (!isConfirmed) return;
                await addYear(parseInt(isr, 10));
            });

            btnDelYears && btnDelYears.addEventListener('click', async () => {
                const picked = Array.from(document.querySelectorAll('#yearRows .chkYearRow:checked')).map(chk => parseInt(chk.closest('tr').dataset.year, 10));
                if (!picked.length) {
                    Swal.fire('Info', 'Pilih tahun yang akan dihapus.', 'info');
                    return;
                }
                const ok = (await Swal.fire({
                    title: 'Hapus Data Tahun Terpilih?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus'
                })).isConfirmed;
                if (!ok) return;

                await fetch(`<?= base_url('admin/data-indikator/grid/delete-years'); ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        region_id: regionId,
                        row_id: rowId,
                        years: picked
                    })
                }).catch(() => {});

                selectedYears = selectedYears.filter(y => !picked.includes(y));
                tableRows = tableRows.filter(r => !picked.includes(r.year));
                renderYearPanel();
                renderTable();
                Swal.fire('Berhasil', 'Tahun terpilih dihapus.', 'success');
            });

            // Validasi realtime di setiap cell
            gridBody.addEventListener('input', (e) => {
                const t = e.target;
                if (t && t.classList && t.classList.contains('inp-cell')) {
                    const ok = isNumericText(t.value);
                    t.classList.toggle('is-invalid', !ok);
                }
            });

            // Variabel CRUD
            btnAddVar && btnAddVar.addEventListener('click', async () => {
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
                const js = await res.json().catch(() => ({
                    ok: true
                }));
                if (js && js.ok === false) {
                    Swal.fire('Gagal', js.error || 'Gagal menambah variabel', 'error');
                    return;
                }
                newVar.value = '';
                renderVarPanel(await fetchVars());
                await fetchMeta();
                renderTable();
                Swal.fire('Berhasil', 'Variabel ditambahkan.', 'success');
            });

            btnSaveVarNames && btnSaveVarNames.addEventListener('click', async () => {
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
                    Swal.fire('Info', 'Tidak ada perubahan.', 'info');
                    return;
                }
                const ok = (await Swal.fire({
                    title: 'Simpan perubahan nama variabel?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Simpan'
                })).isConfirmed;
                if (!ok) return;
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
                renderVarPanel(await fetchVars());
                await fetchMeta();
                renderTable();
                Swal.fire('Berhasil', 'Perubahan nama tersimpan.', 'success');
            });

            btnDelVars && btnDelVars.addEventListener('click', async () => {
                const ids = Array.from(document.querySelectorAll('#varRows .chkVar:checked')).map(chk => parseInt(chk.closest('tr').dataset.id, 10));
                if (!ids.length) {
                    Swal.fire('Info', 'Pilih variabel yang akan dihapus.', 'info');
                    return;
                }
                const ok = (await Swal.fire({
                    title: 'Hapus variabel terpilih?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus'
                })).isConfirmed;
                if (!ok) return;
                const form = new FormData();
                ids.forEach(id => form.append('ids[]', id));
                const res = await fetch(`<?= base_url('admin/subindicator/var/delete-bulk'); ?>`, {
                    method: 'POST',
                    body: form
                });
                const js = await res.json();
                if (!js.ok) {
                    Swal.fire('Gagal', js.error || 'Gagal menghapus variabel', 'error');
                    return;
                }
                renderVarPanel(await fetchVars());
                await fetchMeta();
                renderTable();
                Swal.fire('Berhasil', 'Variabel terhapus.', 'success');
            });

            // Save
            btnSaveAll && btnSaveAll.addEventListener('click', async () => {
                // Validasi semua sel — hentikan bila ada non-angka
                const errors = validateAllCells();
                if (errors.length) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Input tidak valid',
                        html: 'Perbaiki nilai berikut (hanya angka diperbolehkan):<br><pre style="text-align:left;white-space:pre-wrap;">' +
                            errors.slice(0, 15).join('\n') +
                            (errors.length > 15 ? '\n…' : '') +
                            '</pre>'
                    });
                    return;
                }

                // sinkronkan nilai input ke tableRows
                document.querySelectorAll('#gridBody tr').forEach(tr => {
                    const y = parseInt(tr.dataset.y, 10),
                        q = parseInt(tr.dataset.q, 10),
                        m = parseInt(tr.dataset.m, 10);
                    const r = tableRows.find(x => x.year === y && (x.quarter || 0) === (q || 0) && (x.month || 0) === (m || 0));
                    if (!r) return;
                    tr.querySelectorAll('.inp-cell').forEach(inp => {
                        r.values[inp.dataset.col] = parseNumber(inp.value);
                    });
                });

                // build entries
                const entries = [];
                tableRows.forEach(r => {
                    (meta.vars || []).forEach(v => {
                        entries.push({
                            year: r.year,
                            quarter: r.quarter || 0,
                            month: r.month || 0,
                            var_id: v.col === 'val__single' ? '' : (parseInt(v.col.replace('val__', ''), 10) || null),
                            value: (r.values[v.col] === null || r.values[v.col] === '') ? null : Number(r.values[v.col])
                        });
                    });
                });

                const res = await fetch(`<?= base_url('admin/data-indikator/grid/save'); ?>`, {
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
                const js = await res.json().catch(() => ({
                    ok: false,
                    error: 'Gagal parsing respons'
                }));
                if (!js.ok) {
                    Swal.fire('Gagal', js.error || 'Validasi server gagal.', 'error');
                    return;
                }
                Swal.fire('Tersimpan', 'Perubahan data berhasil disimpan.', 'success');
            });

            // Init
            (async function init() {
                await fetchMeta();
                // tampilkan semua tahun yang sudah ada data
                const yearsRes = await fetch(`<?= base_url('admin/data-indikator/grid/years'); ?>?` + new URLSearchParams({
                    region_id: regionId,
                    row_id: rowId
                }));
                const yearsJs = await yearsRes.json();
                const years = yearsJs.ok ? (yearsJs.years || []) : [];
                if (years.length) {
                    for (const y of years) await addYear(y);
                }
            })();

        })();
    </script>
<?php endif; ?>
<?= $this->endSection(); ?>