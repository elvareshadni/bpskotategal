<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'User::index'); // langsung ke dashboard user

$routes->group('user', function($routes) {
    $routes->get('/', 'User::index');          
    $routes->get('beranda', 'User::beranda');  
    $routes->get('list', 'User::list');       
    $routes->get('detail/(:num)', 'User::detail/$1'); 

    // Profile
    $routes->get('profile', 'User::profile', ['as' => 'user.profile']); 
    $routes->post('profile/update', 'User::updateProfile', ['as' => 'user.profile.update']);
    $routes->post('profile/password', 'User::updatePassword', ['as' => 'user.password.update']);
});

// API (proxy CSV -> JSON)
$routes->get('api/indikator', 'Indicators::index'); // dipakai oleh fetch() di JS

$routes->group('', function($routes) {
    $routes->get('login', 'Home::login');
    $routes->post('login', 'Home::doLogin');

    $routes->get('register', 'Home::register');
    $routes->post('register', 'Home::doRegister');

    $routes->get('forget', 'Home::forget');
});

// ADMIN
$routes->group('admin', function($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('laporan-kunjungan', 'Admin::laporanKunjungan');
    $routes->get('data-indikator', 'Admin::dataIndikator');
    $routes->get('edit-carousel', 'Admin::editcarousel');
    $routes->get('edit-infografis', 'Admin::editInfografis');

    // Kelola Data
    $routes->get('data-indikator', 'Admin::dataIndikator');
    $routes->get('laporan-kunjungan', 'Admin::laporanKunjungan');

    // Carousel
    $routes->get('carousel', 'Admin::carousel');
    $routes->get('carousel/add', 'Admin::carouselAdd');
    $routes->post('carousel/save', 'Admin::carouselSave');

    // Infografis
    $routes->get('infografis', 'Admin::infografis');
    $routes->get('infografis/add', 'Admin::infografisAdd');
    $routes->post('infografis/save', 'Admin::infografisSave');

    // Edit carousel & Infografis
    $routes->get('edit-carousel', 'Admin::editcarousel');        
    $routes->get('edit-carousel/list', 'Admin::listcarousel');     
    $routes->get('edit-infografis', 'Admin::editInfografis');       
    $routes->get('edit-infografis/list', 'Admin::listInfografis');   
});
