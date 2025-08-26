<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// =======================
// USER ROUTE
// =======================
$routes->get('/user', 'User::index');
$routes->get('/user/beranda', 'User::beranda');
$routes->get('/user/list', 'User::list');
$routes->get('/user/detail/(:num)', 'User::detail/$1');

// API (proxy CSV -> JSON)
$routes->get('api/indikator', 'Indicators::index'); // dipakai oleh fetch() di JS

// =======================
// AUTH ROUTE (login, register, forget)
// =======================
$routes->get('/login', 'Home::login');
$routes->post('/login', 'Home::doLogin');

$routes->get('/register', 'Home::register');
$routes->post('/register', 'Home::doRegister');

$routes->get('/forget', 'Home::forget');

// =======================
// ADMIN ROUTE
// =======================
$routes->get('/admin', 'Admin::index');
$routes->get('/admin/profile', 'Admin::profile');
$routes->post('/admin/updateUser', 'Admin::updateUser');

$routes->get('/admin/create', 'Admin::create');
$routes->post('/admin/save', 'Admin::save');
$routes->get('/admin/edit/(:num)', 'Admin::edit/$1');
$routes->post('/admin/update/(:num)', 'Admin::update/$1');
$routes->get('/admin/delete/(:num)', 'Admin::delete/$1');
