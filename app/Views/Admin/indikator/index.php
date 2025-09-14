<?= $this->extend('Admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h5 class="mb-3">Kelola Indikator</h5>

<div class="card mb-3">
    <div class="card-body row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Kota/Kabupaten</label>
            <select id="region_id" class="form-select">
                <?php foreach ($regions as $rg): ?>
                    <option value="<?= $rg['id']; ?>" <?= $rg['id'] == $currentRegionId ? 'selected' : ''; ?>>
                        <?= esc($rg['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 ms-auto text-end">
            <button type="button" id="btnAddIndicator" class="btn btn-primary">Tambah Indikator</button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div id="indikatorList"></div>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    (async function() {
        const box = document.getElementById('indikatorList');
        const regionSel = document.getElementById('region_id');
        const addBtn = document.getElementById('btnAddIndicator');

        // Klik "Tambah Indikator" => redirect ke form dengan ?region_id=<terpilih>
        if (addBtn) {
            addBtn.addEventListener('click', () => {
                const rid = regionSel.value;
                window.location.href = "<?= base_url('admin/indicator/form'); ?>?region_id=" + encodeURIComponent(rid);
            });
        }

        async function loadList() {
            const rid = regionSel.value;
            const res = await fetch(`<?= base_url('admin/indicators/list'); ?>?region_id=${rid}`);
            const json = await res.json();
            if (!json.ok) {
                box.innerHTML = '<div class="text-danger">Gagal memuat.</div>';
                return;
            }

            const html = json.data.map(it => {
                const subs = (it.subs || []).map(s => `
        <li>
          <a href="<?= base_url('admin/subindicator/form'); ?>?id=${s.id}" class="link-primary">${s.subindikator}</a>
          <form action="<?= base_url('admin/subindicator/delete'); ?>/${s.id}" method="post" class="d-inline frm-del-sub">
            <?= csrf_field(); ?>
            <button type="button" class="btn btn-link text-danger p-0 ms-2 btn-del-sub">Hapus</button>
          </form>
        </li>
      `).join('');

                return `
        <div class="border rounded p-3 mb-3">
          <div class="d-flex align-items-center">
            <h6 class="mb-0">
              <a href="<?= base_url('admin/indicator/form'); ?>?id=${it.id}" class="link-dark">${it.name}</a>
            </h6>
            <div class="ms-auto">
              <form action="<?= base_url('admin/indicator/delete'); ?>/${it.id}" method="post" class="d-inline frm-del-ind">
                <?= csrf_field(); ?>
                <button type="button" class="btn btn-outline-danger btn-sm btn-del-ind">Hapus</button>
              </form>
            </div>
          </div>
          <div class="small text-muted mt-1">Subindikator: ${it.subcount}</div>
          <ul class="mt-2 mb-0">${subs || '<em class="text-muted">Belum ada subindikator.</em>'}</ul>
        </div>`;
            }).join('');

            box.innerHTML = html || '<em class="text-muted">Belum ada indikator.</em>';

            // Binding SweetAlert untuk tombol hapus (indikator)
            box.querySelectorAll('.btn-del-ind').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const form = btn.closest('form.frm-del-ind');
                    const ok = await Swal.fire({
                        title: 'Hapus indikator?',
                        text: 'Semua subindikator & data terkait juga akan terhapus.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then(r => r.isConfirmed);
                    if (ok) form.submit();
                });
            });

            // Binding SweetAlert untuk tombol hapus (subindikator)
            box.querySelectorAll('.btn-del-sub').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const form = btn.closest('form.frm-del-sub');
                    const ok = await Swal.fire({
                        title: 'Hapus subindikator?',
                        text: 'Semua nilai terkait subindikator ini akan terhapus.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then(r => r.isConfirmed);
                    if (ok) form.submit();
                });
            });
        }

        regionSel.addEventListener('change', loadList);
        await loadList();
    })();
</script>
<?php if (session()->getFlashdata('success')): ?>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            Swal.fire('Berhasil', <?= json_encode(session()->getFlashdata('success')); ?>, 'success');
        });
    </script>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            Swal.fire('Gagal', <?= json_encode(session()->getFlashdata('error')); ?>, 'error');
        });
    </script>
<?php endif; ?>

<?= $this->endSection(); ?>