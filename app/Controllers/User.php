<?php

namespace App\Controllers;

use App\Models\InfografisModel;
use App\Models\CarouselModel; // 👈 tambahkan model carousel

class User extends BaseController 
{
    public function index()
    {
        $infografisModel = new InfografisModel();
        $carouselModel   = new CarouselModel(); // 👈 panggil model carousel

        $data = [
            'title'      => 'Home | BPS Kota Tegal',
            'infografis' => $infografisModel->orderBy('tanggal', 'DESC')->findAll(6), // tampilkan 6 terbaru
            'carousel'   => $carouselModel->findAll() // 👈 ambil semua slide carousel
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

    public function list() 
    {
        $model = new InfografisModel();
        $data = [
            'title'      => 'Daftar Infografis',
            'infografis' => $model->orderBy('tanggal', 'DESC')->findAll()
        ];
        return view('user/list', $data);
    }

    public function detail($id) 
    {
        $model = new InfografisModel();
        $item = $model->find($id);

        if (!$item) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Data dengan ID $id tidak ditemukan");
        }

        $data = [
            'title' => $item['judul'],
            'item'  => $item
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
