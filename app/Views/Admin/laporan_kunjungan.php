<?= $this->extend('admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-4 text-gray-800">Data Kunjungan Website</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
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
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($kunjungan as $row): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['user_id']; ?></td>
                            <td><?= $row['username']; ?></td>
                            <td><?= date('Y-m-d', strtotime($row['login_time'])); ?></td>
                            <td><?= $row['login_time']; ?></td>
                            <td><?= $row['logout_time']; ?></td>
                            <td><?= $row['durasi_waktu']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>