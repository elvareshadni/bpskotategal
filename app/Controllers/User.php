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
}
