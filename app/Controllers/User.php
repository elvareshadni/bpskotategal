<?php

namespace App\Controllers;

use App\Models\InfografisModel;
use App\Models\CarouselModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class User extends BaseController
{
    public function index()
    {
        $infografisModel = new InfografisModel();
        $carouselModel   = new CarouselModel();

        $data = [
            'title'      => 'Home | BPS Kota Tegal',
            'infografis' => $infografisModel->orderBy('tanggal', 'DESC')->findAll(6),
            'carousel'   => $carouselModel->findAll()
        ];
        return view('User/dashboard', $data);
    }

    public function beranda()
    {
        return view('User/home', ['title' => 'Beranda']);
    }

    public function list()
    {
        $model = new InfografisModel();
        $data = [
            'title'      => 'Daftar Infografis',
            'infografis' => $model->orderBy('tanggal', 'DESC')->findAll()
        ];
        return view('User/list', $data);
    }

    public function detail($id)
    {
        $model = new InfografisModel();
        $item = $model->find($id);

        if (!$item) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Data dengan ID $id tidak ditemukan");
        }

        return view('User/detail', [
            'title' => $item['judul'],
            'item'  => $item
        ]);
    }

    public function profile(): ResponseInterface|string
    {
        $userModel = new UserModel();
        $userId    = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('errors', ['User tidak ditemukan'])->with('error', 'User tidak ditemukan');
        }

        return view('User/profile', [
            'title'      => 'My Profile',
            'user'       => $user,
        ]);
    }

    /**
     * Update data profil (validasi manual, tanpa Validation Service)
     */
    public function updateProfile()
    {
        $userModel = new UserModel();
        $userId    = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $username = trim((string) $this->request->getPost('username'));
        $fullname = trim((string) $this->request->getPost('fullname'));
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $phone    = trim((string) $this->request->getPost('phone'));
        $file     = $this->request->getFile('photo');

        $errors = [];

        // --- Validasi username ---
        if ($username === '') {
            $errors['username'] = 'Username wajib diisi.';
        } elseif (mb_strlen($username) < 3) {
            $errors['username'] = 'Username minimal 3 karakter.';
        } elseif (mb_strlen($username) > 50) {
            $errors['username'] = 'Username maksimal 50 karakter.';
        } else {
            // Unik kecuali milik sendiri
            $exists = $userModel->where('username', $username)
                ->where('id !=', $userId)
                ->first();
            if ($exists) {
                $errors['username'] = 'Username sudah digunakan.';
            }
        }

        // --- Validasi fullname ---
        if ($fullname === '') {
            $errors['fullname'] = 'Nama lengkap wajib diisi.';
        } elseif (mb_strlen($fullname) < 3) {
            $errors['fullname'] = 'Nama lengkap minimal 3 karakter.';
        }

        // --- Validasi email ---
        if ($email === '') {
            $errors['email'] = 'Email wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid.';
        } else {
            $exists = $userModel->where('email', $email)
                ->where('id !=', $userId)
                ->first();
            if ($exists) {
                $errors['email'] = 'Email sudah digunakan.';
            }
        }

        // --- Validasi phone (opsional) ---
        if ($phone !== '') {
            if (mb_strlen($phone) < 6 || mb_strlen($phone) > 20) {
                $errors['phone'] = 'No. HP minimal 6 dan maksimal 20 karakter.';
            }
            // Jika ingin hanya angka + plus, aktifkan baris di bawah:
            // elseif (!preg_match('/^\+?[0-9\s\-]+$/', $phone)) {
            //     $errors['phone'] = 'No. HP hanya boleh berisi angka, spasi, tanda minus, dan awalan +.';
            // }
        }

        // --- Validasi foto (jika diupload) ---
        $photoPath = null;
        if ($file && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            // cek ukuran ≤ 1MB
            if ($file->getSize() > 1024 * 1024) { // bytes
                $errors['photo'] = 'Ukuran foto maksimal 1MB.';
            }
            // cek tipe
            $mimeOk = ['image/jpg', 'image/jpeg', 'image/png'];
            $mime   = $file->getMimeType();
            if (!in_array($mime, $mimeOk, true)) {
                $errors['photo'] = 'Format foto harus jpg, jpeg, atau png.';
            }
        }

        if ($errors) {
            return redirect()->back()->withInput()
                ->with('errors', $errors)
                ->with('error', reset($errors));
        }

        // Siapkan data update
        $data = [
            'username' => $username,
            'fullname' => $fullname,
            'email'    => $email,
            'phone'    => $phone,
        ];

        // Pindah file jika ada
        if ($file && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $dir = FCPATH . 'uploads/profile';
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            $newName = $file->getRandomName();
            if (!$file->move($dir, $newName)) {
                return redirect()->back()->withInput()
                    ->with('errors', ['photo' => 'Gagal menyimpan foto.'])
                    ->with('error', 'Gagal menyimpan foto.');
            }
            $photoPath = 'uploads/profile/' . $newName;
            $data['photo'] = $photoPath;

            // (Opsional) hapus foto lama
            $current = $userModel->find($userId);
            if ($current && !empty($current['photo'])) {
                $old = FCPATH . ltrim($current['photo'], '/');
                if (is_file($old)) {
                    @unlink($old);
                }
            }
        }

        // Update
        try {
            $userModel->update($userId, $data);
        } catch (\Throwable $e) {
            log_message('error', 'Update profile error: {0}', [$e->getMessage()]);
            return redirect()->back()->withInput()
                ->with('errors', ['global' => 'Gagal memperbarui profil. Coba lagi.'])
                ->with('error', 'Gagal memperbarui profil. Coba lagi.');
        }

        // >>> SINKRONKAN SESSION <<<
        if (isset($data['username'])) {
            session()->set('username', $data['username']);
        }
        if (isset($data['photo'])) {
            session()->set('photo', $data['photo']);
        }

        return redirect()->route('user.profile')->with('msg', 'Profil berhasil diperbarui.');
    }

    /**
     * Ubah password (validasi manual, tanpa Validation Service)
     */
    public function updatePassword()
    {
        $userModel = new UserModel();
        $userId    = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword     = (string) $this->request->getPost('new_password');
        $confirmPassword = (string) $this->request->getPost('confirm_password');

        $errors = [];

        $user = $userModel->find($userId);
        if (!$user) {
            return redirect()->to('/login')->with('errors', ['User tidak ditemukan'])->with('error', 'User tidak ditemukan');
        }

        $isFirstSet = empty($user['password']); // password masih NULL?

        // Validasi dasar
        if ($newPassword === '') {
            $errors['new_password'] = 'Password baru wajib diisi.';
        } elseif (mb_strlen($newPassword) < 6) {
            $errors['new_password'] = 'Password baru minimal 6 karakter.';
        }

        if ($confirmPassword === '') {
            $errors['confirm_password'] = 'Konfirmasi password wajib diisi.';
        } elseif ($confirmPassword !== $newPassword) {
            $errors['confirm_password'] = 'Konfirmasi password tidak sama dengan Password baru.';
        }

        // Jika BUKAN pertama kali → current_password wajib & harus cocok
        if (!$isFirstSet) {
            if ($currentPassword === '') {
                $errors['current_password'] = 'Password sekarang wajib diisi.';
            } elseif (!password_verify($currentPassword, (string)$user['password'])) {
                $errors['current_password'] = 'Password sekarang salah.';
            }
        }

        if ($errors) {
            return redirect()->back()->withInput()
                ->with('errors', $errors)
                ->with('error', reset($errors));
        }

        try {
            $userModel->update($userId, [
                'password'       => password_hash($newPassword, PASSWORD_DEFAULT),
                'auth_provider'  => 'local', // sekarang sudah bisa login via form
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Update password error: {0}', [$e->getMessage()]);
            return redirect()->back()->withInput()
                ->with('errors', ['global' => 'Gagal mengubah password. Coba lagi.'])
                ->with('error', 'Gagal mengubah password. Coba lagi.');
        }

        return redirect()->route('user.profile')->with('msg', $isFirstSet ? 'Password berhasil diset.' : 'Password berhasil diubah.');
    }


    // ==========================
    // Utilitas Validasi Lokal
    // ==========================
    /*private function isStrongPassword(string $pwd): bool
    {
        $hasLower = preg_match('/[a-z]/', $pwd);
        $hasUpper = preg_match('/[A-Z]/', $pwd);
        $hasDigit = preg_match('/\d/',    $pwd);
        return (bool) ($hasLower && $hasUpper && $hasDigit);
    }*/
}
