<?= $this->extend('Admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 text-gray-800 mb-4">Manajemen Infografis — Daftar</h1>

<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <a href="<?= base_url('admin/tambah-infografis'); ?>" class="btn btn-primary">
      <i class="fas fa-plus"></i> Tambah Infografis
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
              <th>Deskripsi</th>
              <th>Gambar</th>
              <th>Tanggal</th>
              <th style="width:200px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($rows)): $no = 1;
              foreach ($rows as $r): ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td><?= esc($r['judul']); ?></td>

                  <td><?= esc($r['deskripsi']); ?></td>

                  <td>
                    <?php if (!empty($r['gambar'])):
                      $path = FCPATH . 'img/' . $r['gambar'];
                      $url  = base_url('img/' . $r['gambar']);
                      $exists = is_file($path);
                    ?>
                      <?php if ($exists): ?>
                        <a href="<?= $url; ?>" target="_blank" rel="noopener">
                          <img src="<?= $url; ?>" alt="<?= esc($r['judul']); ?>" style="height:60px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;">
                        </a>
                        <div class="small text-muted mt-1"><?= esc($r['gambar']); ?></div>
                      <?php else: ?>
                        <span class="badge bg-warning text-dark">File tidak ditemukan</span>
                        <div class="small text-muted mt-1"><?= esc($r['gambar']); ?></div>
                      <?php endif; ?>
                    <?php else: ?>
                      <span class="text-muted">—</span>
                    <?php endif; ?>
                  </td>

                  <td><?= esc($r['tanggal']); ?></td>

                  <td>
                    <a href="<?= base_url('admin/edit-infografis/' . $r['id']); ?>" class="btn btn-sm btn-info">Edit</a>
                    <button type="button"
                      class="btn btn-sm btn-danger btn-delete"
                      data-url="<?= base_url('admin/edit-infografis/delete/' . $r['id']); ?>"
                      data-title="<?= esc($r['judul']); ?>"
                      data-image="<?php
                                  $imgPath = FCPATH . 'img/' . $r['gambar'];
                                  if (!empty($r['gambar']) && is_file($imgPath)) echo base_url('img/' . $r['gambar']);
                                  ?>">
                      Hapus
                    </button>

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
      // Jika ingin hapus via POST + CSRF (lebih aman), aktifkan block "POST mode" di bawah
      const USE_POST = false; // set true untuk kirim via POST

      // Siapkan form POST + CSRF (opsional)
      let postForm = null;
      <?php if (function_exists('csrf_token')): ?>
        const csrfName = '<?= csrf_token() ?>';
        const csrfHash = '<?= csrf_hash() ?>';
      <?php endif; ?>

      if (USE_POST) {
        postForm = document.createElement('form');
        postForm.method = 'post';
        postForm.style.display = 'none';
        if (csrfName && csrfHash) {
          const i = document.createElement('input');
          i.type = 'hidden';
          i.name = csrfName;
          i.value = csrfHash;
          postForm.appendChild(i);
        }
        document.body.appendChild(postForm);
      }

      document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
          const url = btn.dataset.url;
          const title = btn.dataset.title || '(tanpa judul)';
          const image = btn.dataset.image || '';

          // HTML isi modal (judul + preview)
          const html = `
        <div class="d-flex align-items-center" style="gap:12px;">
          ${image ? `<img src="${image}" alt="" style="height:60px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;">` : ``}
          <div>
            <div class="fw-semibold">${title}</div>
            <small class="text-muted">Aksi ini tidak bisa dibatalkan.</small>
          </div>
        </div>
      `;

          Swal.fire({
            title: 'Anda yakin akan menghapus data ini?',
            html,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true,
            buttonsStyling: false,
            customClass: {
              actions: 'd-flex justify-content-center gap-5 mt-3',
              confirmButton: 'btn btn-danger me-2',
              cancelButton: 'btn btn-secondary'
            }
          }).then((res) => {
            if (!res.isConfirmed) return;

            // Mode GET sederhana
            if (!USE_POST) {
              // optional: state "menghapus..."
              Swal.showLoading();
              window.location.href = url;
              return;
            }

            // Mode POST + CSRF
            postForm.action = url;
            postForm.submit();
          });
        });
      });
    })();
  </script>


  <?= $this->endSection(); ?>