<?php

namespace App\Controllers;

use App\Models\InfografisModel;
use App\Models\CarouselModel;
use App\Models\UserModel;

class Admin extends BaseController
{
    public function index()
    {
        return view('Admin/index');
    }

    public function __construct()
    {
        // Proteksi: wajib admin
        if (session()->get('role') !== 'admin') {
            redirect()->to('/login')->send();
            exit;
        }
    }

    public function profile()
    {
        $userModel = new UserModel();
        $userId    = session()->get('user_id'); // ambil id dari session login


        if (!$userId) {
            return redirect()->to('/login');
        }

        $user = $userModel->find($userId);
        if (!$user) {
            return redirect()->to('/login')
                ->with('errors', ['User tidak ditemukan'])
                ->with('error', 'User tidak ditemukan');
        }

        return view('Admin/profile', [
            'title' => 'My Profile',
            'user'  => $user,
        ]);
    }

    /**
     * Update data profil Admin (validasi manual, tanpa Validation Service)
     */
    public function updateProfile()
    {
        $userModel = new UserModel();
        $userId    = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $current = $userModel->find($userId);
        if (!$current) {
            return redirect()->to('/login')
                ->with('errors', ['User tidak ditemukan'])
                ->with('error', 'User tidak ditemukan');
        }

        $username = trim((string) $this->request->getPost('username'));
        $fullname = trim((string) $this->request->getPost('fullname'));
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $phone    = trim((string) $this->request->getPost('phone'));
        $file     = $this->request->getFile('photo');

        $errors = [];

        // --- Username ---
        if ($username === '') {
            $errors['username'] = 'Username wajib diisi.';
        } elseif (mb_strlen($username) < 3) {
            $errors['username'] = 'Username minimal 3 karakter.';
        } elseif (mb_strlen($username) > 50) {
            $errors['username'] = 'Username maksimal 50 karakter.';
        } elseif ($username !== (string) $current['username']) {
            // cek unik hanya jika berubah
            $exists = $userModel->where('username', $username)
                ->where('id !=', $userId)
                ->first();
            if ($exists) {
                $errors['username'] = 'Username sudah digunakan.';
            }
        }

        // --- Fullname ---
        if ($fullname === '') {
            $errors['fullname'] = 'Nama lengkap wajib diisi.';
        } elseif (mb_strlen($fullname) < 3) {
            $errors['fullname'] = 'Nama lengkap minimal 3 karakter.';
        }

        // --- Email ---
        if ($email === '') {
            $errors['email'] = 'Email wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid.';
        } elseif (strcasecmp($email, (string) $current['email']) !== 0) {
            // cek unik hanya jika berubah (case-insensitive)
            $exists = $userModel->where('email', $email)
                ->where('id !=', $userId)
                ->first();
            if ($exists) {
                $errors['email'] = 'Email sudah digunakan.';
            }
        }

        // --- Phone (opsional) ---
        if ($phone !== '') {
            if (mb_strlen($phone) < 6 || mb_strlen($phone) > 20) {
                $errors['phone'] = 'No. HP minimal 6 dan maksimal 20 karakter.';
            }
            // Jika ingin hanya angka/spasi/-/+ :
            // elseif (!preg_match('/^\+?[0-9\s\-]+$/', $phone)) {
            //     $errors['phone'] = 'No. HP hanya boleh berisi angka, spasi, tanda minus, dan awalan +.';
            // }
        }

        // --- Foto (opsional) ---
        $photoPath = null;
        if ($file && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            // ukuran ≤ 1MB
            if ($file->getSize() > 1024 * 1024) {
                $errors['photo'] = 'Ukuran foto maksimal 1MB.';
            }
            // tipe
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

        // data update
        $data = [
            'username' => $username,
            'fullname' => $fullname,
            'email'    => $email,
            'phone'    => $phone,
        ];

        // simpan foto jika ada
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

            // hapus foto lama (jika ada)
            if (!empty($current['photo'])) {
                $old = FCPATH . ltrim($current['photo'], '/');
                if (is_file($old)) {
                    @unlink($old);
                }
            }
        }

        try {
            $userModel->update($userId, $data);
        } catch (\Throwable $e) {
            log_message('error', 'Admin updateProfile error: {0}', [$e->getMessage()]);
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

        return redirect()->route('admin.profile')->with('msg', 'Profil berhasil diperbarui!');
    }

    /**
     * Ubah password Admin (validasi manual, tanpa Validation Service)
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

        if ($currentPassword === '') {
            $errors['current_password'] = 'Password sekarang wajib diisi.';
        }
        if ($newPassword === '') {
            $errors['new_password'] = 'Password baru wajib diisi.';
        } elseif (mb_strlen($newPassword) < 6) {
            $errors['new_password'] = 'Password baru minimal 6 karakter.';
        }
        // Jika ingin aturan kuat:
        // elseif (!$this->isStrongPassword($newPassword)) {
        //     $errors['new_password'] = 'Password baru harus mengandung huruf kecil, huruf besar, dan angka.';
        // }

        if ($confirmPassword === '') {
            $errors['confirm_password'] = 'Konfirmasi password wajib diisi.';
        } elseif ($confirmPassword !== $newPassword) {
            $errors['confirm_password'] = 'Konfirmasi password tidak sama dengan Password baru.';
        }

        if ($errors) {
            return redirect()->back()->withInput()
                ->with('errors', $errors)
                ->with('error', reset($errors));
        }

        $user = $userModel->find($userId);
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return redirect()->back()->withInput()
                ->with('errors', ['current_password' => 'Password sekarang salah.'])
                ->with('error', 'Password sekarang salah.');
        }

        try {
            $userModel->update($userId, [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Admin updatePassword error: {0}', [$e->getMessage()]);
            return redirect()->back()->withInput()
                ->with('errors', ['global' => 'Gagal mengubah password. Coba lagi.'])
                ->with('error', 'Gagal mengubah password. Coba lagi.');
        }

        return redirect()->route('admin.profile')->with('msg', 'Password berhasil diperbarui.');
    }

    // --- Kelola Data ---

    public function dataIndikator()
    {
        return view('Admin/kelola_data_indikator');
    }

    public function laporanKunjungan()
    {
        $kunjunganModel = new \App\Models\KunjunganModel();
        $data['kunjungan'] = $kunjunganModel->findAll();

        return view('Admin/laporan_kunjungan', $data);
    }

    // --- Infografis ---
    public function addInfografis()
    {
        return view('Admin/tambah_infografis');
    }

    public function saveInfografis()
    {
        $infografisModel = new InfografisModel();

        $file = $this->request->getFile('infografisImage');
        $newName = $file ? $file->getRandomName() : null;

        if ($file && $file->isValid() && $newName) {
            $file->move('img', $newName);
        }

        $infografisModel->save([
            'judul'     => $this->request->getPost('judulInfografis'),
            'deskripsi' => $this->request->getPost('deskripsiInfografis'),
            'gambar'    => $newName,
            'tanggal'   => date('Y-m-d')
        ]);

        return redirect()->to(base_url('admin/edit-infografis/list'))->with('success', 'Infografis berhasil ditambahkan!');
    }

    // === LIST (untuk halaman edit daftar) ===
    public function listInfografis()
    {
        $m = new InfografisModel();
        $data['rows'] = $m->orderBy('id', 'DESC')->findAll();
        return view('Admin/edit_infografis_list', $data);
    }

    // === EDIT (form) ===
    public function editInfografis($id)
    {
        $m = new InfografisModel();
        $row = $m->find($id);
        if (!$row) {
            return redirect()->to(base_url('Admin/edit-infografis/list'))
                ->with('error', 'Data tidak ditemukan');
        }
        return view('Admin/edit_infografis', ['row' => $row]);
    }

    // === UPDATE (submit edit) ===
    public function updateInfografis($id)
    {
        $m = new InfografisModel();
        $row = $m->find($id);
        if (!$row) {
            return redirect()->to(base_url('Admin/edit-infografis/list'))
                ->with('error', 'Data tidak ditemukan');
        }

        $judul     = trim((string)$this->request->getPost('judulInfografis'));
        $deskripsi = trim((string)$this->request->getPost('deskripsiInfografis'));
        $tanggal   = $this->request->getPost('tanggal') ?: $row['tanggal'];

        // Upload gambar (opsional)
        $file = $this->request->getFile('infografisImage');
        $data = [
            'judul'     => $judul,
            'deskripsi' => $deskripsi,
            'tanggal'   => $tanggal,
        ];

        if ($file && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            if ($file->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->withInput()->with('error', 'Ukuran maks 2MB');
            }
            if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg'])) {
                return redirect()->back()->withInput()->with('error', 'Format harus JPG/PNG');
            }
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'img', $newName);
            $data['gambar'] = $newName;

            // hapus file lama jika ada
            if (!empty($row['gambar'])) {
                @unlink(FCPATH . 'img/' . $row['gambar']);
            }
        }

        if (!$m->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengubah data.');
        }

        return redirect()->to(base_url('Admin/edit-infografis/list'))
            ->with('success', 'Infografis berhasil diperbarui.');
    }

    // === DELETE ===
    public function deleteInfografis($id)
    {
        $m = new InfografisModel();
        $row = $m->find($id);
        if ($row && !empty($row['gambar'])) {
            @unlink(FCPATH . 'img/' . $row['gambar']);
        }
        $m->delete($id);
        return redirect()->to(base_url('Admin/edit-infografis/list'))
            ->with('success', 'Infografis berhasil dihapus.');
    }

    // ==========================
    // Utilitas (opsional)
    // ==========================
    /*private function isStrongPassword(string $pwd): bool
    {
        $hasLower = preg_match('/[a-z]/', $pwd);
        $hasUpper = preg_match('/[A-Z]/', $pwd);
        $hasDigit = preg_match('/\d/',    $pwd);
        return (bool) ($hasLower && $hasUpper && $hasDigit);
    }*/
}
