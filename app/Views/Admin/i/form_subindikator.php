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
            <select class="form-select" name="timeline">
                <?php
                $tl = $row['timeline'] ?? 'yearly';
                $map = ['yearly' => 'Tahunan', 'quarterly' => 'Triwulan', 'monthly' => 'Bulanan'];
                ?>
                <?php foreach ($map as $k => $v): ?>
                    <option <?= $tl === $k ? 'selected' : ''; ?>><?= $v; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Bentuk Data</label>
            <?php $dt = $row['data_type'] ?? 'single'; ?>
            <select class="form-select" name="data_type">
                <option <?= $dt === 'single' ? 'selected' : ''; ?>>Data Biasa</option>
                <option <?= $dt === 'proporsi' ? 'selected' : ''; ?>>Data Proporsi</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Unit (opsional)</label>
            <input class="form-control" name="unit" value="<?= esc($row['unit'] ?? '') ?>">
        </div>
    </div>

    <div class="d-flex gap-2 mt-3">
        <button class="btn btn-primary">Save</button>
        <a class="btn btn-light" href="<?= base_url('admin/indicators'); ?>">Cancel</a>
    </div>
</form>

<?php if (!empty($row)): ?>
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 mb-2">
                <div class="me-auto">
                    <span class="badge bg-secondary">Region default ID: <?= (int)$regionDefaultId; ?></span>
                </div>
                <?php if ($row['data_type'] === 'proporsi'): ?>
                    <input type="text" id="newVar" class="form-control form-control-sm" placeholder="Nama variabel baru" style="width:220px">
                    <button class="btn btn-outline-primary btn-sm" id="btnAddVar">Tambah Variabel</button>
                    <button class="btn btn-outline-danger btn-sm" id="btnDelVar">Hapus Variabel (terpilih)</button>
                <?php endif; ?>
                <button class="btn btn-outline-primary btn-sm" id="btnAddYear">Tambah Tahun</button>
                <button class="btn btn-outline-danger btn-sm" id="btnDelYear">Hapus Tahun (terakhir)</button>
            </div>

            <div id="grid"></div>

        </div>
    </div>
<?php endif; ?>

<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<?php if (!empty($row)): ?>
    <link href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator.min.css" rel="stylesheet">
    <script src="https://unpkg.com/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>
    <script>
        (async function() {
            const rowId = <?= (int)$row['id']; ?>;
            const regionId = <?= (int)$regionDefaultId; ?>; // bisa diganti pilihan region kalau mau
            const timeline = "<?= esc($row['timeline']); ?>"; // yearly/quarterly/monthly
            const dtype = "<?= esc($row['data_type']); ?>"; // single/proporsi

            let meta = null;
            let grid = null;
            let vars = <?= json_encode($vars ?? []); ?>;
            let data = [];
            let yearsRange = {
                from: (new Date().getFullYear() - 3),
                to: (new Date().getFullYear())
            };

            async function fetchGrid() {
                const qs = new URLSearchParams({
                    region_id: regionId,
                    row_id: rowId,
                    year_from: yearsRange.from,
                    year_to: yearsRange.to
                });
                const res = await fetch(`<?= base_url('admin/data-indikator/grid/fetch'); ?>?` + qs.toString());
                const json = await res.json();
                if (!json.ok) {
                    alert('Gagal load');
                    return;
                }
                meta = json.meta;
                data = json.rows;
                buildGrid();
            }

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
                (meta.vars || []).forEach(v => {
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

            let pending = [];
            let timer = null;

            function queueSave(entry) {
                pending.push(entry);
                if (timer) clearTimeout(timer);
                timer = setTimeout(flush, 600);
            }

            async function flush() {
                if (!pending.length) return;
                const entries = pending.splice(0, pending.length);
                const payload = {
                    region_id: regionId,
                    row_id: rowId,
                    entries
                };
                await fetch(`<?= base_url('admin/data-indikator/grid/save'); ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
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
                    year: parseInt(row.year || 0, 10),
                    quarter: parseInt(row.quarter || 0, 10),
                    month: parseInt(row.month || 0, 10),
                    var_id: var_id,
                    value: isNaN(val) ? null : val
                });
            }

            function buildGrid() {
                if (grid) {
                    grid.destroy();
                    grid = null;
                }
                grid = new Tabulator('#grid', {
                    data,
                    columns: buildColumns(),
                    layout: 'fitColumns',
                    reactiveData: true,
                    height: '560px',
                    clipboard: true,
                    history: true,
                });
            }

            // Tahun +/- (ikut aturan timeline)
            document.getElementById('btnAddYear').addEventListener('click', async () => {
                yearsRange.to += 1;
                await fetchGrid();
            });
            document.getElementById('btnDelYear').addEventListener('click', async () => {
                yearsRange.to = Math.max(yearsRange.from, yearsRange.to - 1);
                await fetchGrid();
            });

            // Variabel (Proporsi)
            <?php if (($row['data_type'] ?? '') === 'proporsi'): ?>
                const btnAddVar = document.getElementById('btnAddVar');
                const btnDelVar = document.getElementById('btnDelVar');
                const newVar = document.getElementById('newVar');

                btnAddVar.addEventListener('click', async () => {
                    const nm = (newVar.value || '').trim();
                    if (!nm) return;
                    const body = new URLSearchParams({
                        row_id: rowId,
                        name: nm
                    });
                    await fetch(`<?= base_url('admin/subindicator/var/create'); ?>`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body
                    });
                    newVar.value = '';
                    // refresh grid columns
                    await fetchGrid();
                });

                btnDelVar.addEventListener('click', async () => {
                    const col = grid.getColumnFromPosition(4); // setelah hidden fields, kol data mulai index 4
                    // agar sederhana: minta pengguna klik header kolom yang mau dihapus
                    const selected = grid.getColumns().find(c => c.isHeaderVisible() && c.getDefinition().title && c.headerElement?.classList.contains('tabulator-selected'));
                    const pick = selected || grid.getColumns().slice(-1)[0];
                    const field = pick.getField();
                    const m = /^val__([0-9]+)$/.exec(field);
                    if (!m) {
                        alert('Pilih kolom variabel dahulu');
                        return;
                    }
                    const varId = parseInt(m[1], 10);
                    if (!confirm('Hapus variabel terpilih beserta nilainya?')) return;
                    await fetch(`<?= base_url('admin/subindicator/var/delete'); ?>/` + varId, {
                        method: 'POST'
                    });
                    await fetchGrid();
                });
            <?php endif; ?>

            await fetchGrid();
        })();
    </script>
<?php endif; ?>
<?= $this->endSection(); ?>