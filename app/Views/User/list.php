<?= $this->extend('Template/index'); ?>
<?= $this->section('content'); ?>

<div class="container mt-3">
  <div class="container py-4">
    <div class="stats-container stats-blue rounded-3 p-3 p-md-4 shadow-sm" style="background-color: #dcddeeff">
      <!-- Breadcrumb -->
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= base_url('/user'); ?>">Beranda</a></li>
          <li class="breadcrumb-item active" aria-current="page">Infografis</li>
        </ol>
      </nav>


      <h2 class="mb-3 text-black"><strong>Daftar Infografis</strong></h2>

      <div class="row g-3 mb-3 align-items-stretch">
        <?php if (!empty($infografis)): ?>
          <?php foreach ($infografis as $item): ?>
            <div class="col-md-6 d-flex">
              <a href="<?= base_url('user/detail/' . $item['id']); ?>" class="text-decoration-none text-dark w-100">
                <div class="card shadow-sm h-100">
                  <div class="row g-0 h-100">
                    <div class="col-md-4">
                      <img src="<?= base_url('img/' . $item['gambar']); ?>"
                        class="img-fluid rounded-start border border-2 border-white h-100"
                        alt="<?= esc($item['judul']); ?>"
                        style="object-fit: cover; width: 100%;">
                    </div>
                    <div class="col-md-8 d-flex">
                      <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                          <h6 class="text-muted"><?= date('d M Y', strtotime($item['tanggal'])); ?></h6>
                          <h5 class="card-title mb-2"><?= esc($item['judul']); ?></h5>
                          <p class="card-text mb-0"><?= esc(substr($item['deskripsi'], 0, 100)); ?>...</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-muted">Belum ada data infografis.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>