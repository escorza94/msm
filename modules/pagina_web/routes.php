<?php

// --- RUTAS PÚBLICAS DE LA TIENDA (FRONTEND CLIENTE) ---
$router->get('/', 'StorefrontController@index'); 
$router->get('/pagina', 'StorefrontController@pagina');
$router->get('/producto/{slug}', 'StorefrontController@verProducto');

// --- RUTAS PRIVADAS DEL PANEL DE ADMINISTRACIÓN (BACKEND ERP) ---
$router->get('/pagina_web', 'PaginaWebController@index');
$router->get('/pagina_web/colecciones', 'ColeccionesController@index');
$router->get('/pagina_web/colecciones/nuevo', 'ColeccionesController@nuevo');
$router->post('/pagina_web/colecciones/nuevo', 'ColeccionesController@postNuevo');
$router->get('/pagina_web/colecciones/cambiarEstado', 'ColeccionesController@cambiarEstado');
$router->get('/pagina_web/colecciones/editar', 'ColeccionesController@editar');
$router->post('/pagina_web/colecciones/editar', 'ColeccionesController@postEditar');
$router->get('/pagina_web/colecciones/eliminar', 'ColeccionesController@eliminar');

$router->get('/pagina_web/constructor', 'ConstructorController@index');
$router->get('/pagina_web/constructor/seccion', 'ConstructorController@seccion');
$router->post('/pagina_web/constructor/guardarSeccion', 'ConstructorController@guardarSeccion');
$router->get('/pagina_web/constructor/eliminarSeccion', 'ConstructorController@eliminarSeccion');
$router->post('/pagina_web/constructor/ordenar', 'ConstructorController@ordenar');

$router->get('/pagina_web/configuracion', 'ConfiguracionTemaController@index');
$router->post('/pagina_web/configuracion/guardar', 'ConfiguracionTemaController@guardar');

$router->get('/pagina_web/paginas', 'PaginasController@index');
$router->post('/pagina_web/paginas/guardar', 'PaginasController@guardar');
$router->get('/pagina_web/paginas/eliminar', 'PaginasController@eliminar');
$router->get('/pagina_web/paginas/cambiarEstado', 'PaginasController@cambiarEstado');