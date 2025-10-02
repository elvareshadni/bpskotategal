<?= $this->extend('Admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-4 text-gray-800">Data Kunjungan Website</h1>

<div class="card shadow mb-4">
    <div class="card-body">

        <!-- Toolbar -->
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
            <div class="input-group" style="max-width:420px;">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input id="searchInput" type="text" class="form-control" placeholder="Cari ID, Username, Tanggal…">
            </div>

            <div class="d-flex flex-wrap gap-2 ms-auto">
                <select id="sortSelect" class="form-select">
                    <option value="newest" selected>Urutkan: Berdasarkan yang Terbaru</option>
                    <option value="oldest">Urutkan: Berdasarkan yang Terlama</option>
                    <option value="name">Urutkan: Berdasarkan Nama (A–Z)</option>
                </select>

                <select id="durasiSelect" class="form-select">
                    <option value="none" selected>— Durasi —</option>
                    <option value="longest">Berdasarkan Durasi Pemakaian Terlama</option>
                    <option value="shortest">Durasi Pemakaian Tercepat</option>
                </select>

                <select id="pageSize" class="form-select">
                    <option value="10" selected>Tampilkan 10</option>
                    <option value="25">Tampilkan 25</option>
                    <option value="50">Tampilkan 50</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Tanggal</th>
                        <th>Login Time</th>
                        <th>Logout Time</th>
                        <th>Durasi Waktu</th>
                    </tr>
                </thead>
                <tbody id="rowsBody"><!-- diisi oleh JS --></tbody>
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

<?php
// Siapkan data ke JSON agar bisa diolah di sisi klien
$rowsJson = [];
if (!empty($kunjungan)) {
    foreach ($kunjungan as $r) {
        $login  = $r['login_time']  ?? '';
        $logout = $r['logout_time'] ?? '';
        $tanggal = $login ? date('Y-m-d', strtotime($login)) : '';
        $rowsJson[] = [
            'user_id'      => (string)($r['user_id'] ?? ''),
            'username'     => (string)($r['username'] ?? ''),
            'tanggal'      => $tanggal,
            'login_time'   => (string)$login,
            'logout_time'  => (string)$logout,
            'durasi_waktu' => (string)($r['durasi_waktu'] ?? ''), // contoh: "01:23:45"
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
        const $info = document.getElementById('pageInfo');
        const $prev = document.getElementById('prevBtn');
        const $next = document.getElementById('nextBtn');

        const $search = document.getElementById('searchInput');
        const $sort = document.getElementById('sortSelect');
        const $durasi = document.getElementById('durasiSelect');
        const $size = document.getElementById('pageSize');

        let page = 1;

        // Konversi string durasi ke detik. Mendukung "HH:MM:SS" atau "X jam Y menit Z detik"
        function durasiToSeconds(s) {
            if (!s) return 0;
            s = String(s).trim();

            if (s.includes(':')) {
                const parts = s.split(':').map(x => parseInt(x, 10) || 0);
                let h = 0,
                    m = 0,
                    sec = 0;
                if (parts.length === 3)[h, m, sec] = parts;
                else if (parts.length === 2)[m, sec] = parts;
                return h * 3600 + m * 60 + sec;
            }

            // tangani "1 jam 2 menit 3 detik" (opsional)
            const num = (re) => {
                const m = s.match(re);
                return m ? parseInt(m[1], 10) : 0;
            };
            const h = num(/(\d+)\s*j(?:am)?/i);
            const m = num(/(\d+)\s*m(?:enit)?/i);
            const d = num(/(\d+)\s*d(?:etik)?/i);
            if (h || m || d) return h * 3600 + m * 60 + d;

            // fallback: angka polos = detik
            const only = parseInt(s.replace(/[^\d]/g, ''), 10);
            return isNaN(only) ? 0 : only;
        }

        function escapeHtml(s) {
            return String(s ?? '').replace(/[&<>"']/g, m => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            } [m]));
        }

        function filtered() {
            const q = ($search.value || '').trim().toLowerCase();
            if (!q) return data.slice();

            return data.filter(r =>
                (r.user_id || '').toLowerCase().includes(q) ||
                (r.username || '').toLowerCase().includes(q) ||
                (r.tanggal || '').toLowerCase().includes(q) ||
                (r.login_time || '').toLowerCase().includes(q) ||
                (r.logout_time || '').toLowerCase().includes(q)
            );
        }

        function sorted(rows) {
            // Sort utama (tanggal login / nama)
            switch ($sort.value) {
                case 'name':
                    rows.sort((a, b) => (a.username || '').localeCompare(b.username || '', 'id'));
                    break;
                case 'oldest':
                    rows.sort((a, b) => new Date(a.login_time || 0) - new Date(b.login_time || 0));
                    break;
                default: // newest
                    rows.sort((a, b) => new Date(b.login_time || 0) - new Date(a.login_time || 0));
            }

            // Jika filter durasi aktif → override urutan dengan durasi
            if ($durasi.value === 'longest') {
                rows.sort((a, b) => durasiToSeconds(b.durasi_waktu) - durasiToSeconds(a.durasi_waktu));
            } else if ($durasi.value === 'shortest') {
                rows.sort((a, b) => durasiToSeconds(a.durasi_waktu) - durasiToSeconds(b.durasi_waktu));
            }
            return rows;
        }

        function render() {
            const perPage = parseInt($size.value, 10) || 10;
            let rows = sorted(filtered());

            const total = rows.length;
            const pages = Math.max(1, Math.ceil(total / perPage));
            if (page > pages) page = pages;

            const start = (page - 1) * perPage;
            const slice = rows.slice(start, start + perPage);

            if (!slice.length) {
                $body.innerHTML = `<tr><td colspan="7" class="text-center text-muted">Tidak ada data.</td></tr>`;
            } else {
                let html = '';
                slice.forEach((r, i) => {
                    const no = start + i + 1;
                    html += `<tr>
          <td>${no}</td>
          <td>${escapeHtml(r.user_id)}</td>
          <td>${escapeHtml(r.username)}</td>
          <td>${escapeHtml(r.tanggal)}</td>
          <td>${escapeHtml(r.login_time)}</td>
          <td>${escapeHtml(r.logout_time)}</td>
          <td>${escapeHtml(r.durasi_waktu)}</td>
        </tr>`;
                });
                $body.innerHTML = html;
            }

            const showFrom = total ? (start + 1) : 0;
            const showTo = total ? (start + slice.length) : 0;
            $info.textContent = `Menampilkan ${showFrom}–${showTo} dari ${total} data`;

            $prev.disabled = (page <= 1);
            $next.disabled = (page >= pages);
        }

        // Events
        $search.addEventListener('input', () => {
            page = 1;
            render();
        });
        $sort.addEventListener('change', () => {
            page = 1;
            render();
        });
        $durasi.addEventListener('change', () => {
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