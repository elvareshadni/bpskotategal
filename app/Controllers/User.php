<?php

namespace App\Controllers;

<<<<<<< HEAD
use App\Models\InfografisModel;
use App\Models\CarouselModel; // 👈 tambahkan model carousel

class User extends BaseController 
=======
class User extends BaseController
>>>>>>> ac3bfa8de96bd057f22d001c5e926d0f1b4e1485
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
<<<<<<< HEAD
{
    $userModel = new \App\Models\UserModel();
    $user = $userModel->find(session()->get('user_id'));

    $data = [
        'title' => 'My Profile',
        'user'  => $user
    ];

    return view('user/profile', $data);
}

public function updateProfile()
{
    $userModel = new \App\Models\UserModel();
    $id = session()->get('user_id');

    $data = [
        'username' => $this->request->getPost('username'),
        'email'    => $this->request->getPost('email'),
        'phone'    => $this->request->getPost('phone'),
    ];

    // upload foto
    $photo = $this->request->getFile('photo');
    if ($photo && $photo->isValid() && !$photo->hasMoved()) {
        $newName = $photo->getRandomName();
        $photo->move('uploads/profile', $newName);
        $data['photo'] = 'uploads/profile/' . $newName;
    }

    $userModel->update($id, $data);
    return redirect()->back()->with('msg', 'Profil berhasil diperbarui');
}

public function updatePassword()
    {
        $userModel = new \App\Models\UserModel();
        $id = session()->get('user_id');

        $current = $this->request->getPost('current_password');
        $new     = $this->request->getPost('new_password');
        $confirm = $this->request->getPost('confirm_password');

        $user = $userModel->find($id);

        if (!password_verify($current, $user['password'])) {
            return redirect()->back()->with('errors', ['Password sekarang salah']);
        }
        if ($new !== $confirm) {
            return redirect()->back()->with('errors', ['Konfirmasi password tidak cocok']);
        }

        $userModel->update($id, ['password' => password_hash($new, PASSWORD_DEFAULT)]);
        return redirect()->back()->with('msg', 'Password berhasil diperbarui');
    }

=======
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
>>>>>>> ac3bfa8de96bd057f22d001c5e926d0f1b4e1485
}
