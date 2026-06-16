<?php

// Rutas principales para el módulo proveedores

/** @var \Router $router */
$router->get('/proveedores', 'ProveedoresController@index');
$router->get('/proveedores/nuevo', 'ProveedoresController@nuevo');
$router->post('/proveedores/nuevo', 'ProveedoresController@postNuevo');
$router->get('/proveedores/editar', 'ProveedoresController@editar');
$router->post('/proveedores/editar', 'ProveedoresController@postEditar');