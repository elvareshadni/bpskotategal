<?php

namespace App\Controllers;

use App\Models\InfografisModel;
use App\Models\CarouselModel;

class Admin extends BaseController
{
    public function index(): string
    {
        return view('admin/index');
    }

    public function profile(): string
    {
        return view('admin/profile');
    }

    public function updateUser()
    {
        // logika update user (sementara kosong)
        return redirect()->back()->with('success', 'User berhasil diupdate!');
    }

    // --- Kelola Data ---
    public function dataIndikator(): string
    {
        return view('admin/kelola_data_indikator');
    }

    public function laporanKunjungan(): string
    {
        return view('admin/laporan_kunjungan');
    }

    // --- carousel ---
    public function carousel(): string
    {
        $carouselModel = new CarouselModel();
        $data['carousels'] = $carouselModel->findAll();

        return view('admin/carousel', $data);
    }


    public function carouselAdd(): string
    {
        return view('admin/carousel_add');
    }

    public function carouselSave()
{
    $carouselModel = new CarouselModel();

    $file  = $this->request->getFile('carouselImage');
    $judul = $this->request->getPost('judulcarousel');

    if ($file && $file->isValid() && !$file->hasMoved()) {
        $newName = $file->getRandomName();
        $file->move(FCPATH . 'img', $newName);

        $carouselModel->insert([
            'judul'   => $judul,
            'gambar'  => $newName,
            'posisi'  => 'center'
        ]);
    }

    return redirect()->to(base_url('admin/listcarousel'))
        ->with('success', 'Carousel berhasil ditambahkan!');
}

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


    // --- Edit carousel ---
    public function editcarousel(): string
    {
        return view('admin/edit_carousel');
    }

    public function listcarousel(): string
    {
        return view('admin/edit_carousel_list');
    }

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
