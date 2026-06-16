<?php

/** @var \Router $router */
$router->get('/clientes', 'ClientesController@index');
$router->get('/clientes/nuevo', 'ClientesController@nuevo');
$router->post('/clientes/nuevo', 'ClientesController@postNuevo');
$router->get('/clientes/editar', 'ClientesController@editar');
$router->post('/clientes/editar', 'ClientesController@postEditar');
$router->get('/clientes/ver', 'ClientesController@ver');
