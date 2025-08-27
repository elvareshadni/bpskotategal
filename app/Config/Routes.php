<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('home/user', 'Home::user');
$routes->get('/data-indikator', 'Home::dataIndikator');
