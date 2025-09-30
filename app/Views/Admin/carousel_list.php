<?= $this->extend('Admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 text-gray-800 mb-4">Manajemen Carousel — Daftar</h1>

<div class="container mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="<?= base_url('admin/carousel/add'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Slide
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:60px;">No</th>
                            <th>Judul</th>
                            <th>Gambar</th>
                            <th>Posisi</th>
                            <th>Dibuat</th>
                            <th>Tautan</th>
                            <th style="width:200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rows)): $no = 1;
                            foreach ($rows as $r): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= esc($r['judul']); ?></td>

                                    <td>
                                        <?php
                                        $path1 = FCPATH . 'img/carousel/' . $r['gambar'];
                                        $url1  = base_url('img/carousel/' . $r['gambar']);
                                        $path2 = FCPATH . 'img/' . $r['gambar']; // fallback seeder lama
                                        $url2  = base_url('img/' . $r['gambar']);
                                        $exists1 = is_file($path1);
                                        $exists2 = is_file($path2);
                                        $url = $exists1 ? $url1 : ($exists2 ? $url2 : '');
                                        ?>
                                        <?php if ($url): ?>
                                            <a href="<?= $url; ?>" target="_blank" rel="noopener">
                                                <img src="<?= $url; ?>" alt="<?= esc($r['judul']); ?>" style="height:60px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;">
                                            </a>
                                            <div class="small text-muted mt-1"><?= esc($r['gambar']); ?></div>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">File tidak ditemukan</span>
                                            <div class="small text-muted mt-1"><?= esc($r['gambar']); ?></div>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php
                                        $lbl = strtoupper($r['posisi'] ?? 'CENTER');
                                        ?>
                                        <span class="badge bg-light text-dark"><?= esc($lbl); ?></span>
                                    </td>

                                    <td><?= esc($r['created_at'] ?? ''); ?></td>

                                    <td>
                                        <a href="<?= base_url('admin/carousel/edit/' . $r['id']); ?>" class="btn btn-sm btn-info">Edit</a>
                                        <button type="button"
                                            class="btn btn-sm btn-danger btn-delete"
                                            data-url="<?= base_url('admin/carousel/delete/' . $r['id']); ?>"
                                            data-title="<?= esc($r['judul']); ?>"
                                            data-image="<?= $url; ?>">
                                            Hapus
                                        </button>
                                    </td>
                                    <td>
                                        <?php if (!empty($r['link_url'])): ?>
                                            <a href="<?= esc($r['link_url']); ?>" target="_blank" rel="noopener">Buka</a>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        (function() {
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', () => {
                    const url = btn.dataset.url;
                    const title = btn.dataset.title || '(tanpa judul)';
                    const image = btn.dataset.image || '';

                    const html = `
            <div class="d-flex align-items-center" style="gap:12px;">
              ${image ? `<img src="${image}" alt="" style="height:60px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;">` : ``}
              <div>
                <div class="fw-semibold">${title}</div>
                <small class="text-muted">Aksi ini tidak bisa dibatalkan.</small>
              </div>
            </div>
          `;

                    if (window.Swal) {
                        Swal.fire({
                            title: 'Hapus slide ini?',
                            html,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true,
                            buttonsStyling: false,
                            customClass: {
                                actions: 'd-flex justify-content-center gap-5 mt-3',
                                confirmButton: 'btn btn-danger me-2',
                                cancelButton: 'btn btn-secondary'
                            }
                        }).then(res => {
                            if (res.isConfirmed) window.location.href = url;
                        });
                    } else {
                        if (confirm('Yakin hapus "' + title + '" ?')) window.location.href = url;
                    }
                });
            });
        })();
    </script>

    <?= $this->endSection(); ?>