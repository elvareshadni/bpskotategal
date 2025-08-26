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
}
