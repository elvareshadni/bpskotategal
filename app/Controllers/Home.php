<?php

namespace App\Controllers;

use App\Models\UserModel;

class Home extends BaseController
{
    // Halaman Utama
    public function index(): string
    {
        // Peta URL CSV; kunci ini dipakai di front‑end (config INDICATOR_SOURCES)
        /*$csvMap = [
            'LUAS_KEPENDUDUKAN' => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=1557804739&single=true&output=csv',
            'ANGKA_KEMISKINAN'  => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=901949954&single=true&output=csv',
            'INFLASI_UMUM'      => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=882681484&single=true&output=csv',
            'IPM'               => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=896003102&single=true&output=csv',
            'PDRB'              => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=1018033332&single=true&output=csv',
            'KETENAGAKERJAAN'   => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=1275628956&single=true&output=csv',
            'KESEJAHTERAAN'     => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJyBrk8tL1KUffFoTdcpM_xEd5GpiBQSYA1chCqd631ABxGTSahHBtXHkNzTLKCKa67a8eqJ0IEWwp/pub?gid=2060752569&single=true&output=csv',
        ];*/

        // Peta URL CSV; kunci ini dipakai di front-end (config INDICATOR_SOURCES)
        $exec = getenv('CSV_URL'); // ambil dari .env
        if (!$exec) {
            log_message('error', 'ENV CSV_URL kosong!');
        }


        $csvMap = [
            'LUAS_KEPENDUDUKAN' => $exec . '?sheet=LUAS_KEPENDUDUKAN',
            'ANGKA_KEMISKINAN'  => $exec . '?sheet=ANGKA_KEMISKINAN',
            'INFLASI_UMUM'      => $exec . '?sheet=INFLASI_UMUM',
            'IPM'               => $exec . '?sheet=IPM',
            'PDRB'              => $exec . '?sheet=PDRB',
            'KETENAGAKERJAAN'   => $exec . '?sheet=KETENAGAKERJAAN',
            'KESEJAHTERAAN'     => $exec . '?sheet=KESEJAHTERAAN',
        ];

        // Kirim data ke view
        $data = [
            'title'     => 'Daftar Data Indikator',
            'csvMap'    => $csvMap,
        ];

        return view('user/dashboard', $data);
    }

    // ==========================
    // AUTH
    // ==========================

    // Form Login
    public function login()
    {
        return view('auth/login');
    }

    // Proses Login
    public function doLogin()
{
    // Ambil input "login" (boleh username ATAU email) + password
    $login    = trim($this->request->getPost('login') 
                ?? $this->request->getPost('username') 
                ?? $this->request->getPost('email') 
                ?? '');
    $password = (string) $this->request->getPost('password');

    if ($login === '' || $password === '') {
        return redirect()->back()->with('error', 'Isi username/email dan password.')->withInput();
    }

    $userModel = new \App\Models\UserModel();

    // Cari berdasarkan username ATAU email
    $user = $userModel->where('username', $login)
                      ->orWhere('email', $login)
                      ->first();

    if (!$user || !password_verify($password, $user['password'])) {
        return redirect()->back()->with('error', 'Kredensial tidak valid.')->withInput();
    }

    // Simpan session
    session()->set([
        'user_id'   => $user['id'],
        'username'  => $user['username'],
        'role'      => $user['role'] ?? 'user',
        'logged_in' => true,
    ]);

    // Arahkan sesuai role
    if (($user['role'] ?? 'user') === 'admin') {
        return redirect()->to('/admin');      // dashboard admin
    }
    return redirect()->to('/user');           // dashboard user
}

    // Form Register
    public function register()
    {
        return view('auth/register');
    }

    // Proses Register
    public function doRegister()
    {
        $userModel = new UserModel();

        $data = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'fullname' => $this->request->getPost('fullname'),
        ];

        $userModel->save($data);

        return redirect()->to('/login')->with('success', 'Pendaftaran berhasil, silakan login!');
    }

    // Lupa Password
    public function forget()
    {
        return view('auth/forget');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda berhasil logout.');
    }
}
