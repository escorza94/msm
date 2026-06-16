<?php

// Rutas principales para el módulo pos

/** @var \Router $router */
$router->get('/pos', 'PosController@index');
$router->post('/pos/guardar', 'PosController@guardar');
$router->get('/pos/historial', 'PosController@historial');
$router->get('/pos/ver', 'PosController@ver');
