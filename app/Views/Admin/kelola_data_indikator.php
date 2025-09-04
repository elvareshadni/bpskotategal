<?= $this->extend('Admin/templates/index'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 mb-4 text-gray-800">Silahkan edit data melalui link spreadsheet di bawah ini</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <a href="https://docs.google.com/spreadsheets/d/1ohHcbmQnyH5S2SwY1B9SGa_oC64FzloE3L4F8Vk2Ito/edit?usp=sharing"
            target="_blank" class="btn btn-success">
            👉 Buka Spreadsheet
        </a>
    </div>
</div>

<?= $this->endSection(); ?>