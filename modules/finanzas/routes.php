<?php

// Rutas principales para el módulo finanzas

/** @var \Router $router */
$router->get('/finanzas', 'FinanzasController@index');
$router->post('/finanzas/movimiento', 'FinanzasController@guardarMovimiento');
$router->post('/finanzas/traspaso', 'FinanzasController@guardarTraspaso');
$router->post('/finanzas/cuenta', 'FinanzasController@guardarCuenta');
$router->get('/finanzas/cobrar', 'FinanzasController@cuentasPorCobrar');
$router->post('/finanzas/abono', 'FinanzasController@guardarAbono');
$router->get('/finanzas/ver_movimiento', 'FinanzasController@verMovimiento');
$router->get('/pos/marcarEntregado', 'PosController@marcarEntregado');