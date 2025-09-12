<?php

namespace App\Controllers;

use App\Models\InfografisModel;
use App\Models\CarouselModel;
use App\Models\UserModel;
use App\Models\RegionModel;
use App\Models\IndicatorModel;
use App\Models\IndicatorRowModel;
use App\Models\IndicatorRowVarModel;
use App\Models\IndicatorValueModel;

class Admin extends BaseController
{
    public function index()
    {
        return view('Admin/index');
    }

    public function __construct()
    {
        // Proteksi: wajib admin
        if (session()->get('role') !== 'admin') {
            redirect()->to('/login')->send();
            exit;
        }
    }

    public function profile()
    {
        $userModel = new UserModel();
        $userId    = session()->get('user_id'); // ambil id dari session login


        if (!$userId) {
            return redirect()->to('/login');
        }

        $user = $userModel->find($userId);
        if (!$user) {
            return redirect()->to('/login')
                ->with('errors', ['User tidak ditemukan'])
                ->with('error', 'User tidak ditemukan');
        }

        return view('Admin/profile', [
            'title' => 'My Profile',
            'user'  => $user,
        ]);
    }

    /**
     * Update data profil Admin (validasi manual, tanpa Validation Service)
     */
    public function updateProfile()
    {
        $userModel = new UserModel();
        $userId    = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $current = $userModel->find($userId);
        if (!$current) {
            return redirect()->to('/login')
                ->with('errors', ['User tidak ditemukan'])
                ->with('error', 'User tidak ditemukan');
        }

        $username = trim((string) $this->request->getPost('username'));
        $fullname = trim((string) $this->request->getPost('fullname'));
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $phone    = trim((string) $this->request->getPost('phone'));
        $file     = $this->request->getFile('photo');

        $errors = [];

        // --- Username ---
        if ($username === '') {
            $errors['username'] = 'Username wajib diisi.';
        } elseif (mb_strlen($username) < 3) {
            $errors['username'] = 'Username minimal 3 karakter.';
        } elseif (mb_strlen($username) > 50) {
            $errors['username'] = 'Username maksimal 50 karakter.';
        } elseif ($username !== (string) $current['username']) {
            // cek unik hanya jika berubah
            $exists = $userModel->where('username', $username)
                ->where('id !=', $userId)
                ->first();
            if ($exists) {
                $errors['username'] = 'Username sudah digunakan.';
            }
        }

        // --- Fullname ---
        if ($fullname === '') {
            $errors['fullname'] = 'Nama lengkap wajib diisi.';
        } elseif (mb_strlen($fullname) < 3) {
            $errors['fullname'] = 'Nama lengkap minimal 3 karakter.';
        }

        // --- Email ---
        if ($email === '') {
            $errors['email'] = 'Email wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid.';
        } elseif (strcasecmp($email, (string) $current['email']) !== 0) {
            // cek unik hanya jika berubah (case-insensitive)
            $exists = $userModel->where('email', $email)
                ->where('id !=', $userId)
                ->first();
            if ($exists) {
                $errors['email'] = 'Email sudah digunakan.';
            }
        }

        // --- Phone (opsional) ---
        if ($phone !== '') {
            if (mb_strlen($phone) < 6 || mb_strlen($phone) > 20) {
                $errors['phone'] = 'No. HP minimal 6 dan maksimal 20 karakter.';
            }
            // Jika ingin hanya angka/spasi/-/+ :
            // elseif (!preg_match('/^\+?[0-9\s\-]+$/', $phone)) {
            //     $errors['phone'] = 'No. HP hanya boleh berisi angka, spasi, tanda minus, dan awalan +.';
            // }
        }

        // --- Foto (opsional) ---
        $photoPath = null;
        if ($file && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            // ukuran ≤ 1MB
            if ($file->getSize() > 1024 * 1024) {
                $errors['photo'] = 'Ukuran foto maksimal 1MB.';
            }
            // tipe
            $mimeOk = ['image/jpg', 'image/jpeg', 'image/png'];
            $mime   = $file->getMimeType();
            if (!in_array($mime, $mimeOk, true)) {
                $errors['photo'] = 'Format foto harus jpg, jpeg, atau png.';
            }
        }

        if ($errors) {
            return redirect()->back()->withInput()
                ->with('errors', $errors)
                ->with('error', reset($errors));
        }

        // data update
        $data = [
            'username' => $username,
            'fullname' => $fullname,
            'email'    => $email,
            'phone'    => $phone,
        ];

        // simpan foto jika ada
        if ($file && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $dir = FCPATH . 'uploads/profile';
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            $newName = $file->getRandomName();
            if (!$file->move($dir, $newName)) {
                return redirect()->back()->withInput()
                    ->with('errors', ['photo' => 'Gagal menyimpan foto.'])
                    ->with('error', 'Gagal menyimpan foto.');
            }
            $photoPath = 'uploads/profile/' . $newName;
            $data['photo'] = $photoPath;

            // hapus foto lama (jika ada)
            if (!empty($current['photo'])) {
                $old = FCPATH . ltrim($current['photo'], '/');
                if (is_file($old)) {
                    @unlink($old);
                }
            }
        }

        try {
            $userModel->update($userId, $data);
        } catch (\Throwable $e) {
            log_message('error', 'Admin updateProfile error: {0}', [$e->getMessage()]);
            return redirect()->back()->withInput()
                ->with('errors', ['global' => 'Gagal memperbarui profil. Coba lagi.'])
                ->with('error', 'Gagal memperbarui profil. Coba lagi.');
        }

        // >>> SINKRONKAN SESSION <<<
        if (isset($data['username'])) {
            session()->set('username', $data['username']);
        }
        if (isset($data['photo'])) {
            session()->set('photo', $data['photo']);
        }

        return redirect()->route('admin.profile')->with('msg', 'Profil berhasil diperbarui!');
    }

    /**
     * Ubah password Admin (validasi manual, tanpa Validation Service)
     */
    public function updatePassword()
    {
        $userModel = new UserModel();
        $userId    = session()->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword     = (string) $this->request->getPost('new_password');
        $confirmPassword = (string) $this->request->getPost('confirm_password');

        $errors = [];

        if ($currentPassword === '') {
            $errors['current_password'] = 'Password sekarang wajib diisi.';
        }
        if ($newPassword === '') {
            $errors['new_password'] = 'Password baru wajib diisi.';
        } elseif (mb_strlen($newPassword) < 6) {
            $errors['new_password'] = 'Password baru minimal 6 karakter.';
        }
        // Jika ingin aturan kuat:
        // elseif (!$this->isStrongPassword($newPassword)) {
        //     $errors['new_password'] = 'Password baru harus mengandung huruf kecil, huruf besar, dan angka.';
        // }

        if ($confirmPassword === '') {
            $errors['confirm_password'] = 'Konfirmasi password wajib diisi.';
        } elseif ($confirmPassword !== $newPassword) {
            $errors['confirm_password'] = 'Konfirmasi password tidak sama dengan Password baru.';
        }

        if ($errors) {
            return redirect()->back()->withInput()
                ->with('errors', $errors)
                ->with('error', reset($errors));
        }

        $user = $userModel->find($userId);
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return redirect()->back()->withInput()
                ->with('errors', ['current_password' => 'Password sekarang salah.'])
                ->with('error', 'Password sekarang salah.');
        }

        try {
            $userModel->update($userId, [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Admin updatePassword error: {0}', [$e->getMessage()]);
            return redirect()->back()->withInput()
                ->with('errors', ['global' => 'Gagal mengubah password. Coba lagi.'])
                ->with('error', 'Gagal mengubah password. Coba lagi.');
        }

        return redirect()->route('admin.profile')->with('msg', 'Password berhasil diperbarui.');
    }

    // --- Kelola Data ---

    public function dataIndikator()
    {
        $regions = (new RegionModel())->orderBy('is_default', 'DESC')->orderBy('name', 'ASC')->findAll();
        return view('Admin/data_indikator/landing', ['regions' => $regions]);
    }

    // ====== Kelola Region ======
    public function regions()
    {
        $regions = (new RegionModel())->orderBy('id', 'DESC')->findAll();
        return view('Admin/regions/index', ['regions' => $regions]);
    }

    public function regionCreate()
    {
        $code = trim((string)$this->request->getPost('code_bps'));
        $name = trim((string)$this->request->getPost('name'));
        if ($name === '') return redirect()->back()->with('error', 'Nama wajib diisi');
        (new RegionModel())->insert(['code_bps' => $code ?: null, 'name' => $name, 'is_default' => 0]);
        return redirect()->to(base_url('admin/regions'))->with('success', 'Berhasil menambah region');
    }

    public function regionUpdate($id)
    {
        $code = trim((string)$this->request->getPost('code_bps'));
        $name = trim((string)$this->request->getPost('name'));
        (new RegionModel())->update((int)$id, ['code_bps' => $code ?: null, 'name' => $name]);
        return redirect()->to(base_url('admin/regions'))->with('success', 'Berhasil memperbarui');
    }

    public function regionDelete($id)
    {
        (new RegionModel())->delete((int)$id);
        return redirect()->to(base_url('admin/regions'))->with('success', 'Dihapus');
    }

    // ====== Kelola Indikator ======
    public function indicators()
    {
        $regionM = new RegionModel();
        $regions = $regionM->orderBy('is_default', 'DESC')->orderBy('name', 'ASC')->findAll();

        // default Kota Tegal kalau ada
        $default = null;
        foreach ($regions as $r) {
            if ((int)$r['is_default'] === 1) {
                $default = $r;
                break;
            }
        }
        $currentRegionId = (int)($this->request->getGet('region_id') ?? ($default['id'] ?? ($regions[0]['id'] ?? 0)));

        return view('Admin/indikator/index', [
            'regions' => $regions,
            'currentRegionId' => $currentRegionId
        ]);
    }

    public function indicatorsList()
    {
        $regionId = (int)($this->request->getGet('region_id') ?? 0);
        if ($regionId <= 0) return $this->response->setJSON(['ok' => false, 'error' => 'Region invalid']);
        $rows = (new IndicatorModel())
            ->where('region_id', $regionId)
            ->orderBy('id', 'DESC')->findAll();

        // tambahkan subindikator list ringkas
        $rowM = new IndicatorRowModel();
        $out = [];
        foreach ($rows as $ind) {
            $subs = $rowM->where('indicator_id', $ind['id'])->orderBy('sort_order', 'ASC')->findAll();
            $out[] = [
                'id' => $ind['id'],
                'name' => $ind['name'],
                'subcount' => count($subs),
                'subs' => array_map(fn($s) => ['id' => $s['id'], 'subindikator' => $s['subindikator']], $subs),
            ];
        }
        return $this->response->setJSON(['ok' => true, 'data' => $out]);
    }

    public function indicatorForm()
    {
        $id = (int)($this->request->getGet('id') ?? 0);
        $indicator = null;
        if ($id > 0) $indicator = (new IndicatorModel())->find($id);

        $regions = (new RegionModel())->orderBy('name', 'ASC')->findAll();
        $subs = $indicator ? (new IndicatorRowModel())->where('indicator_id', $indicator['id'])->orderBy('sort_order', 'ASC')->findAll() : [];

        return view('Admin/indikator/form_indicator', [
            'indicator' => $indicator,
            'regions' => $regions,
            'subs' => $subs
        ]);
    }

    public function indicatorSave()
    {
        $id       = (int)$this->request->getPost('id');
        $regionId = (int)$this->request->getPost('region_id');
        $name     = trim((string)$this->request->getPost('name'));
        $code     = trim((string)$this->request->getPost('code')) ?: null;

        $indM = new IndicatorModel();

        if ($id > 0) {
            $indM->update($id, ['region_id' => $regionId, 'name' => $name, 'code' => $code]);
        } else {
            $id = $indM->insert(['region_id' => $regionId, 'name' => $name, 'code' => $code]);
        }

        // opsional: proses subindikator baru dari form (array sub_new[])
        $subNew = (array)($this->request->getPost('sub_new') ?? []);
        $rowM = new IndicatorRowModel();
        $order = 1;
        foreach ($subNew as $nm) {
            $nm = trim($nm);
            if ($nm === '') continue;
            $rowM->insert([
                'indicator_id' => $id,
                'subindikator' => $nm,
                'timeline' => 'yearly',
                'data_type' => 'single',
                'unit' => null,
                'sort_order' => $order++,
            ]);
        }

        return redirect()->to(base_url('admin/indicators'))->with('success', 'Indikator tersimpan');
    }

    public function indicatorDelete($id)
    {
        // cascade oleh FK (rows, values, vars akan ikut hilang)
        (new IndicatorModel())->delete((int)$id);
        return redirect()->to(base_url('admin/indicators'))->with('success', 'Indikator dihapus');
    }

    // ====== Subindikator ======
    public function subindikatorForm()
    {
        $rowId = (int)($this->request->getGet('id') ?? 0);
        $indicatorId = (int)($this->request->getGet('indicator_id') ?? 0);

        $rowM = new IndicatorRowModel();
        $varM = new IndicatorRowVarModel();

        $row = $rowId > 0 ? $rowM->find($rowId) : null;
        $vars = $row ? $varM->where('row_id', $row['id'])->orderBy('sort_order', 'ASC')->findAll() : [];

        // region default utk grid data: ambil default region id (untuk render awal)
        $regionDefault = (new RegionModel())->where('is_default', 1)->first();

        return view('Admin/indikator/form_subindikator', [
            'row' => $row,
            'indicator_id' => $indicatorId ?: ($row['indicator_id'] ?? 0),
            'vars' => $vars,
            'regionDefaultId' => $regionDefault['id'] ?? 0,
        ]);
    }

    public function subindikatorSave()
    {
        $rowM = new IndicatorRowModel();
        $id   = (int)$this->request->getPost('id');
        $data = [
            'indicator_id' => (int)$this->request->getPost('indicator_id'),
            'subindikator' => trim((string)$this->request->getPost('subindikator')),
            'timeline'     => $this->request->getPost('timeline') === 'Triwulan' ? 'quarterly' : ($this->request->getPost('timeline') === 'Bulanan' ? 'monthly' : 'yearly'),
            'data_type'    => $this->request->getPost('data_type') === 'Data Proporsi' ? 'proporsi' : 'single',
            'unit'         => $this->request->getPost('unit') ?: null,
        ];
        if ($id > 0) {
            $rowM->update($id, $data);
        } else {
            $id = $rowM->insert($data); // default Data Biasa & Tahunan sudah dicover mapping di atas
        }
        return redirect()->to(base_url('admin/subindicator/form?id=' . $id))->with('success', 'Disimpan');
    }

    public function subindikatorDelete($id)
    {
        (new IndicatorRowModel())->delete((int)$id);
        return redirect()->to(base_url('admin/indicators'))->with('success', 'Subindikator dihapus');
    }

    // Variabel untuk Data Proporsi
    public function varCreate()
    {
        $rowId = (int)$this->request->getPost('row_id');
        $name  = trim((string)$this->request->getPost('name'));
        if ($rowId <= 0 || $name === '') return $this->response->setJSON(['ok' => false, 'error' => 'Invalid']);
        $vm = new IndicatorRowVarModel();
        $vm->insert(['row_id' => $rowId, 'name' => $name, 'sort_order' => 999]);
        return $this->response->setJSON(['ok' => true]);
    }

    public function varDelete($id)
    {
        (new IndicatorRowVarModel())->delete((int)$id);
        return $this->response->setJSON(['ok' => true]);
    }

    // ====== AJAX untuk landing grid (yang sudah kamu tulis di JS) ======
    public function ajaxIndicatorsByRegion($regionId)
    {
        $list = (new IndicatorModel())
            ->select('id,name,code')
            ->where('region_id', (int)$regionId)
            ->orderBy('name', 'ASC')->findAll();
        return $this->response->setJSON(['ok' => true, 'data' => $list]);
    }

    public function ajaxRowsByRegionIndicator($regionId, $indicatorId)
    {
        $rows = (new IndicatorRowModel())
            ->select('id,subindikator,timeline,data_type')
            ->where('indicator_id', (int)$indicatorId)
            ->orderBy('sort_order', 'ASC')->findAll();

        // sesuaikan field yang dipakai di landing.js (dataset.timeline, dataset.dtype)
        $data = array_map(fn($r) => [
            'id' => $r['id'],
            'subindikator' => $r['subindikator'],
            'timeline' => $r['timeline'],      // yearly/quarterly/monthly
            'data_type' => $r['data_type'],    // single/proporsi
        ], $rows);

        return $this->response->setJSON(['ok' => true, 'data' => $data]);
    }

    public function gridFetch()
    {
        $regionId = (int)$this->request->getGet('region_id');
        $rowId    = (int)$this->request->getGet('row_id');
        $yFrom    = (int)($this->request->getGet('year_from') ?? 0);
        $yTo      = (int)($this->request->getGet('year_to') ?? 0);

        $row = (new IndicatorRowModel())->find($rowId);
        if (!$row) return $this->response->setJSON(['ok' => false, 'error' => 'Row not found']);

        $vars = [];
        if ($row['data_type'] === 'proporsi') {
            $vars = (new IndicatorRowVarModel())->where('row_id', $rowId)->orderBy('sort_order', 'ASC')->findAll();
        }

        // meta kolom untuk Tabulator
        $meta = [
            'timeline' => $row['timeline'],
            'data_type' => $row['data_type'],
            'unit' => $row['unit'],
            'vars' => []
        ];
        if ($row['data_type'] === 'single') {
            $meta['vars'][] = ['col' => 'val__single', 'name' => $row['subindikator']];
        } else {
            foreach ($vars as $v) $meta['vars'][] = ['col' => 'val__' . $v['id'], 'name' => $v['name']];
        }

        // siapkan periode rows
        $rows = [];
        $years = range($yFrom ?: date('Y') - 5, $yTo ?: date('Y'));
        foreach ($years as $yy) {
            if ($row['timeline'] === 'yearly') {
                $rows[] = ['period' => (string)$yy, 'year' => $yy, 'quarter' => 0, 'month' => 0];
            } elseif ($row['timeline'] === 'quarterly') {
                foreach ([1, 2, 3, 4] as $q) {
                    $rows[] = ['period' => "$yy Q$q", 'year' => $yy, 'quarter' => $q, 'month' => 0];
                }
            } else { // monthly
                for ($m = 1; $m <= 12; $m++) {
                    $rows[] = ['period' => sprintf('%d-%02d', $yy, $m), 'year' => $yy, 'quarter' => 0, 'month' => $m];
                }
            }
        }

        // ambil nilai
        $valM = new IndicatorValueModel();
        $valQ = $valM->where('row_id', $rowId)->where('region_id', $regionId);
        if ($yFrom) $valQ->where('year >=', $yFrom);
        if ($yTo)   $valQ->where('year <=', $yTo);
        $vals = $valQ->findAll();

        // index by (y,q,m,var)
        $map = [];
        foreach ($vals as $v) {
            $key = $v['year'] . '|' . ((int)$v['quarter']) . '|' . ((int)$v['month']);
            $vid = $row['data_type'] === 'single' ? 'single' : (string)$v['var_id'];
            $map[$key][$vid] = is_null($v['value']) ? null : (float)$v['value'];
        }

        // masukkan ke rows
        foreach ($rows as &$r) {
            $key = $r['year'] . '|' . $r['quarter'] . '|' . $r['month'];
            if ($row['data_type'] === 'single') {
                $r['val__single'] = $map[$key]['single'] ?? null;
            } else {
                foreach ($vars as $v) {
                    $r['val__' . $v['id']] = $map[$key][(string)$v['id']] ?? null;
                }
            }
        }

        return $this->response->setJSON(['ok' => true, 'meta' => $meta, 'rows' => $rows]);
    }

    public function gridSave()
    {
        $json = $this->request->getJSON(true);
        $regionId = (int)($json['region_id'] ?? 0);
        $rowId    = (int)($json['row_id'] ?? 0);
        $entries  = (array)($json['entries'] ?? []);
        if ($regionId <= 0 || $rowId <= 0) return $this->response->setJSON(['ok' => false, 'error' => 'Bad payload']);

        $row = (new IndicatorRowModel())->find($rowId);
        if (!$row) return $this->response->setJSON(['ok' => false, 'error' => 'Row not found']);

        $valM = new IndicatorValueModel();

        // Simpan per entry (idempoten: delete+insert atau upsert)
        foreach ($entries as $e) {
            $year = (int)($e['year'] ?? 0);
            $q    = (int)($e['quarter'] ?? 0);
            $m    = (int)($e['month'] ?? 0);
            $var  = $e['var_id'] === '' ? null : ($e['var_id'] ?? null);
            $val  = $e['value'];

            // cari existing
            $builder = $valM->where([
                'row_id' => $rowId,
                'region_id' => $regionId,
                'year' => $year,
                'quarter' => $q ?: null,
                'month' => $m ?: null,
            ]);
            if ($row['data_type'] === 'proporsi') $builder->where('var_id', $var);
            else $builder->where('var_id', null);

            $found = $builder->first();

            if ($val === null || $val === '') {
                // kosong => hapus record kalau ada
                if ($found) $valM->delete($found['id']);
                continue;
            }

            $payload = [
                'row_id' => $rowId,
                'region_id' => $regionId,
                'var_id' => $row['data_type'] === 'proporsi' ? (int)$var : null,
                'year' => $year,
                'quarter' => $q ?: null,
                'month' => $m ?: null,
                'value' => (float)$val,
            ];

            if ($found) {
                $valM->update($found['id'], $payload);
            } else {
                $valM->insert($payload);
            }
        }
    }



    public function laporanKunjungan()
    {
        $kunjunganModel = new \App\Models\KunjunganModel();
        $data['kunjungan'] = $kunjunganModel->findAll();

        return view('Admin/laporan_kunjungan', $data);
    }

    // --- Infografis ---
    public function addInfografis()
    {
        return view('Admin/tambah_infografis');
    }

    public function saveInfografis()
    {
        $infografisModel = new InfografisModel();

        $file = $this->request->getFile('infografisImage');
        $newName = $file ? $file->getRandomName() : null;

        if ($file && $file->isValid() && $newName) {
            $file->move('img', $newName);
        }

        $infografisModel->save([
            'judul'     => $this->request->getPost('judulInfografis'),
            'deskripsi' => $this->request->getPost('deskripsiInfografis'),
            'gambar'    => $newName,
            'tanggal'   => date('Y-m-d')
        ]);

        return redirect()->to(base_url('admin/edit-infografis/list'))->with('success', 'Infografis berhasil ditambahkan!');
    }

    // === LIST (untuk halaman edit daftar) ===
    public function listInfografis()
    {
        $m = new InfografisModel();
        $data['rows'] = $m->orderBy('id', 'DESC')->findAll();
        return view('Admin/edit_infografis_list', $data);
    }

    // === EDIT (form) ===
    public function editInfografis($id)
    {
        $m = new InfografisModel();
        $row = $m->find($id);
        if (!$row) {
            return redirect()->to(base_url('admin/edit-infografis/list'))
                ->with('error', 'Data tidak ditemukan');
        }
        return view('Admin/edit_infografis', ['row' => $row]);
    }

    // === UPDATE (submit edit) ===
    public function updateInfografis($id)
    {
        $m = new InfografisModel();
        $row = $m->find($id);
        if (!$row) {
            return redirect()->to(base_url('admin/edit-infografis/list'))
                ->with('error', 'Data tidak ditemukan');
        }

        $judul     = trim((string)$this->request->getPost('judulInfografis'));
        $deskripsi = trim((string)$this->request->getPost('deskripsiInfografis'));
        $tanggal   = $this->request->getPost('tanggal') ?: $row['tanggal'];

        // Upload gambar (opsional)
        $file = $this->request->getFile('infografisImage');
        $data = [
            'judul'     => $judul,
            'deskripsi' => $deskripsi,
            'tanggal'   => $tanggal,
        ];

        if ($file && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            if ($file->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->withInput()->with('error', 'Ukuran maks 2MB');
            }
            if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg'])) {
                return redirect()->back()->withInput()->with('error', 'Format harus JPG/PNG');
            }
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'img', $newName);
            $data['gambar'] = $newName;

            // hapus file lama jika ada
            if (!empty($row['gambar'])) {
                @unlink(FCPATH . 'img/' . $row['gambar']);
            }
        }

        if (!$m->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengubah data.');
        }

        return redirect()->to(base_url('admin/edit-infografis/list'))
            ->with('success', 'Infografis berhasil diperbarui.');
    }

    // === DELETE ===
    public function deleteInfografis($id)
    {
        $m = new InfografisModel();
        $row = $m->find($id);
        if ($row && !empty($row['gambar'])) {
            @unlink(FCPATH . 'img/' . $row['gambar']);
        }
        $m->delete($id);
        return redirect()->to(base_url('admin/edit-infografis/list'))
            ->with('success', 'Infografis berhasil dihapus.');
    }

    // ==========================
    // Utilitas (opsional)
    // ==========================
    /*private function isStrongPassword(string $pwd): bool
    {
        $hasLower = preg_match('/[a-z]/', $pwd);
        $hasUpper = preg_match('/[A-Z]/', $pwd);
        $hasDigit = preg_match('/\d/',    $pwd);
        return (bool) ($hasLower && $hasUpper && $hasDigit);
    }*/
}
