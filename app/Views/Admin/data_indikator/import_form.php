<?= $this->extend('Admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h5 class="mb-3">Import Data Indikator</h5>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-3" method="post" action="<?= base_url('admin/data-indikator/import'); ?>" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            <div class="col-md-4">
                <label class="form-label">Region</label>
                <select class="form-select" name="region_id" required>
                    <?php foreach ((new \App\Models\RegionModel())->orderBy('name', 'ASC')->findAll() as $r): ?>
                        <option value="<?= $r['id']; ?>" <?= (int)($region_id ?? 0) === (int)$r['id'] ? 'selected' : ''; ?>>
                            <?= esc($r['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Indikator</label>
                <select class="form-select" name="indicator_id" required>
                    <?php
                    $indM = new \App\Models\IndicatorModel();
                    $prefR = (int)($region_id ?? 0);
                    $inds = $prefR ? $indM->where('region_id', $prefR)->orderBy('name', 'ASC')->findAll()
                        : $indM->orderBy('name', 'ASC')->findAll();
                    foreach ($inds as $i):
                    ?>
                        <option value="<?= $i['id']; ?>" <?= (int)($indicator_id ?? 0) === (int)$i['id'] ? 'selected' : ''; ?>>
                            <?= esc($i['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <a class="btn btn-outline-secondary w-100"
                    id="btnTemplate"
                    href="<?= base_url('api/export/indicator-template'); ?>?indicator_id=<?= (int)($indicator_id ?? 0) ?>&region_id=<?= (int)($region_id ?? 0) ?>">
                    Download Template Import Data
                </a>
            </div>
            <div class="col-12">
                <hr>
            </div>
            <div class="col-md-6">
                <label class="form-label">File Excel (.xlsx)</label>
                <input type="file" class="form-control" name="file" accept=".xlsx" required>
                <small class="text-muted">Gunakan tombol “Download Template Import Data” untuk format yang sesuai.</small>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button class="btn btn-primary">Import</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>