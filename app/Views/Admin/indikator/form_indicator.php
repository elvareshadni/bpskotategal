<?= $this->extend('Admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h5 class="mb-3"><?= isset($indicator) ? 'Edit' : 'Tambah'; ?> Indikator</h5>

<form method="post" action="<?= base_url('admin/indicator/save'); ?>" class="card p-3">
    <?= csrf_field(); ?>
    <input type="hidden" name="id" value="<?= $indicator['id'] ?? '' ?>">

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Kota/Kabupaten</label>
            <select class="form-select" name="region_id" required>
                <?php foreach ($regions as $rg):
                    $selected = false;
                    if (isset($indicator)) {
                        $selected = ($indicator['region_id'] == $rg['id']);
                    } else {
                        $selected = (!empty($prefRegionId) && $prefRegionId == $rg['id']);
                    }
                ?>
                    <option value="<?= $rg['id']; ?>" <?= $selected ? 'selected' : ''; ?>>
                        <?= esc($rg['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-5">
            <label class="form-label">Nama Indikator</label>
            <input class="form-control" name="name" required value="<?= esc($indicator['name'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Kode (opsional)</label>
            <input class="form-control" name="code" value="<?= esc($indicator['code'] ?? '') ?>">
        </div>
    </div>

    <hr>

    <h6>Subindikator</h6>
    <?php if (!empty($subs)): ?>
        <ul class="mb-3">
            <?php foreach ($subs as $s): ?>
                <li>
                    <a class="link-primary" href="<?= base_url('admin/subindicator/form?id=' . $s['id']); ?>">
                        <?= esc($s['subindikator']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="text-muted mb-3">Belum ada subindikator.</div>
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label">Tambah Subindikator</label>
        <textarea class="form-control" name="sub_new" rows="3"
            placeholder="Contoh:&#10;Jumlah Penduduk&#10;Rasio Jenis Kelamin"></textarea>
        <small class="text-muted">Tulis satu subindikator per baris. Akan dibuat banyak sekaligus.</small>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary">Simpan</button>
        <a class="btn btn-light" href="<?= base_url('admin/indicators'); ?>">Cancel</a>
    </div>
</form>

<?= $this->endSection(); ?>