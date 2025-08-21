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

        // Peta URL CSV; kunci ini dipakai di front‑end (config INDICATOR_SOURCES)
        $csvMap = [
            'LUAS_KEPENDUDUKAN' => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=1557804739&single=true&output=csv',
            'ANGKA_KEMISKINAN'  => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=901949954&single=true&output=csv',
            'INFLASI_UMUM'      => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=882681484&single=true&output=csv',
            'IPM'               => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=896003102&single=true&output=csv',
            'PDRB'              => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=1018033332&single=true&output=csv',
            'KETENAGAKERJAAN'   => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=1275628956&single=true&output=csv',
            'KESEJAHTERAAN'     => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=2060752569&single=true&output=csv',
        ];

        // Kirim data ke view
        $data = [
            'title'     => 'Daftar Data Indikator',
            'indikator' => $indikator,
            'csvMap'    => $csvMap,
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
