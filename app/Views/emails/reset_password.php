<!doctype html>
<html>
  <body>
    <p>Halo,</p>
    <p>Kami menerima permintaan reset password untuk akun: <b><?= esc($email) ?></b>.</p>
    <p>Klik tautan berikut untuk mengatur password baru (berlaku <?= esc($ttl) ?> menit):</p>
    <p><a href="<?= esc($resetLink) ?>"><?= esc($resetLink) ?></a></p>
    <hr>
    <small>Abaikan email ini jika Anda tidak merasa meminta reset password.</small>
  </body>
</html>
