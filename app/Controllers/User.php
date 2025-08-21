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
}
