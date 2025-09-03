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

// API (proxy CSV -> JSON)
$routes->get('api/indikator', 'Indicators::index'); // dipakai oleh fetch() di JS

// ADMIN
$routes->group('admin', function ($routes) {
    $routes->get('/', 'Admin::index');

    // Kelola Data
    $routes->get('data-indikator', 'Admin::dataIndikator');
    $routes->get('laporan-kunjungan', 'Admin::laporanKunjungan');

    // Carousel
    $routes->get('carousel', 'Admin::carousel');
    $routes->get('carousel/add', 'Admin::carouselAdd');
    $routes->post('carousel/save', 'Admin::carouselSave');
    $routes->get('tambah-carousel', 'Admin::addcarousel');
    $routes->get('edit-carousel/list', 'Admin::listcarousel');

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
