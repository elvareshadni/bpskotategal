<?php

namespace App\Controllers;

use App\Models\InfografisModel;
use App\Models\CarouselModel; 
use App\Models\UserModel;

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
        $userModel = new UserModel();
        $userId    = session()->get('user_id'); // ambil id dari session login

        if (!$userId) {
            return redirect()->to('/login');
        }

        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('errors', ['User tidak ditemukan']);
        }

        return view('user/profile', [
            'title'      => 'My Profile',
            'user'       => $user,
            'validation' => \Config\Services::validation(),
        ]);
    }

    public function updateProfile()
    {
        $userModel  = new UserModel();
        $validation = \Config\Services::validation();
        $userId     = session()->get('user_id');

        $rules = [
            'username' => 'required|min_length[3]|max_length[50]',
            'email'    => 'required|valid_email',
            'phone'    => 'permit_empty|min_length[6]|max_length[20]',
            'photo'    => 'if_exist|max_size[photo,1024]|is_image[photo]|mime_in[photo,image/jpg,image/jpeg,image/png]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'phone'    => $this->request->getPost('phone'),
        ];

        // handle foto
        $file = $this->request->getFile('photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/profile', $newName);
            $data['photo'] = 'uploads/profile/' . $newName;
        }

        $userModel->update($userId, $data);

        return redirect()->route('user.profile')->with('msg', 'Profil berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $userModel  = new UserModel();
        $validation = \Config\Services::validation();
        $userId     = session()->get('user_id');

        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $user = $userModel->find($userId);

        // cek password lama
        if (! password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('errors', ['Password lama salah']);
        }

        // update password baru
        $userModel->update($userId, [
            'password' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT)
        ]);

        return redirect()->route('user.profile')->with('msg', 'Password berhasil diubah.');
    }
}
