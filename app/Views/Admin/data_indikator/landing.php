<?= $this->extend('Admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h5 class="mb-3">Kelola Data Indikator</h5>

<div class="mb-3 d-flex gap-2">
    <a class="btn btn-outline-primary" href="<?= base_url('admin/regions'); ?>">Kelola Region</a>
    <a class="btn btn-outline-primary" href="<?= base_url('admin/indicators'); ?>">Kelola Indikator</a>
</div>

<?= $this->endSection(); ?>