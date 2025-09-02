<?php

namespace App\Controllers;

use App\Models\PasswordResetModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;
use App\Models\UserModel;

class Home extends BaseController
{
    // Halaman Utama
    public function index(): string
    {
        return view('user/dashboard');
    }

    // ==========================
    // AUTH
    // ==========================

    // Form Login
    public function login()
    {
        return view('auth/login');
    }

    // Proses Login
    public function doLogin()
    {
        // Ambil input "login" (boleh username ATAU email) + password
        $login    = trim($this->request->getPost('login')
            ?? $this->request->getPost('username')
            ?? $this->request->getPost('email')
            ?? '');
        $password = (string) $this->request->getPost('password');

        if ($login === '' || $password === '') {
            return redirect()->back()->with('error', 'Isi username/email dan password.')->withInput();
        }

        $userModel = new UserModel();

        // Cari berdasarkan username ATAU email
        $user = $userModel->where('username', $login)
            ->orWhere('email', $login)
            ->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Username atau E-Mail atau password salah!')->withInput();
        }

        // Simpan session
        session()->set([
            'user_id'   => $user['id'],
            'username'  => $user['username'],
            'role'      => $user['role'] ?? 'user',
            'logged_in' => true,
        ]);

        // Arahkan sesuai role
        if (($user['role'] ?? 'user') === 'admin') {
            return redirect()->to('/admin')->with('msg', 'Login admin berhasil.');      // dashboard admin
        }
        return redirect()->to('/user')->with('msg', 'Login berhasil.');           // dashboard user
    }

    // Form Register
    public function register()
    {
        return view('auth/register');
    }

    // Proses Register
    public function doRegister()
    {
        $userModel = new UserModel();

        $data = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'fullname' => $this->request->getPost('fullname'),
            'role'     => 'user',
        ];

        // Validasi sederhana (boleh kamu ganti ke Validation Service)
        if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Format email tidak valid.');
        }

        try {
            $userModel->save($data);
        } catch (\Throwable $e) {
            log_message('error', 'Register error: {0}', [$e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal mendaftar. Coba lagi.');
        }

        return redirect()->to('/login')->with('success', 'Pendaftaran berhasil, silakan login!');
    }

    // Lupa Password
    public function forget()
    {
        return view('auth/forget');
    }

    // POST: Kirim email reset password (token 30 menit)
    public function sendReset(): RedirectResponse
    {
        $validation = Services::validation();
        $rules = ['email' => 'required|valid_email'];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $emailAddr = strtolower(trim((string) $this->request->getPost('email')));

        // Cek user ada/tidak — tapi balasannya tetap generik (anti enumeration)
        $userModel = new UserModel();
        $user = $userModel->where('email', $emailAddr)->first();

        $genericMsg = 'Jika email terdaftar, tautan reset sudah dikirim. Periksa inbox/spam.';

        if (!$user) {
            return redirect()->to(base_url('forget'))->with('msg', $genericMsg);
        }

        // Buat token + simpan hash
        $resetModel = new PasswordResetModel();
        $resetModel->purgeExpired();

        $tokenPlain = bin2hex(random_bytes(16)); // 32 hex char
        $resetModel->where('email', $emailAddr)->where('used_at', null)->set(['used_at' => date('Y-m-d H:i:s')])->update(); // matikan token lama
        $resetModel->createToken($emailAddr, $tokenPlain, 30);

        $resetLink = base_url('reset-password') . '?' . http_build_query([
            'email' => $emailAddr,
            'token' => $tokenPlain,
        ]);

        // Kirim email (pakai config .env Gmail)
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
            return redirect()->back()->with('error', 'Gagal mengirim email reset. Coba lagi nanti.');
        }

        return redirect()->to(base_url('forget'))->with('msg', $genericMsg);
    }

    // GET: Halaman reset (via link email)
    public function reset()
    {
        $email = $this->request->getGet('email');
        $token = $this->request->getGet('token');

        if (!$email || !$token) {
            return redirect()->to(base_url('login'))->with('error', 'Tautan reset tidak valid.');
        }

        return view('auth/reset_password', [
            'title' => 'Reset Password',
            'email' => $email,
            'token' => $token,
        ]);
    }

    // POST: Simpan password baru
    public function doReset(): RedirectResponse
    {
        $validation = Services::validation();
        $rules = [
            'email'            => 'required|valid_email',
            'token'            => 'required',
            'new_password'     => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $emailAddr = strtolower(trim((string) $this->request->getPost('email')));
        $token     = trim((string) $this->request->getPost('token'));

        $resetModel = new PasswordResetModel();
        $row = $resetModel->validateToken($emailAddr, $token);
        if (!$row) {
            return redirect()->to(base_url('forget'))->with('error', 'Token tidak valid atau sudah kedaluwarsa.');
        }

        // Update password user
        $userModel = new UserModel();
        $user = $userModel->where('email', $emailAddr)->first();
        if (!$user) {
            return redirect()->to(base_url('forget'))->with('error', 'Akun tidak ditemukan.');
        }

        $ok = $userModel->update($user['id'], [
            'password' => password_hash((string) $this->request->getPost('new_password'), PASSWORD_DEFAULT),
        ]);

        if (! $ok) {
            return redirect()->back()->with('error', 'Gagal memperbarui password. Coba lagi.');
        }

        // Tandai token used
        $resetModel->markUsed((int) $row['id']);

        return redirect()->to(base_url('login'))->with('msg', 'Password berhasil direset. Silakan login.');
    }

    // GET: Lupa Password (legacy)
    public function change_password()
    {
        // Kalau masih ingin pakai halaman ini, arahkan ke forget atau tampilkan instruksi
        return redirect()->to(base_url('forget'));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda berhasil logout.');
    }
}
