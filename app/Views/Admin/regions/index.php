<?= $this->extend('Admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h5 class="mb-3">Kelola Region</h5>

<div class="card mb-3">
    <div class="card-body">
        <form id="frmCreateRegion" method="post" action="<?= base_url('admin/regions/create'); ?>" class="row g-2 align-items-end">
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
                <button type="button" id="btnCreateRegion" class="btn btn-primary w-100">Tambah Lokasi</button>
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
                        <th style="width:200px" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($regions as $r): ?>
                        <tr data-id="<?= $r['id'] ?>">
                            <td>
                                <input class="form-control form-control-sm inpCode" value="<?= esc($r['code_bps']) ?>">
                            </td>
                            <td>
                                <input class="form-control form-control-sm inpName" value="<?= esc($r['name']) ?>" required>
                            </td>
                            <td class="text-end">
                                <!-- FORM UPDATE: khusus di kolom aksi -->
                                <form class="d-inline frmUpdateRegion" method="post" action="<?= base_url('admin/regions/update/' . $r['id']); ?>">
                                    <?= csrf_field(); ?>
                                    <input type="hidden" name="code_bps">
                                    <input type="hidden" name="name">
                                    <button type="button" class="btn btn-success btn-sm btnUpdate">Update</button>
                                </form>

                                <!-- FORM DELETE: terpisah -->
                                <form class="d-inline frmDeleteRegion" method="post" action="<?= base_url('admin/regions/delete/' . $r['id']); ?>">
                                    <?= csrf_field(); ?>
                                    <button type="button" class="btn btn-outline-danger btn-sm btnDelete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    (function() {
        // ========== CREATE ==========
        const btnCreate = document.getElementById('btnCreateRegion');
        const frmCreate = document.getElementById('frmCreateRegion');

        btnCreate?.addEventListener('click', async () => {
            const name = frmCreate.querySelector('input[name="name"]').value.trim();
            if (!name) {
                await Swal.fire({
                    icon: 'warning',
                    title: 'Nama belum diisi',
                    text: 'Silakan isi nama Kota/Kabupaten.'
                });
                return;
            }
            const ok = await Swal.fire({
                title: 'Tambah lokasi baru?',
                text: 'Data region akan disimpan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, simpan',
                cancelButtonText: 'Batal'
            }).then(r => r.isConfirmed);
            if (ok) frmCreate.submit();
        });

        // ========== UPDATE ==========
        document.querySelectorAll('tr[data-id]').forEach(function(tr) {
            const form = tr.querySelector('.frmUpdateRegion');
            const btn = tr.querySelector('.btnUpdate');
            const inpC = tr.querySelector('.inpCode');
            const inpN = tr.querySelector('.inpName');

            btn?.addEventListener('click', async () => {
                const name = (inpN.value || '').trim();
                if (!name) {
                    await Swal.fire({
                        icon: 'warning',
                        title: 'Nama belum diisi',
                        text: 'Silakan isi nama Kota/Kabupaten.'
                    });
                    return;
                }

                const ok = await Swal.fire({
                    title: 'Simpan perubahan region?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal'
                }).then(r => r.isConfirmed);

                if (!ok) return;

                // isi hidden input di form update (agar POST sesuai yang diharapkan controller)
                form.querySelector('input[name="code_bps"]').value = inpC.value.trim();
                form.querySelector('input[name="name"]').value = name;

                form.submit();
            });
        });

        // ========== DELETE ==========
        document.querySelectorAll('.frmDeleteRegion').forEach(function(form) {
            const btn = form.querySelector('.btnDelete');
            btn?.addEventListener('click', async () => {
                const ok = await Swal.fire({
                    title: 'Hapus region ini?',
                    text: 'Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then(r => r.isConfirmed);
                if (ok) form.submit();
            });
        });
    })();
</script>

<?php if (session()->getFlashdata('success')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '<?= esc(session()->getFlashdata('success')) ?>'
        });
    </script>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '<?= esc(session()->getFlashdata('error')) ?>'
        });
    </script>
<?php endif; ?>

<?= $this->endSection(); ?>