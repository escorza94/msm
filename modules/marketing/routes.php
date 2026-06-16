<?php

/** @var \Router $router */
$router->get('/marketing', 'MarketingController@index');
$router->get('/marketing/nuevo', 'MarketingController@nuevo');
$router->post('/marketing/nuevo', 'MarketingController@postNuevo');
$router->get('/marketing/editar', 'MarketingController@editar');
$router->post('/marketing/editar', 'MarketingController@postEditar');
$router->get('/marketing/cambiarEstado', 'MarketingController@cambiarEstado');