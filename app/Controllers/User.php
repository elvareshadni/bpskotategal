<?php

namespace App\Controllers;

class User extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Home | BPS Kota Tegal'
        ];
        return view('user/dashboard', $data);
    }

    public function beranda()
    {
        $data = [
            'title' => 'Beranda'
        ];
        return view('user/home', $data);
    }

    public function listInfografis()
    {
        return view('user/list');
    }

    public function list()
    {
        $data = [
            'title' => 'Daftar Berita Resmi Statistik'
        ];
        return view('user/list', $data);
    }

    public function detail($id = null)
    {
        // nanti bisa pakai $id untuk ambil detail dari database
        $data = [
            'title' => 'Detail Berita Resmi Statistik'
        ];
        return view('user/detail', $data);
    }

    public function profile()
    {
        // contoh data user aktif; ganti dengan session/auth milikmu
        $user = [
            'username' => 'userdemo',
            'email'    => 'user@contoh.go.id',
            'phone'    => '08123456789',
            'photo'    => null, // path foto jika ada
        ];

        return view('User/profile', [
            'title' => 'My Profile',
            'user'  => $user,
            'validation' => \Config\Services::validation(),
        ]);
    }

    public function updateProfile()
    {
        $validation = \Config\Services::validation();
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]',
            'email'    => 'required|valid_email',
            'phone'    => 'permit_empty|min_length[6]|max_length[20]',
            'photo'    => 'uploaded[photo]|max_size[photo,1024]|is_image[photo]|mime_in[photo,image/jpg,image/jpeg,image/png]',
        ];

        // foto opsional: jika tidak diupload, hapus rule uploaded[photo]
        if ($this->request->getFile('photo')?->getError() === UPLOAD_ERR_NO_FILE) {
            unset($rules['photo']);
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Simpan data (mock). Integrasikan dengan UserModel punyamu.
        // $file = $this->request->getFile('photo');
        // if ($file && $file->isValid()) { ... simpan ke /writable/uploads ... }

        return redirect()->route('user.profile')->with('msg', 'Profil berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $validation = \Config\Services::validation();
        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Validasi password lama & update password di DB sesuai sistem auth milikmu.
        // if (! password_verify($this->request->getPost('current_password'), $hashLama)) { ... }

        return redirect()->route('user.profile')->with('msg', 'Password berhasil diubah.');
    }
}
