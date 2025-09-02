<?php
$errors = session()->getFlashdata('errors') ?? [];
$msg    = session()->getFlashdata('msg');
$error  = session()->getFlashdata('error');
$success  = session()->getFlashdata('success')
?>
<?php if ($msg): ?><div class="alert alert-success"><?= esc($msg) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= esc($error) ?></div><?php endif; ?>
<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= esc($success) ?></div><?php endif; ?>