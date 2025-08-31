<?= $this->extend('admin/templates/index'); ?>
<?= $this->section('content'); ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Daftar Infografis</h2>
        <a href="<?= base_url('admin/infografisAdd'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Infografis
        </a>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success'); ?></div>
    <?php endif; ?>

    <?php if (!empty($infografis)): ?>
        <div class="row g-3">
            <?php foreach($infografis as $item): ?>
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= base_url('img/' . $item['gambar']); ?>" 
                             class="card-img-top" 
                             alt="<?= esc($item['judul']); ?>" 
                             style="height: 180px; object-fit: cover;">
                        <div class="card-body">
                            <small class="text-muted"><?= date('d M Y', strtotime($item['tanggal'])); ?></small>
                            <h6 class="card-title mt-2"><?= esc($item['judul']); ?></h6>
                            <p class="small text-muted"><?= character_limiter($item['deskripsi'], 80); ?></p>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="<?= base_url('admin/editInfografis/'.$item['id']); ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= base_url('admin/deleteInfografis/'.$item['id']); ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Yakin ingin hapus?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">Belum ada data infografis.</p>
    <?php endif; ?>
</div>

<?= $this->endSection(); ?>
