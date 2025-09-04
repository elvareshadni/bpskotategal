<!-- <?= $this->extend('Admin/templates/index'); ?>
<?= $this->section('content'); ?>

<div class="container mt-4">
  <h3>Daftar Carousel</h3>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success'); ?></div>
  <?php endif; ?>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>Judul</th>
        <th>Gambar</th>
        <th>Posisi</th>
        <th>Dibuat</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $carouselModel = new \App\Models\CarouselModel();
      $carousels = $carouselModel->findAll();
      if ($carousels):
        $no=1;
        foreach ($carousels as $c): ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= esc($c['judul']); ?></td>
            <td><img src="<?= base_url('img/'.$c['gambar']); ?>" width="100"></td>
            <td><?= esc($c['posisi']); ?></td>
            <td><?= esc($c['created_at']); ?></td>
          </tr>
      <?php endforeach; else: ?>
        <tr>
          <td colspan="5" class="text-center">Belum ada data carousel</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?= $this->endSection(); ?> -->
