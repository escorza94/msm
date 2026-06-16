<?php

/** @var \Router $router */
$router->get('/help', 'HelpController@index');
$router->get('/help/nuevo', 'HelpController@nuevo');
$router->post('/help/nuevo', 'HelpController@postNuevo');
$router->get('/help/editar', 'HelpController@editar');
$router->post('/help/editar', 'HelpController@postEditar');
$router->get('/help/ver', 'HelpController@ver');
$router->post('/help/categoria', 'HelpController@postCategoria');
$router->get('/help/cambiarEstado', 'HelpController@cambiarEstado');