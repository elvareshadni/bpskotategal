<?= $this->extend('Admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 text-gray-800 mb-4">Edit Slide Carousel</h1>

<div class="card shadow">
    <div class="card-body">
        <form action="<?= base_url('admin/carousel/update/' . $row['id']); ?>" method="post" enctype="multipart/form-data">
            <?= function_exists('csrf_field') ? csrf_field() : '' ?>

            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" class="form-control" name="judul" value="<?= esc($row['judul']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Gambar (opsional, JPG/PNG, maks 2MB)</label>
                <input type="file" class="form-control" name="gambar" accept=".jpg,.jpeg,.png" id="gambar">

                <?php
                $path1 = FCPATH . 'img/carousel/' . $row['gambar'];
                $url1  = base_url('img/carousel/' . $row['gambar']);
                $path2 = FCPATH . 'img/' . $row['gambar'];
                $url2  = base_url('img/' . $row['gambar']);
                $exists1 = is_file($path1);
                $exists2 = is_file($path2);
                $url = $exists1 ? $url1 : ($exists2 ? $url2 : '');
                ?>

                <div class="mt-2" id="previewBox" style="<?= $url ? '' : 'display:none;' ?>">
                    <a href="<?= $url ?: '#'; ?>" target="<?= $url ? '_blank' : '_self' ?>" rel="noopener" id="previewLink">
                        <img id="imgPreview"
                            src="<?= $url ?: '' ?>"
                            data-original="<?= $url ?: '' ?>"
                            alt="<?= esc($row['judul']); ?>"
                            style="height:100px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">
                    </a>
                </div>
                <small class="text-muted d-block mt-1" id="imgCaption">
                    <?= $url ? 'Gambar saat ini: ' . esc($row['gambar']) : 'Belum ada gambar. Pilih file untuk melihat preview.' ?>
                </small>
                <?php if ($url): ?>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="revertBtn" style="display:none;">
                        Batalkan & pakai gambar lama
                    </button>
                <?php endif; ?>
            </div>

            <script>
                (function() {
                    const input = document.getElementById('gambar');
                    const box = document.getElementById('previewBox');
                    const img = document.getElementById('imgPreview');
                    const link = document.getElementById('previewLink');
                    const cap = document.getElementById('imgCaption');
                    const revert = document.getElementById('revertBtn');
                    const original = img ? img.dataset.original : '';

                    input.addEventListener('change', (e) => {
                        const file = e.target.files && e.target.files[0];
                        if (!file) {
                            if (original) {
                                img.src = original;
                                link.href = original;
                                cap.textContent = 'Gambar saat ini';
                                box.style.display = '';
                                if (revert) revert.style.display = 'none';
                            } else {
                                img.removeAttribute('src');
                                link.removeAttribute('href');
                                cap.textContent = 'Belum ada gambar. Pilih file untuk melihat preview.';
                                box.style.display = 'none';
                            }
                            return;
                        }
                        if (!/^image\/(jpeg|png)$/.test(file.type)) {
                            alert('Hanya JPG/PNG.');
                            input.value = '';
                            return;
                        }
                        if (file.size > 2 * 1024 * 1024) {
                            alert('Maks 2MB');
                            input.value = '';
                            return;
                        }
                        const url = URL.createObjectURL(file);
                        img.src = url;
                        link.href = url;
                        box.style.display = '';
                        cap.textContent = 'Preview gambar baru (belum tersimpan).';
                        if (revert) revert.style.display = 'inline-block';
                    });

                    if (revert) {
                        revert.addEventListener('click', () => {
                            input.value = '';
                            if (original) {
                                img.src = original;
                                link.href = original;
                                cap.textContent = 'Gambar saat ini';
                                box.style.display = '';
                                revert.style.display = 'none';
                            } else {
                                img.removeAttribute('src');
                                link.removeAttribute('href');
                                cap.textContent = 'Belum ada gambar. Pilih file untuk melihat preview.';
                                box.style.display = 'none';
                            }
                        });
                    }
                })();
            </script>

            <div class="mb-3">
                <label class="form-label">Link URL (opsional)</label>
                <input type="url" class="form-control" name="link_url" value="<?= esc($row['link_url'] ?? ''); ?>" placeholder="https://contoh.go.id/halaman">
            </div>

            <button class="btn btn-primary">Update</button>
            <a class="btn btn-secondary" href="<?= base_url('admin/carousel'); ?>">Kembali</a>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>