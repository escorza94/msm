<?php

// Rutas principales para el módulo promociones

/** @var \Router $router */
$router->get('/promociones', 'PromocionesController@index');
$router->get('/promociones/nuevo', 'PromocionesController@nuevo');
$router->post('/promociones/nuevo', 'PromocionesController@postNuevo');
$router->get('/promociones/editar', 'PromocionesController@editar');
$router->post('/promociones/editar', 'PromocionesController@postEditar');
$router->get('/promociones/ver', 'PromocionesController@ver');
$router->get('/promociones/cambiarEstado', 'PromocionesController@cambiarEstado');
$router->post('/promociones/validarCupon', 'PromocionesController@validarCupon');
