<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('auth/login');
    }

    public function register(): string
    {
        return view('auth/register');
    }

    public function user(): string
    {
        return view('user/index');
    }

    public function dataIndikator(): string
    {
        return view('user/kelola_data_indikator');
    }
    
    // --- Laporan Kunjungan ---
    public function laporanKunjungan(): string
    {
        return view('user/laporan_kunjungan');
    }

    // --- Edit Carousell ---
    public function editCarousell(): string
    {
        return view('user/edit_carousell');
    }

    public function listCarousell(): string
    {
        return view('user/edit_carousell_list');
    }

    // --- Edit Infografis ---
    public function editInfografis(): string
    {
        return view('user/edit_infografis');
    }

    public function listInfografis(): string
    {
        return view('user/edit_infografis_list');
    }
}
