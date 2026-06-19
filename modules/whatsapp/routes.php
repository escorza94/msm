<?php
/** @var \Router $router */
$router->get('/whatsapp', 'WhatsappController@index');
$router->get('/whatsapp/chat', 'WhatsappController@chat');
$router->get('/whatsapp/vincular', 'WhatsappController@vincular');
$router->get('/whatsapp/contactos', 'WhatsappController@contactos');
$router->get('/whatsapp/mensajes', 'WhatsappController@obtenerMensajes');
$router->post('/whatsapp/enviar', 'WhatsappController@enviarMensaje');
$router->post('/whatsapp/enviar-archivo', 'WhatsappController@enviarArchivo');
$router->post('/whatsapp/webhook', 'WhatsappController@webhookIncoming');
$router->get('/whatsapp/configuracion', 'WhatsappController@configuracion');
$router->post('/whatsapp/configuracion', 'WhatsappController@guardarConfiguracion');
$router->post('/whatsapp/toggleBot', 'WhatsappController@toggleBot');
$router->get('/whatsapp/panelCrm', 'WhatsappController@obtenerPanelCrm');
$router->post('/whatsapp/estado', 'WhatsappController@estado');
