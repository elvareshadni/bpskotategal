<?php

namespace App\Controllers;

use App\Models\PasswordResetModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;
use App\Models\UserModel;
use App\Models\KunjunganModel;


class Home extends BaseController
{
    // Halaman Utama
    public function index(): string
    {
        return view('User/dashboard');
    }

    // ==========================
    // AUTH
    // ==========================

    // Form Login
    public function login()
    {
        // jika masih login dan belum idle-logout, arahkan ke dashboard
        if (session()->get('logged_in')) {
            return redirect()->to((session()->get('role') === 'admin') ? '/admin' : '/user');
        }
        return view('Auth/login');
    }

    // Proses Login (validasi manual)
    public function doLogin()
    {
        $login    = trim($this->request->getPost('login')
            ?? $this->request->getPost('username')
            ?? $this->request->getPost('email')
            ?? '');
        $password = (string) $this->request->getPost('password');

        $errors = [];

        // Required
        if ($login === '') {
            $errors['login']   = 'Username atau email wajib diisi.';
        }
        if ($password === '') {
            $errors['password'] = 'Password wajib diisi.';
        }

        // Panjang wajar, cegah input aneh
        if ($login !== '' && mb_strlen($login) > 100) {
            $errors['login'] = 'Username/Email terlalu panjang.';
        }
        if ($password !== '' && mb_strlen($password) < 6) {
            $errors['password'] = 'Password minimal 6 karakter.';
        }

        if ($errors) {
            return redirect()->to('/login')->with('errors', $errors)->withInput();
        }

        $userModel = new UserModel();

        // Cari berdasarkan username ATAU email
        $user = $userModel->where('username', $login)
            ->orWhere('email', $login)
            ->first();

        if (!$user) {
            return redirect()->back()
                ->with('errors', ['login' => 'Username/Email atau password salah.'])
                ->withInput();
        }

        // >>> BLOKIR: akun tanpa password (akun Google murni) tidak boleh pakai form login
        if (empty($user['password'])) {
            return redirect()->back()
                ->with('errors', [
                    'login' =>
                    'Akun ini dibuat via Google Sign-In dan belum memiliki password. ' .
                        'Silakan masuk memakai tombol "Sign in with Google", atau atur password terlebih dulu di My Profile.'
                ])
                ->withInput();
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()
                ->with('errors', ['login' => 'Username/Email atau password salah.'])
                ->withInput();
        }

        // Simpan session
        session()->set([
            'user_id'   => $user['id'],
            'username'  => $user['username'],
            'role'      => $user['role'] ?? 'user',
            'photo'     => $user['photo'] ?? 'img/default.png',
            'fullname'  => $user['fullname'],
            'logged_in' => true,
        ]);

        // --- Kunci kunjungan lama bila masih terbuka (logout_time NULL) ---
        try {
            $role = $user['role'] ?? 'user';
            if ($role === 'user') {
                $km = new KunjunganModel();
                $open = $km->where('user_id', $user['id'])
                    ->where('logout_time', null)
                    ->orderBy('id', 'DESC')->first();
                if ($open) {
                    $logout = date('Y-m-d H:i:s');
                    $durasi = (new \DateTime($open['login_time']))
                        ->diff(new \DateTime($logout))->format('%H:%I:%S');
                    $km->update($open['id'], [
                        'logout_time'  => $logout,
                        'durasi_waktu' => $durasi,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Auto-close kunjungan lama gagal: {0}', [$e->getMessage()]);
        }

        // === Catat kunjungan (login) ===
        try {
            $role = $user['role'] ?? 'user';
            if ($role === 'user') {         //Validasi hanya User yang ditangkap
                $kunjungan = new KunjunganModel();
                $now = date('Y-m-d H:i:s');

                $kunjungan->insert([
                    'user_id'      => session()->get('user_id'),
                    'username'     => session()->get('username'),
                    'login_time'   => $now,
                    'logout_time'  => null,
                    'durasi_waktu' => null,
                ]);

                // simpan id baris kunjungan ke session → dipakai saat logout
                session()->set('visit_row_id', $kunjungan->getInsertID());

                // opsional: untuk idle timeout via filter nantinya
                session()->set('last_activity', time());
            } else {
                // pastikan admin tidak punya visit_row_id agar tidak ter-update saat logout
                session()->remove('visit_row_id');
            }
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mencatat kunjungan login: {0}', [$e->getMessage()]);
        }

        // Arahkan sesuai role
        if (($user['role'] ?? 'user') === 'admin') {
            return redirect()->to('/admin')->with('success', 'Anda berhasil login sebagai Admin, selamat datang ' . $user['username'] . '!');
        }
        return redirect()->to('/user')->with('success', 'Anda berhasil login sebagai User, selamat datang ' . $user['username'] . '!');
    }

    // Form Register
    public function register()
    {
        return view('Auth/register');
    }

    // Proses Register (validasi manual lengkap)
    public function doRegister()
    {
        $username         = trim((string) $this->request->getPost('username'));
        $email            = strtolower(trim((string) $this->request->getPost('email')));
        $password         = (string) $this->request->getPost('password');
        $confirmPassword  = (string) $this->request->getPost('confirm_password');
        $fullname         = trim((string) $this->request->getPost('fullname'));

        $errors = [];

        // Username
        if ($username === '') {
            $errors['username'] = 'Username wajib diisi.';
        } elseif (mb_strlen($username) < 3) {
            $errors['username'] = 'Username minimal 3 karakter.';
        } elseif (mb_strlen($username) > 50) {
            $errors['username'] = 'Username maksimal 50 karakter.';
        } else {
            // Unik di DB
            $userModel = new UserModel();
            if ($userModel->where('username', $username)->first()) {
                $errors['username'] = 'Username sudah digunakan.';
            }
        }

        // Email
        if ($email === '') {
            $errors['email'] = 'Email wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid.';
        } else {
            // Unik di DB
            $userModel = new UserModel();
            if ($userModel->where('email', $email)->first()) {
                $errors['email'] = 'Email sudah digunakan.';
            }
        }

        // Password
        if ($password === '') {
            $errors['password'] = 'Password wajib diisi.';
        } elseif (mb_strlen($password) < 6) {
            $errors['password'] = 'Password minimal 6 karakter.';
        } /*elseif (!$this->isStrongPassword($password)) {
            $errors['password'] = 'Password harus mengandung huruf kecil, huruf besar, dan angka.';
        }*/

        // Konfirmasi password
        if ($confirmPassword === '') {
            $errors['confirm_password'] = 'Konfirmasi password wajib diisi.';
        } elseif ($confirmPassword !== $password) {
            $errors['confirm_password'] = 'Konfirmasi password tidak sama dengan Password.';
        }

        // Fullname (opsional, batasi panjang saja)
        if ($fullname !== '' && mb_strlen($fullname) > 100) {
            $errors['fullname'] = 'Nama lengkap terlalu panjang.';
        }

        if ($errors) {
            return redirect()->to('/register')->with('errors', $errors)->withInput();
        }

        // Simpan
        $userModel = new UserModel();
        try {
            $userModel->save([
                'username' => $username,
                'email'    => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'fullname' => $fullname,
                'role'     => 'user',
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Register error: {0}', [$e->getMessage()]);
            return redirect()->back()->withInput()->with('errors', ['global' => 'Gagal mendaftar. Coba lagi.']);
        }

        return redirect()->to('/login')->with('success', 'Pendaftaran berhasil, silakan login!');
    }

    // Lupa Password
    public function forget()
    {
        return view('Auth/forget');
    }

    // POST: Kirim email reset password (token 30 menit) — validasi manual
    public function sendReset(): RedirectResponse
    {
        $emailAddr = strtolower(trim((string) $this->request->getPost('email')));
        $errors    = [];

        if ($emailAddr === '') {
            $errors['email'] = 'Email wajib diisi.';
        } elseif (!filter_var($emailAddr, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid.';
        }

        if ($errors) {
            return redirect()->back()->with('errors', $errors)->withInput();
        }

        $user = (new UserModel())->where('email', $emailAddr)->first();

        // Jika email TIDAK ditemukan
        if (!$user) {
            return redirect()->to(base_url('forget'))
                ->with('error', 'Email tersebut tidak pernah terdaftar, silakan Register/Daftar terlebih dahulu.')
                ->withInput();
        }

        // Jika email ditemukan maka Buat token + simpan hash
        $resetModel = new PasswordResetModel();
        $resetModel->purgeExpired();

        $tokenPlain = bin2hex(random_bytes(16)); // 32 hex
        // matikan token lama aktif
        $resetModel->where('email', $emailAddr)
            ->where('used_at', null)
            ->set(['used_at' => date('Y-m-d H:i:s')])
            ->update();

        $resetModel->createToken($emailAddr, $tokenPlain, 30);

        $resetLink = base_url('reset-password') . '?' . http_build_query([
            'email' => $emailAddr,
            'token' => $tokenPlain,
        ]);

        // Kirim email
        $email = Services::email();
        $email->setFrom('bpstegalsystem@gmail.com', 'BPS Kota Tegal System');
        $email->setTo($emailAddr);
        $email->setSubject('[BPS Kota Tegal] Reset Password');
        $email->setMessage(view('emails/reset_password', [
            'resetLink' => $resetLink,
            'email'     => $emailAddr,
            'ttl'       => 30,
        ]));

        if (! $email->send()) {
            log_message('error', 'Gagal kirim reset email ke {0}: {1}', [
                $emailAddr,
                print_r($email->printDebugger(['headers', 'subject', 'body']), true)
            ]);
            return redirect()->back()->with('errors', ['global' => 'Gagal mengirim email reset. Coba lagi nanti.']);
        }

        return redirect()->to(base_url('forget'))->with('success', 'Tautan reset sudah dikirim ke email tersebut. Periksa inbox/spam.');
    }

    // GET: Halaman reset (via link email)
    public function reset()
    {
        $email = $this->request->getGet('email');
        $token = $this->request->getGet('token');

        if (!$email || !$token) {
            return redirect()->to(base_url('login'))->with('errors', ['global' => 'Tautan reset tidak valid.']);
        }

        return view('Auth/reset_password', [
            'title' => 'Reset Password',
            'email' => $email,
            'token' => $token,
        ]);
    }

    // POST: Simpan password baru (validasi manual)
    public function doReset(): RedirectResponse
    {
        $emailAddr       = strtolower(trim((string) $this->request->getPost('email')));
        $token           = trim((string) $this->request->getPost('token'));
        $newPassword     = (string) $this->request->getPost('new_password');
        $confirmPassword = (string) $this->request->getPost('confirm_password');

        $errors = [];

        // Email
        if ($emailAddr === '') {
            $errors['email'] = 'Email wajib diisi.';
        } elseif (!filter_var($emailAddr, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid.';
        }

        // Token
        if ($token === '') {
            $errors['token'] = 'Token wajib diisi.';
        } elseif (!ctype_xdigit($token) || strlen($token) !== 32) {
            $errors['token'] = 'Token tidak valid.';
        }

        // Password baru
        if ($newPassword === '') {
            $errors['new_password'] = 'Password baru wajib diisi.';
        } elseif (mb_strlen($newPassword) < 6) {
            $errors['new_password'] = 'Password baru minimal 6 karakter.';
        } /*elseif (!$this->isStrongPassword($newPassword)) {
            $errors['new_password'] = 'Password baru harus mengandung huruf kecil, huruf besar, dan angka.';
        }*/

        if ($confirmPassword === '') {
            $errors['confirm_password'] = 'Konfirmasi password wajib diisi.';
        } elseif ($confirmPassword !== $newPassword) {
            $errors['confirm_password'] = 'Konfirmasi password tidak sama dengan Password baru.';
        }

        if ($errors) {
            return redirect()->back()->with('errors', $errors)->withInput();
        }

        // Validasi token ke DB
        $resetModel = new PasswordResetModel();
        $row = $resetModel->validateToken($emailAddr, $token);
        if (!$row) {
            return redirect()->to(base_url('forget'))->with('errors', ['global' => 'Token tidak valid atau sudah kedaluwarsa.']);
        }

        // Update password user
        $userModel = new UserModel();
        $user = $userModel->where('email', $emailAddr)->first();
        if (!$user) {
            return redirect()->to(base_url('forget'))->with('errors', ['global' => 'Akun tidak ditemukan.']);
        }

        $ok = $userModel->update($user['id'], [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        if (! $ok) {
            return redirect()->back()->with('errors', ['global' => 'Gagal memperbarui password. Coba lagi.']);
        }

        // Tandai token used
        $resetModel->markUsed((int) $row['id']);

        return redirect()->to(base_url('login'))->with('msg', 'Password berhasil direset. Silakan login.');
    }

    // GET: Lupa Password (legacy)
    public function change_password()
    {
        return redirect()->to(base_url('forget'));
    }

    public function logout()
    {
        try {
            $visitId = session()->get('visit_row_id');
            if ($visitId) {
                $kunjungan = new KunjunganModel();
                $row = $kunjungan->find($visitId);

                // hanya update jika barisnya ada & belum terisi logout_time
                if ($row && empty($row['logout_time'])) {
                    $logout = date('Y-m-d H:i:s');

                    // hitung durasi HH:MM:SS
                    $dtIn  = new \DateTime($row['login_time']);
                    $dtOut = new \DateTime($logout);
                    $durasi = $dtIn->diff($dtOut)->format('%H:%I:%S');

                    $kunjungan->update($visitId, [
                        'logout_time'  => $logout,
                        'durasi_waktu' => $durasi,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mengunci kunjungan saat logout: {0}', [$e->getMessage()]);
            // kita lanjutkan logout meski gagal mencatat durasi
        }

        session()->remove('visit_row_id');
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda berhasil logout.');
    }

    // ==========================
    // Utilitas Validasi Lokal
    // ==========================
    /**
     * Password kuat: minimal ada huruf kecil, huruf besar, dan angka.
     * (Silakan tambah regex/syarat lain bila perlu)
     */
    /*private function isStrongPassword(string $pwd): bool
    {
        $hasLower = preg_match('/[a-z]/', $pwd);
        $hasUpper = preg_match('/[A-Z]/', $pwd);
        $hasDigit = preg_match('/\d/',    $pwd);
        return (bool) ($hasLower && $hasUpper && $hasDigit);
    }*/
}
