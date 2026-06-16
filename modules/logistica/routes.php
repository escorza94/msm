<?php
$router->get('/logistica', 'LogisticaController@index');
$router->get('/logistica/configuracion', 'LogisticaController@configuracion');
$router->get('/logistica/entregas', 'LogisticaController@entregas');
$router->post('/logistica/guardarConfiguracion', 'LogisticaController@guardarConfiguracion');
$router->post('/logistica/guardarTarifa', 'LogisticaController@guardarTarifa');
$router->get('/logistica/eliminarTarifa', 'LogisticaController@eliminarTarifa');
$router->post('/logistica/actualizarEstadoEnvio', 'LogisticaController@actualizarEstadoEnvio');
$router->get('/logistica/historial', 'LogisticaController@historial');
$router->get('/logistica/rutaGoogleMaps', 'LogisticaController@rutaGoogleMaps');
$router->get('/logistica/mapaRutas', 'LogisticaController@mapaRutas');
