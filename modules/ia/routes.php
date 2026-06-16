<?php

$router->get('/ia', 'IaController@index');
$router->get('/ia/herramientas', 'IaController@herramientas');
$router->post('/ia/chatAPI', 'IaController@chatAPI');
$router->get('/ia/historialInterno', 'IaController@historialInterno');
$router->post('/ia/chatInterno', 'IaController@chatInterno');
$router->post('/ia/toggleHerramienta', 'IaController@toggleHerramienta');
$router->get('/ia/prompts', 'IaController@prompts');
$router->post('/ia/guardarPrompts', 'IaController@guardarPrompts');
$router->post('/ia/generarPromptConIA', 'IaController@generarPromptConIA');