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

}
