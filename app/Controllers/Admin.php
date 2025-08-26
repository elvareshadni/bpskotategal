<?php

namespace App\Controllers;
use App\Models\UserModel;
use App\Models\InfografisModel;

class Admin extends BaseController
{
    protected $userModel;
    protected $infografisModel;

    public function __construct()
    {
        // Inisialisasi UserModel
        $this->userModel = new UserModel();
        $this->infografisModel = new InfografisModel();

    }

    public function index(): string
    {
        // Mendapatkan ID pengguna yang sedang login
        $id = user()->id;

        // Mendapatkan data user
        $data = [
            'title' => 'Profile',
            'user' => $this->userModel->where(['id' => $id])->first()  
        ];

        return view('admin/profile', $data);
    }

    public function updateUser()
    {
        // Mendapatkan ID pengguna yang sedang login
        $id = user()->id;

        // Ambil data lama
        $userLama = $this->userModel->find($id);

        // Validasi Form Input
        if (!$this->validate([
            'fullname' => 'permit_empty|string|max_length[255]',
            'email' => 'permit_empty|valid_email',
            'phone_number' => 'permit_empty|numeric|max_length[15]',
            'user_image' => 'permit_empty|is_image[user_image]|max_size[user_image,3072]|mime_in[user_image,image/jpg,image/jpeg,image/png]',
            'id_image' => 'permit_empty|is_image[id_image]|max_size[id_image,3072]|mime_in[id_image,image/jpg,image/jpeg,image/png]',
        ])) {
            return redirect()->to('/user/profile')->withInput()->with('validation', $this->validator);
        }

        // Mengelola file gambar user_image
        $userImage = $this->request->getFile('user_image');
        $userImageName = $userLama['user_image']; // Gunakan gambar lama secara default
        if ($userImage && $userImage->isValid()) {
            $userImage->move('img/user');
            $userImageName = $userImage->getName();
        }

        // Mengelola file gambar id_image
        $idImage = $this->request->getFile('id_image');
        $idImageName = $userLama['id_image']; // Gunakan gambar lama secara default
        if ($idImage && $idImage->isValid()) {
            $idImage->move('img/user');
            $idImageName = $idImage->getName();
        }

        // Data yang ingin diupdate
        $data = [
            'fullname'     => $this->request->getVar('fullname') ?: $userLama['fullname'],
            'email'        => $this->request->getVar('email') ?: $userLama['email'],
            'phone_number' => $this->request->getVar('phone_number') ?: $userLama['phone_number'],
            'user_image'   => $userImageName,
            'nik'          => $this->request->getVar('nik') ?: $userLama['nik'],
            'id_image'     => $idImageName,
        ];

        // Update data pengguna
        if ($this->userModel->update($id, $data)) {
            session()->setFlashdata('pesan', 'Data berhasil diperbarui');
        } else {
            session()->setFlashdata('pesan', 'Gagal memperbarui data');
        }

        return redirect()->to('/admin/profile');
    }

    public function infografis()
    {
        $infografis = $this->infografisModel->findAll();
        // Mendapatkan data user
        $data = [
            'title' => 'Manage infografis',
            'infografis' => $infografis
        ];

        return view('admin/infografis', $data);
    }

    public function create()
    {
        // Mendapatkan data user
        $data = [
            'title' => 'Form Tambah infografis',
        ];

        return view('admin/create', $data);
    }

    public function save()
    {
        $slug = url_title($this->request->getVar('name'), '-', true);

        // Mengelola file foto infografis
        $photo = $this->request->getFile('image');
        $photoName = '';
        if ($photo && $photo->isValid()) {
            $photo->move('img/infografis', $photo->getName());
            $photoName = $photo->getName();
        }

        // Menyimpan data infografis baru
        $this->infografisModel->save([
            'name' => $this->request->getVar('name'),
            'address' => $this->request->getVar('address'),
            'type' => $this->request->getVar('type'),
            'slug' => $slug,
            'price' => $this->request->getVar('price'),
            'capacity' => $this->request->getVar('capacity'),
            'description' => $this->request->getVar('description'),
            'image' => $photoName
        ]);

        // Set flashdata untuk notifikasi
        session()->setFlashdata('success', 'Data infografis berhasil ditambahkan.');
        
        return redirect()->to('/admin/infografis');
    }

    public function edit($id)
    {
        // Ambil data infografis berdasarkan ID
        $infografis = $this->infografisModel->find($id);

        // Data untuk form edit
        $data = [
            'title' => 'Edit infografis',
            'infografis' => $infografis
        ];

        return view('admin/edit', $data); // Menggunakan tampilan terpisah untuk edit
    }

    public function update($id)
    {
        // Mengelola file foto infografis
        $photo = $this->request->getFile('image');
        $photoName = $this->request->getVar('existing_image');
        if ($photo && $photo->isValid()) {
            $photo->move('img/infografis', $photo->getName());
            $photoName = $photo->getName();
        }

        // Memperbarui data infografis
        $this->infografisModel->update($id, [
            'name' => $this->request->getVar('name'),
            'address' => $this->request->getVar('address'),
            'type' => $this->request->getVar('type'),
            'price' => $this->request->getVar('price'),
            'capacity' => $this->request->getVar('capacity'),
            'description' => $this->request->getVar('description'),
            'image' => $photoName
        ]);

        session()->setFlashdata('success', 'Data infografis berhasil diperbarui.');

        return redirect()->to('/admin/infografis');
    }

    public function delete($id)
    {
        // Cek apakah data infografis ada
        $infografis = $this->infografisModel->find($id);
        if (!$infografis) {
            session()->setFlashdata('error', 'Data infografis tidak ditemukan.');
            return redirect()->to('/admin/infografis');
        }

        // Hapus file foto infografis jika ada
        if ($infografis['image']) {
            $filePath = 'img/infografis/' . $infografis['image'];
            if (file_exists($filePath)) {
                unlink($filePath); // Hapus foto infografis
            }
        }

        // Hapus data infografis dari database
        if ($this->infografisModel->delete($id)) {
            session()->setFlashdata('success', 'Data infografis berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data infografis.');
        }

        return redirect()->to('/admin/infografis');
    }

}
