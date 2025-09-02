<?php

namespace App\Controllers;

use App\Models\InfografisModel;
use App\Models\CarouselModel;
use App\Models\UserModel;

class Admin extends BaseController
{
    public function index(): string
    {
        return view('admin/index');
    }

    public function __construct()
    {
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

        return view('admin/profile', [
            'title'      => 'My Profile',
            'user'       => $user,
            'validation' => \Config\Services::validation(),
        ]);
    }

    public function updateProfile()
    {
        $userModel = new UserModel();
        $validation = \Config\Services::validation();
        $userId = session()->get('user_id');

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

        // handle upload foto
        $file = $this->request->getFile('photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads', $newName);
            $data['photo'] = 'uploads/' . $newName;
        }

        $userModel->update($userId, $data);

        return redirect()->back()->with('msg', 'Profil berhasil diperbarui!');
    }

    public function updatePassword()
    {
        $userModel = new UserModel();
        $validation = \Config\Services::validation();
        $userId = session()->get('user_id');

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

        return redirect()->route('admin.profile')->with('msg', 'Password berhasil diperbarui.');
    }

    // --- Kelola Data ---
    public function dataIndikator(): string
    {
        return view('admin/kelola_data_indikator');
    }

    public function laporanKunjungan()
    {
        $kunjunganModel = new \App\Models\KunjunganModel();
        $data['kunjungan'] = $kunjunganModel->findAll();

        return view('admin/laporan_kunjungan', $data);
    }



    // // --- carousel ---
    // public function carousel(): string
    // {
    //     $carouselModel = new CarouselModel();
    //     $data['carousels'] = $carouselModel->findAll();

    //     return view('admin/carousel', $data);
    // }


    // public function carouselAdd(): string
    // {
    //     return view('admin/carousel_add');
    // }

    //     public function carouselSave()
    // {
    //     $carouselModel = new CarouselModel();

    //     $file  = $this->request->getFile('carouselImage');
    //     $judul = $this->request->getPost('judulcarousel');

    //     if ($file && $file->isValid() && !$file->hasMoved()) {
    //         $newName = $file->getRandomName();
    //         $file->move(FCPATH . 'img', $newName);

    //         $carouselModel->insert([
    //             'judul'   => $judul,
    //             'gambar'  => $newName,
    //             'posisi'  => 'center'
    //         ]);
    //     }

    //     return redirect()->to(base_url('admin/listcarousel'))
    //         ->with('success', 'Carousel berhasil ditambahkan!');
    // }

    // --- Infografis ---
    public function infografis(): string
    {
        return view('admin/infografis');
    }

    public function infografisAdd(): string
    {
        return view('admin/infografis_add');
    }

    public function infografisSave()
    {
        $infografisModel = new InfografisModel();

        $file = $this->request->getFile('infografisImage');
        $newName = $file->getRandomName();
        $file->move('img', $newName);

        $infografisModel->save([
            'judul'     => $this->request->getPost('judulInfografis'),
            'deskripsi' => $this->request->getPost('deskripsiInfografis'),
            'gambar'    => $newName,
            'tanggal'   => date('Y-m-d')
        ]);

        return redirect()->to(base_url('admin/infografis'))->with('success', 'Infografis berhasil ditambahkan!');
    }


    // // --- Edit carousel ---
    // public function editcarousel(): string
    // {
    //     return view('admin/edit_carousel');
    // }

    // public function listcarousel(): string
    // {
    //     return view('admin/edit_carousel_list');
    // }

    // --- Edit Infografis ---
    public function editInfografis(): string
    {
        return view('admin/edit_infografis');
    }

    public function listInfografis(): string
    {
        return view('admin/edit_infografis_list');
    }
}
