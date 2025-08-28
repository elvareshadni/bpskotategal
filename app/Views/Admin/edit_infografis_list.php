<?= $this->extend('admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 text-gray-800 mb-4">Edit Infografis — Daftar</h1>

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
            <th style="width:160px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Infografis A</td>
            <td>Deskripsi singkat A</td>
            <td>info_a.png</td>
            <td>
              <a href="#" class="btn btn-sm btn-info">Edit</a>
              <a href="#" class="btn btn-sm btn-danger">Hapus</a>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td>Infografis B</td>
            <td>Deskripsi singkat B</td>
            <td>info_b.jpg</td>
            <td>
              <a href="#" class="btn btn-sm btn-info">Edit</a>
              <a href="#" class="btn btn-sm btn-danger">Hapus</a>
            </td>
          </tr>
          <!-- Tambahkan baris simulasi lain bila perlu -->
        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>
