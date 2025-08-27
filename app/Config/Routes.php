<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('home/user', 'Home::user');
$routes->get('/data-indikator', 'Home::dataIndikator');
$routes->get('/laporan-kunjungan', 'Home::laporanKunjungan');

$routes->get('/edit-carousell', 'Home::editCarousell');          // Form tambah
$routes->get('/edit-carousell/list', 'Home::listCarousell');     // Daftar/edit existing

$routes->get('/edit-infografis', 'Home::editInfografis');        // Form tambah
$routes->get('/edit-infografis/list', 'Home::listInfografis');   // Daftar/edit existing