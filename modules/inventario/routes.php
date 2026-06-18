<?php

// Rutas principales para el módulo inventario

/** @var \Router $router */
$router->get('/inventario', 'InventarioController@index');
$router->get('/inventario/nuevo', 'InventarioController@nuevo');
$router->post('/inventario/nuevo', 'InventarioController@postNuevo');
$router->get('/inventario/ver', 'InventarioController@ver');
$router->get('/inventario/editar', 'InventarioController@editar');
$router->post('/inventario/editar', 'InventarioController@postEditar');

$router->get('/inventario/categorias', 'CategoriasController@index');
$router->post('/inventario/categorias/guardar', 'CategoriasController@guardar');
$router->get('/inventario/categorias/eliminar', 'CategoriasController@eliminar');
