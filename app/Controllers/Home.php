<?php

namespace App\Controllers;

use App\Models\DataModel;

class Home extends BaseController
{
    // Halaman Utama BPS Kota Tegal 
    public function index(): string
    {
        // Mengambil Data Indikator dari Database
        $dataModel = new DataModel();
        $indikator = $dataModel->findAll();

        // Kirim data ke view
        $data = [
            'title'     => 'Daftar Data Indikator',
            'indikator' => $indikator
        ];

        return view('user/dashboard', $data);
    }

    // Menampilkan Halaman Register
    public function register()
    {
        return view('auth/register');
    }

    // Menampilkan Halaman Lupa Sandi
    public function forget()
    {
        return view('auth/forget');
    }
}
