<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Halaman awal → login, bukan dashboard
$routes->get('/', 'Home::login');

// USER
$routes->group('user', function ($routes) {
    $routes->get('/', 'User::index');
    $routes->get('beranda', 'User::beranda');
    $routes->get('list', 'User::list');
    $routes->get('detail/(:num)', 'User::detail/$1');
});

// API untuk data indikator (JSON)
$routes->get('api/indikator', 'Indicators::index');

// API untuk dashboard
$routes->group('api', function ($routes) {
    $routes->get('regions', 'Indicators::apiRegions');                 // list region
    $routes->get('indicators', 'Indicators::apiIndicators');           // ?region_id=ID
    $routes->get('rows', 'Indicators::apiRows');                       // ?indicator_id=ID
    $routes->get('series', 'Indicators::apiSeries');                   // ?row_id=..&region_id=..&window=all|last3|last5|year=YYYY&quarter=Q&month=M
    $routes->get('proportion', 'Indicators::apiProportion');           // ?row_id=..&region_id=..&year=..[&quarter=..|&month=..]
    $routes->get('export/xlsx', 'Indicators::apiExportXlsx');          // unduh data yg sedang ditampilkan
});


// ADMIN
$routes->group('admin', function ($routes) {
    $routes->get('/', 'Admin::index');

    // Kelola Kunjungan
    $routes->get('laporan-kunjungan', 'Admin::laporanKunjungan');

    // Carousel
    $routes->get('carousel', 'Admin::carousel');
    $routes->get('carousel/add', 'Admin::carouselAdd');
    $routes->post('carousel/save', 'Admin::carouselSave');
    $routes->get('tambah-carousel', 'Admin::addcarousel');
    $routes->get('edit-carousel/list', 'Admin::listcarousel');

    //Kelola Data Indikator
    $routes->get('data-indikator', 'Admin::dataIndikator');
    // === REGION ===
    $routes->get('regions', 'Admin::regions');                 // halaman
    $routes->post('regions/create', 'Admin::regionCreate');
    $routes->post('regions/update/(:num)', 'Admin::regionUpdate/$1');
    $routes->post('regions/delete/(:num)', 'Admin::regionDelete/$1');

    // === INDIKATOR ===
    $routes->get('indicators', 'Admin::indicators');           // halaman daftar indikator (pilih region)
    $routes->get('indicators/list', 'Admin::indicatorsList');  // ?region_id=ID (json)
    $routes->get('indicator/form', 'Admin::indicatorForm');    // create/edit ?id= (opsi)
    $routes->post('indicator/save', 'Admin::indicatorSave');   // create/update
    $routes->post('indicator/delete/(:num)', 'Admin::indicatorDelete/$1');

    // === SUBINDIKATOR ===
    $routes->get('subindicator/form', 'Admin::subindikatorForm');      // ?id= (edit) atau ?indicator_id=
    $routes->post('subindicator/save', 'Admin::subindikatorSave');     // create/update basic fields
    $routes->post('subindicator/delete/(:num)', 'Admin::subindikatorDelete/$1');

    // === VARIABEL (untuk data proporsi) ===
    $routes->post('subindicator/var/create', 'Admin::varCreate');      // {row_id, name}
    $routes->post('subindicator/var/delete/(:num)', 'Admin::varDelete/$1');

    // === VARIABEL: rename, bulk delete, list ===
    $routes->post('subindicator/var/update/(:num)', 'Admin::varUpdate/$1');     // rename 1 var
    $routes->post('subindicator/var/delete-bulk', 'Admin::varDeleteBulk');      // hapus banyak var
    $routes->get('subindicator/var/list/(:num)', 'Admin::varList/$1');          // list vars by row_id (JSON)

    // === GRID: daftar tahun yang punya data ===
    $routes->get('data-indikator/grid/years', 'Admin::gridYears');

    // === GRID: hapus tahun-tahun terpilih (multi) ===
    $routes->post('data-indikator/grid/delete-years', 'Admin::gridDeleteYears');


    // === AJAX untuk landing grid kamu ===
    $routes->get('data-indikator/ajax/indicators/(:num)', 'Admin::ajaxIndicatorsByRegion/$1');
    $routes->get('data-indikator/ajax/rows/(:num)/(:num)', 'Admin::ajaxRowsByRegionIndicator/$1/$2');
    $routes->get('data-indikator/grid/fetch', 'Admin::gridFetch');   // GET: region_id,row_id,year_from,year_to
    $routes->post('data-indikator/grid/save', 'Admin::gridSave');    // POST: entries batch


    // Infografis
    $routes->get('tambah-infografis', 'Admin::addInfografis');       // form tambah
    $routes->post('infografis/save',  'Admin::saveInfografis');      // simpan tambah

    $routes->get('edit-infografis/list',     'Admin::listInfografis');      // list dari DB
    $routes->get('edit-infografis/(:num)',   'Admin::editInfografis/$1');   // form edit
    $routes->post('edit-infografis/update/(:num)', 'Admin::updateInfografis/$1'); // simpan edit
    $routes->get('edit-infografis/delete/(:num)',  'Admin::deleteInfografis/$1'); // hapus

    $routes->get('/create', 'Admin::create');
    $routes->post('/save', 'Admin::save');
    $routes->get('/edit/(:num)', 'Admin::edit/$1');
    $routes->post('/update/(:num)', 'Admin::update/$1');
    $routes->get('/delete/(:num)', 'Admin::delete/$1');
});

// =======================
// AUTH ROUTE (login, register, forget)
// =======================
$routes->get('/login', 'Home::login');
$routes->post('/login', 'Home::doLogin');

$routes->get('register', 'Home::register');
$routes->post('register', 'Home::doRegister');

$routes->get('forget', 'Home::forget');          // form lupa
$routes->post('forget', 'Home::sendReset');      // kirim email

$routes->get('reset-password', 'Home::reset');   // GET: email+token
$routes->post('reset-password', 'Home::doReset'); // POST: simpan password

$routes->get('/logout', 'Home::logout', ['as' => 'logout']);


// =======================
// USER PROFILE
// =======================
$routes->get('user/profile', 'User::profile', ['as' => 'user.profile']);
$routes->post('user/profile/update', 'User::updateProfile', ['as' => 'user.profile.update']);
$routes->post('user/profile/password', 'User::updatePassword', ['as' => 'user.password.update']);

// =======================
// ADMIN PROFILE
// =======================
$routes->get('admin/profile', 'Admin::profile', ['as' => 'admin.profile']);
$routes->post('admin/profile/update', 'Admin::updateProfile', ['as' => 'admin.profile.update']);
$routes->post('admin/profile/password', 'Admin::updatePassword', ['as' => 'admin.password.update']);
