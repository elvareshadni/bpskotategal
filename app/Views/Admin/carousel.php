<!-- <?= $this->extend('Admin/templates/index'); ?>
<?= $this->section('content'); ?>

<div class="container mt-5">
  <h3>Daftar Carousel</h3>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
      <?= session()->getFlashdata('success') ?>
    </div>
  <?php endif; ?>

  <a href="<?= base_url('admin/carouselAdd'); ?>" class="btn btn-primary mb-3">+ Tambah Carousel</a>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>Judul</th>
        <th>Gambar</th>
        <th>Posisi</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (isset($carousels) && count($carousels) > 0): ?>
        <?php foreach ($carousels as $i => $c): ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><?= esc($c['judul']) ?></td>
            <td><img src="<?= base_url('img/'.$c['gambar']); ?>" width="150"></td>
            <td><?= esc($c['posisi']) ?></td>
            <td>
              <a href="<?= base_url('admin/editcarousel/'.$c['id']); ?>" class="btn btn-sm btn-warning">Edit</a>
              <a href="<?= base_url('admin/deletecarousel/'.$c['id']); ?>" 
                 class="btn btn-sm btn-danger"
                 onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="5" class="text-center">Belum ada data carousel</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?= $this->endSection(); ?> -->
