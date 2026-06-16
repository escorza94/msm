<?php

// Rutas principales para el módulo documentos

/** @var \Router $router */
$router->get('/documentos', 'DocumentosController@index');
$router->post('/documentos/guardarConfiguracion', 'DocumentosController@guardarConfiguracion');
$router->get('/documentos/ticket', 'DocumentosController@ticket');
