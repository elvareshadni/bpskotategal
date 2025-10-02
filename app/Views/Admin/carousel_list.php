<?= $this->extend('Admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 text-gray-800 mb-4">Manajemen Carousel — Daftar</h1>

<div class="container mt-3">

    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
        <a href="<?= base_url('admin/carousel/add'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Slide
        </a>

        <div class="d-flex flex-wrap gap-2 ms-auto">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input id="searchInput" type="text" class="form-control" placeholder="Cari judul…">
            </div>

            <select id="sortSelect" class="form-select">
                <option value="newest" selected>Urutkan: Terbaru</option>
                <option value="name">Urutkan: Nama (A–Z)</option>
            </select>

            <select id="pageSize" class="form-select">
                <option value="10" selected>Tampilkan 10</option>
                <option value="25">Tampilkan 25</option>
                <option value="50">Tampilkan 50</option>
            </select>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0" id="carouselTable">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:60px;">No</th>
                            <th>Judul</th>
                            <th>Gambar</th>
                            <th style="width:160px;">Dibuat</th>
                            <th style="width:120px;">Tautan</th>
                            <th style="width:200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="rowsBody">
                        <!-- diisi JS -->
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="small text-muted" id="pageInfo">–</div>
                <div class="btn-group">
                    <button class="btn btn-outline-secondary" id="prevBtn">&laquo;</button>
                    <button class="btn btn-outline-secondary" id="nextBtn">&raquo;</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Siapkan data ke JSON + URL gambar fallback
$rowsJson = [];
if (!empty($rows)) {
    foreach ($rows as $r) {
        $path1 = FCPATH . 'img/carousel/' . $r['gambar'];
        $url1  = base_url('img/carousel/' . $r['gambar']);
        $path2 = FCPATH . 'img/' . $r['gambar'];
        $url2  = base_url('img/' . $r['gambar']);
        $exists1 = is_file($path1);
        $exists2 = is_file($path2);
        $url = $exists1 ? $url1 : ($exists2 ? $url2 : '');

        $rowsJson[] = [
            'id'         => $r['id'],
            'judul'      => $r['judul'],
            'gambar'     => $r['gambar'],
            'image_url'  => $url,
            'created_at' => $r['created_at'] ?? '',
            'link_url'   => $r['link_url'] ?? ''
        ];
    }
}
?>
<script id="rows-json" type="application/json">
    <?= json_encode($rowsJson, JSON_UNESCAPED_SLASHES) ?>
</script>

<script>
    (function() {
        const data = JSON.parse(document.getElementById('rows-json').textContent || '[]');

        const $body = document.getElementById('rowsBody');
        const $search = document.getElementById('searchInput');
        const $sort = document.getElementById('sortSelect');
        const $size = document.getElementById('pageSize');
        const $prev = document.getElementById('prevBtn');
        const $next = document.getElementById('nextBtn');
        const $info = document.getElementById('pageInfo');

        let page = 1;

        function escapeHtml(s) {
            return String(s ?? '').replace(/[&<>"']/g, m => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            } [m]));
        }

        function filteredSorted() {
            const q = ($search.value || '').trim().toLowerCase();
            let out = data.filter(r => r.judul.toLowerCase().includes(q));

            if ($sort.value === 'name') {
                out.sort((a, b) => a.judul.localeCompare(b.judul, 'id'));
            } else {
                // newest
                out.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0));
            }
            return out;
        }

        function render() {
            const size = parseInt($size.value, 10) || 10;
            const rows = filteredSorted();
            const total = rows.length;
            const pages = Math.max(1, Math.ceil(total / size));
            if (page > pages) page = pages;

            const start = (page - 1) * size;
            const slice = rows.slice(start, start + size);

            let html = '';
            slice.forEach((r, i) => {
                const no = start + i + 1;
                const imgHtml = r.image_url ?
                    `<a href="${r.image_url}" target="_blank" rel="noopener">
             <img src="${r.image_url}" alt="${escapeHtml(r.judul)}" style="height:60px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;">
           </a>
           <div class="small text-muted mt-1">${escapeHtml(r.gambar)}</div>` :
                    `<span class="badge bg-warning text-dark">File tidak ditemukan</span>
           <div class="small text-muted mt-1">${escapeHtml(r.gambar)}</div>`;

                const linkHtml = r.link_url ?
                    `<a href="${escapeHtml(r.link_url)}" target="_blank" rel="noopener">Buka</a>` :
                    `<span class="text-muted">—</span>`;

                html += `<tr>
        <td>${no}</td>
        <td>${escapeHtml(r.judul)}</td>
        <td>${imgHtml}</td>
        <td>${escapeHtml(r.created_at || '')}</td>
        <td>${linkHtml}</td>
        <td>
          <a href="<?= base_url('admin/carousel/edit/'); ?>${r.id}" class="btn btn-sm btn-info">Edit</a>
          <button type="button" class="btn btn-sm btn-danger btn-delete"
                  data-url="<?= base_url('admin/carousel/delete/'); ?>${r.id}"
                  data-title="${escapeHtml(r.judul)}"
                  data-image="${r.image_url || ''}">
            Hapus
          </button>
        </td>
      </tr>`;
            });
            $body.innerHTML = html;

            // info + pager
            const showFrom = total ? (start + 1) : 0;
            const showTo = total ? (start + slice.length) : 0;
            $info.textContent = `Menampilkan ${showFrom}–${showTo} dari ${total} data`;
            $prev.disabled = (page <= 1);
            $next.disabled = (page >= pages);

            // re-bind delete buttons (SweetAlert jika ada)
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
          </div>`;
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
                        if (confirm('Yakin hapus "' + title + '"?')) window.location.href = url;
                    }
                });
            });
        }

        $search.addEventListener('input', () => {
            page = 1;
            render();
        });
        $sort.addEventListener('change', () => {
            page = 1;
            render();
        });
        $size.addEventListener('change', () => {
            page = 1;
            render();
        });
        $prev.addEventListener('click', () => {
            if (page > 1) {
                page--;
                render();
            }
        });
        $next.addEventListener('click', () => {
            page++;
            render();
        });

        render();
    })();
</script>

<?= $this->endSection(); ?>