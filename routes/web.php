<?php
use App\Core\Router;

$router = new Router();

$router->get('/', 'HomeController@index');
$router->get('/product/{slug}', 'LandingController@show');
$router->post('/product/{slug}/lead', 'LandingController@lead');

// Auth
$router->get('/admin/login', 'AuthController@showLogin');
$router->post('/admin/login', 'AuthController@login');
$router->get('/admin/logout', 'AuthController@logout');

// Dashboard
$router->get('/admin', 'DashboardController@index');

// Products
$router->get('/admin/products', 'ProductController@index');
$router->get('/admin/products/create', 'ProductController@create');
$router->post('/admin/products/create', 'ProductController@store');
$router->get('/admin/products/{id}/edit', 'ProductController@edit');
$router->post('/admin/products/{id}/edit', 'ProductController@update');
$router->post('/admin/products/{id}/delete', 'ProductController@delete');
$router->post('/admin/products/{id}/duplicate', 'ProductController@duplicate');

// Leads
$router->get('/admin/leads', 'LeadController@index');
$router->get('/admin/leads/export', 'LeadController@export');

// Settings
$router->any('/admin/settings', 'SettingsController@index');

return $router;
