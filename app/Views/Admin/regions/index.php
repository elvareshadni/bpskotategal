<?= $this->extend('Admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h5 class="mb-3">Kelola Region</h5>

<div class="card mb-3">
    <div class="card-body">
        <form method="post" action="<?= base_url('admin/regions/create'); ?>" class="row g-2 align-items-end">
            <?= csrf_field(); ?>
            <div class="col-md-3">
                <label class="form-label">Kode BPS (opsional)</label>
                <input type="text" name="code_bps" class="form-control" placeholder="3372">
            </div>
            <div class="col-md-5">
                <label class="form-label">Kota/Kabupaten</label>
                <input type="text" name="name" class="form-control" required placeholder="Kota Tegal">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Tambah Lokasi</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th style="width:160px">Kode BPS</th>
                        <th>Kota/Kabupaten</th>
                        <th style="width:160px"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($regions as $r): ?>
                        <tr>
                            <form method="post" action="<?= base_url('admin/regions/update/' . $r['id']); ?>">
                                <?= csrf_field(); ?>
                                <td><input class="form-control form-control-sm" name="code_bps" value="<?= esc($r['code_bps']) ?>"></td>
                                <td><input class="form-control form-control-sm" name="name" value="<?= esc($r['name']) ?>" required></td>
                                <td class="text-end">
                                    <button class="btn btn-success btn-sm">Update</button>
                                    <form method="post" action="<?= base_url('admin/regions/delete/' . $r['id']); ?>" class="d-inline">
                                        <?= csrf_field(); ?>
                                        <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus region ini?')">Delete</button>
                                    </form>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>