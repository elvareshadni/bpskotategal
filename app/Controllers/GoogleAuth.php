<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\KunjunganModel;
use Google\Client as GoogleClient;
use Google\Service\Oauth2 as GoogleOauth2;

class GoogleAuth extends BaseController
{
    private GoogleClient $client;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setClientId(getenv('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(getenv('GOOGLE_REDIRECT_URI'));
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account'); // atau 'consent select_account'
        $this->client->setScopes([
            'openid',
            'email',
            'profile',
        ]);
    }

public function redirect()
{
    $state = bin2hex(random_bytes(16));
    session()->set('oauth2state', $state);

    // set via client API, bukan manual di URL
    $this->client->setState($state);

    // pastikan redirectUri kepasang dari env
    $this->client->setRedirectUri(getenv('GOOGLE_REDIRECT_URI'));

    return redirect()->to($this->client->createAuthUrl());
}


    public function callback()
    {
        $stateFromGoogle = $this->request->getGet('state');
        $stateSession    = session()->get('oauth2state');
        $code            = $this->request->getGet('code');

        if (!$stateFromGoogle || !$stateSession || !hash_equals($stateSession, $stateFromGoogle)) {
            return redirect()->to('/login')->with('errors', ['global' => 'State OAuth tidak cocok. Coba lagi.']);
        }
        session()->remove('oauth2state');

        if (!$code) {
            return redirect()->to('/login')->with('errors', ['global' => 'Kode OAuth tidak ditemukan.']);
        }

        try {
            $this->client->fetchAccessTokenWithAuthCode($code);
        } catch (\Throwable $e) {
            log_message('error', 'OAuth token exchange error: {0}', [$e->getMessage()]);
            return redirect()->to('/login')->with('errors', ['global' => 'Gagal autentikasi Google.']);
        }

        $service = new GoogleOauth2($this->client);
        try {
            $gUser = $service->userinfo->get();
        } catch (\Throwable $e) {
            log_message('error', 'Fetch userinfo error: {0}', [$e->getMessage()]);
            return redirect()->to('/login')->with('errors', ['global' => 'Gagal mengambil profil Google.']);
        }

        // Data penting
        $googleId   = $gUser->id ?? null;
        $email      = strtolower(trim((string)($gUser->email ?? '')));
        $name       = trim((string)($gUser->name ?? ''));
        $emailVerif = (bool)($gUser->verifiedEmail ?? false);

        if (!$googleId || !$email) {
            return redirect()->to('/login')->with('errors', ['global' => 'Profil Google tidak lengkap (id/email).']);
        }
        if (!$emailVerif) {
            return redirect()->to('/login')->with('errors', ['global' => 'Email Google Anda belum terverifikasi.']);
        }

        $userM = new UserModel();

        // 1) Cari by google_id
        $user = $userM->where('google_id', $googleId)->first();

        // 2) Kalau belum ada, kaitkan by email (hindari duplikasi akun)
        if (!$user) {
            $user = $userM->where('email', $email)->first();
        }

        // 3) Jika tetap tidak ada → buat user baru (tanpa password)
        if (!$user) {
            $username = $this->generateUniqueUsername($name ?: explode('@', $email)[0], $userM);

            $userId = $userM->insert([
                'username'         => $username,
                'fullname'         => $name ?: $username,
                'email'            => $email,
                'password'         => null,             // <— inti: tidak ada password
                'role'             => 'user',
                'google_id'        => $googleId,
                'auth_provider'    => 'google',
                'email_verified_at'=> date('Y-m-d H:i:s'),
            ], true);

            $user = $userM->find($userId);
        } else {
            // Update pengkaitan google_id + verif email jika perlu
            $userM->update($user['id'], [
                'google_id'        => $user['google_id'] ?: $googleId,
                'auth_provider'    => 'google',
                'email_verified_at'=> $user['email_verified_at'] ?: date('Y-m-d H:i:s'),
            ]);
            $user = $userM->find($user['id']);
        }

        // === Set session (selaras dengan Home::doLogin) ===
        session()->set([
            'user_id'   => $user['id'],
            'username'  => $user['username'],
            'role'      => $user['role'] ?? 'user',
            'photo'     => $user['photo'] ?? 'img/default.png',
            'fullname'  => $user['fullname'],
            'logged_in' => true,
        ]);

        // Catat kunjungan (hanya role user)
        try {
            if (($user['role'] ?? 'user') === 'user') {
                $km  = new KunjunganModel();
                $now = date('Y-m-d H:i:s');
                $km->insert([
                    'user_id'      => $user['id'],
                    'username'     => $user['username'],
                    'login_time'   => $now,
                    'logout_time'  => null,
                    'durasi_waktu' => null,
                ]);
                session()->set('visit_row_id', $km->getInsertID());
                session()->set('last_activity', time());
            } else {
                session()->remove('visit_row_id');
            }
        } catch (\Throwable $e) {
            log_message('error', 'Kunjungan Google login gagal: {0}', [$e->getMessage()]);
        }

        // Arahkan sesuai role
        if (($user['role'] ?? 'user') === 'admin') {
            return redirect()->to('/admin')->with('success', 'Login Google berhasil. Selamat datang, ' . $user['username'] . '!');
        }
        return redirect()->to('/user')->with('success', 'Login Google berhasil. Selamat datang, ' . $user['username'] . '!');
    }

    private function generateUniqueUsername(string $base, UserModel $userM): string
    {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '', str_replace(' ', '', $base)));
        $slug = $slug ?: 'user';
        $candidate = $slug;
        $i = 1;
        while ($userM->where('username', $candidate)->first()) {
            $candidate = $slug . $i;
            $i++;
        }
        return $candidate;
    }
}
