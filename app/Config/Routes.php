<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Route untuk user (dashboard / home user)
$routes->get('/user', 'User::index');
$routes->get('/user/beranda', 'User::beranda');

// Route untuk halaman auth (login, register, dll.)
$routes->get('/auth/register', 'Home::register');
$routes->get('/auth/forget', 'Home::forget');
