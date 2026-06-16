<?php

// Rutas principales para el módulo notificaciones

/** @var \Router $router */
$router->get('/notificaciones', 'NotificacionesController@index');
$router->get('/notificaciones/obtenerNoLeidas', 'NotificacionesController@obtenerNoLeidas');
$router->post('/notificaciones/marcarLeida', 'NotificacionesController@marcarLeida');
$router->post('/notificaciones/marcarNoLeida', 'NotificacionesController@marcarNoLeida');
