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
        return view('admin/data_indikator');
    }
}
