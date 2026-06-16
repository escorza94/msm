<?php

// Rutas principales para el módulo admin_core

/** @var \Router $router */
$router->get('/admin_core', 'Admin_coreController@index');
$router->post('/admin_core/update-config', 'Admin_coreController@updateConfig');
$router->post('/admin_core/run-migrations', 'Admin_coreController@runMigrations');
$router->get('/admin_core/ajustes', 'AjustesController@index');
$router->post('/admin_core/ajustes', 'AjustesController@guardar');
$router->post('/admin_core/update_module', 'Admin_coreController@updateModuleConfig');
