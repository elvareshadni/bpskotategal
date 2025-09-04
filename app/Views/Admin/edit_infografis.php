<?= $this->extend('Admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 text-gray-800 mb-4">Edit Infografis</h1>

<div class="card shadow">
    <div class="card-body">
        <form action="<?= base_url('admin/edit-infografis/update/' . $row['id']); ?>" method="post" enctype="multipart/form-data">
            <?= function_exists('csrf_field') ? csrf_field() : '' ?>

            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" class="form-control" name="judulInfografis"
                    value="<?= esc($row['judul']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <input type="text" class="form-control" name="deskripsiInfografis"
                    value="<?= esc($row['deskripsi']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal (YYYY-MM-DD)</label>
                <input type="text" class="form-control" name="tanggal"
                    value="<?= esc($row['tanggal']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Gambar (JPG/PNG, maks 2MB)</label>
                <input type="file" class="form-control" name="infografisImage" accept=".jpg,.jpeg,.png" id="infografisImage">

                <?php
                $hasOld = !empty($row['gambar']);
                $oldPath = $hasOld ? FCPATH . 'img/' . $row['gambar'] : null;
                $oldUrl  = $hasOld ? base_url('img/' . $row['gambar']) : '';
                $oldOK   = $hasOld && is_file($oldPath);
                ?>

                <div class="mt-2" id="previewBox" style="<?= $oldOK ? '' : 'display:none;' ?>">
                    <a href="<?= $oldOK ? $oldUrl : '#' ?>" target="<?= $oldOK ? '_blank' : '_self' ?>" rel="noopener" id="previewLink">
                        <img
                            id="imgPreview"
                            src="<?= $oldOK ? $oldUrl : '' ?>"
                            data-original="<?= $oldOK ? $oldUrl : '' ?>"
                            alt="<?= esc($row['judul']); ?>"
                            style="height:100px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">
                    </a>
                </div>

                <small class="text-muted d-block mt-1" id="imgCaption">
                    <?= $oldOK ? 'Gambar saat ini: ' . esc($row['gambar']) : 'Belum ada gambar. Pilih file untuk melihat preview.' ?>
                </small>

                <?php if ($oldOK): ?>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="revertBtn" style="display:none;">
                        Batalkan & pakai gambar lama
                    </button>
                <?php endif; ?>
            </div>

            <script>
                (function() {
                    const input = document.getElementById('infografisImage');
                    const box = document.getElementById('previewBox');
                    const img = document.getElementById('imgPreview');
                    const link = document.getElementById('previewLink');
                    const caption = document.getElementById('imgCaption');
                    const revert = document.getElementById('revertBtn');
                    const originalSrc = img ? img.dataset.original : '';

                    if (!input) return;

                    input.addEventListener('change', function(e) {
                        const file = e.target.files && e.target.files[0];
                        if (!file) {
                            // Tidak jadi pilih file → kembali ke kondisi awal
                            if (originalSrc) {
                                img.src = originalSrc;
                                link.href = originalSrc;
                                caption.textContent = 'Gambar saat ini';
                                if (revert) revert.style.display = 'none';
                                box.style.display = '';
                            } else {
                                img.removeAttribute('src');
                                link.removeAttribute('href');
                                caption.textContent = 'Belum ada gambar. Pilih file untuk melihat preview.';
                                box.style.display = 'none';
                            }
                            return;
                        }

                        // Validasi ringan di sisi klien
                        if (!/^image\/(jpeg|png)$/.test(file.type)) {
                            alert('Hanya menerima JPG/PNG.');
                            input.value = '';
                            return;
                        }
                        if (file.size > 2 * 1024 * 1024) {
                            alert('Ukuran file maksimal 2MB.');
                            input.value = '';
                            return;
                        }

                        const objectUrl = URL.createObjectURL(file);
                        if (img) img.src = objectUrl;
                        if (link) link.href = objectUrl;
                        box.style.display = '';
                        caption.textContent = 'Preview gambar baru (belum tersimpan).';
                        if (revert) revert.style.display = 'inline-block';
                    });

                    if (revert) {
                        revert.addEventListener('click', function() {
                            // Kembalikan ke gambar lama dari DB
                            input.value = '';
                            if (originalSrc) {
                                img.src = originalSrc;
                                link.href = originalSrc;
                                caption.textContent = 'Gambar saat ini';
                                revert.style.display = 'none';
                                box.style.display = '';
                            } else {
                                img.removeAttribute('src');
                                link.removeAttribute('href');
                                caption.textContent = 'Belum ada gambar. Pilih file untuk melihat preview.';
                                box.style.display = 'none';
                            }
                        });
                    }
                })();
            </script>

            <button class="btn btn-primary">Update</button>
            <a class="btn btn-secondary" href="<?= base_url('admin/edit-infografis/list'); ?>">Kembali</a>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>