const express = require('express');
const cors = require('cors');
const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const qrcode = require('qrcode');
const qrcodeTerminal = require('qrcode-terminal');
const { Server } = require('socket.io');
const http = require('http');
const os = require('os');

const PHP_WEBHOOK_URL = `http://localhost/whatsapp/webhook`;
const app = express();
const server = http.createServer(app);
const io = new Server(server, { cors: { origin: '*' } });

app.use(cors());
app.use(express.json({ limit: '50mb' }));

const CHROME_PATH_WINDOWS = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const CHROMIUM_PATH_LINUX = '/usr/bin/chromium-browser';
const executablePath = os.platform() === 'win32' ? CHROME_PATH_WINDOWS : CHROMIUM_PATH_LINUX;

const client = new Client({
    authStrategy: new LocalAuth(),
    puppeteer: {
        headless: true,
        executablePath: executablePath,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-extensions'
        ]
    }
});

// 1. Mapa para asociar IDs: ID de WhatsApp -> ID de la Base de Datos
const messageIdMap = {};

let qrCodeDataUrl = '';
let isConnected = false;

client.on('qr', async (qr) => {
    console.log('Generando nuevo código QR para escanear...');
    qrcodeTerminal.generate(qr, { small: true });
    qrCodeDataUrl = await qrcode.toDataURL(qr);
    io.emit('qr_update', qrCodeDataUrl);
});

client.on('ready', () => {
    console.log('✅ ¡WhatsApp vinculado y listo!');
    isConnected = true;
    qrCodeDataUrl = '';
    io.emit('wa_ready');
});

client.on('message', async (msg) => {
    let cuerpoMensaje = msg.body;
    let nombreContacto = msg.from.split('@')[0];
    let tipoMensaje = 'texto';
    let archivoData = null;

    if (msg.hasMedia) {
        try {
            const media = await msg.downloadMedia();
            if (media) {
                archivoData = `data:${media.mimetype};base64,${media.data}`;
                if (media.mimetype.includes('image')) tipoMensaje = 'imagen';
                else if (media.mimetype.includes('audio') || media.mimetype.includes('ogg')) tipoMensaje = 'audio';
                else if (media.mimetype.includes('video')) tipoMensaje = 'video';
                else tipoMensaje = 'archivo';
            }
        } catch (e) { console.error('Error al descargar media entrante:', e); }
    }

    if (msg.from === 'status@broadcast') {
        const remitente = msg.author ? msg.author.split('@')[0] : 'Alguien';
        cuerpoMensaje = msg.hasMedia ? cuerpoMensaje : `[Estado de ${remitente}]: ${msg.body}`;
        nombreContacto = 'Feed de Estados';
    } else {
        try {
            const contact = await msg.getContact();
            nombreContacto = contact.name || contact.pushname || nombreContacto;
        } catch (e) { /* Ignorar si falla la consulta del contacto */ }
    }
    console.log(`📩 Mensaje RECIBIDO de ${msg.from} (${nombreContacto}) - Tipo: ${tipoMensaje}`);
    io.emit('mensaje_entrante', { whatsapp_id: msg.from, mensaje: cuerpoMensaje, archivo: archivoData, timestamp: msg.timestamp, nombre: nombreContacto, tipo: tipoMensaje });
    try { await fetch(PHP_WEBHOOK_URL, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ whatsapp_id: msg.from, mensaje: cuerpoMensaje, archivo: archivoData, timestamp: msg.timestamp, nombre: nombreContacto, tipo: tipoMensaje, direccion: 'entrante' }) }); } catch (err) { console.error('Error Webhook:', err.message); }
});

client.on('message_create', async (msg) => {
    if (msg.fromMe && !msg.body.endsWith('\u200B')) {
        let cuerpoMensaje = msg.body;
        let tipoMensaje = 'texto';
        let archivoData = null;

        if (msg.hasMedia) {
            try {
                const media = await msg.downloadMedia();
                if (media) {
                    archivoData = `data:${media.mimetype};base64,${media.data}`;
                    if (media.mimetype.includes('image')) tipoMensaje = 'imagen';
                    else if (media.mimetype.includes('audio') || media.mimetype.includes('ogg')) tipoMensaje = 'audio';
                    else if (media.mimetype.includes('video')) tipoMensaje = 'video';
                    else tipoMensaje = 'archivo';
                }
            } catch (e) { console.error('Error al descargar media saliente:', e); }
        }

        console.log(`📱 Mensaje ENVIADO desde otro dispositivo a ${msg.to} - Tipo: ${tipoMensaje}`);
        io.emit('mensaje_saliente_fisico', { whatsapp_id: msg.to, mensaje: cuerpoMensaje, archivo: archivoData, timestamp: msg.timestamp, tipo: tipoMensaje });
        try { await fetch(PHP_WEBHOOK_URL, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ whatsapp_id: msg.to, mensaje: cuerpoMensaje, archivo: archivoData, timestamp: msg.timestamp, nombre: msg.to.split('@')[0], tipo: tipoMensaje, direccion: 'saliente' }) }); } catch (err) { console.error('Error Webhook (Saliente):', err.message); }
    }
});

// 2. Escuchar los cambios de estado de los mensajes (enviado, entregado, leído)
client.on('message_ack', (msg, ack) => {
    const whatsappId = msg.id._serialized;
    const dbId = messageIdMap[whatsappId];

    if (!dbId) return; // Si no tenemos este ID en nuestro mapa, lo ignoramos.

    let status = 'enviado';
    if (ack === 2) status = 'entregado';
    else if (ack === 3) status = 'leido';
    else if (ack === -1) status = 'fallido';

    console.log(`✅ ACK Update: Mensaje DB ID ${dbId} ahora está ${status}`);

    // Emitir el evento al frontend con el ID de la base de datos
    io.emit('ack_update', {
        id: dbId,
        status: status
    });

    // Si el mensaje ya fue leído, lo podemos eliminar del mapa para ahorrar memoria
    if (ack === 3) {
        delete messageIdMap[whatsappId];
    }
});

client.initialize();

app.get('/api/status', (req, res) => res.json({ isConnected, qr: qrCodeDataUrl }));

// 3. Modificar el endpoint de envío para que use el mapa de IDs
app.post('/api/enviar', async (req, res) => {
    // Leemos los IDs de la base de datos que nos manda PHP
    const { numero, mensaje, archivo, nombreArchivo, mensaje_ids } = req.body;
    console.log(`💻 Petición del CRM para ENVIAR a ${numero} (DB IDs: ${mensaje_ids})`);

    if (!isConnected) return res.status(500).json({ error: 'WhatsApp no está vinculado' });

    try {
        const chatId = numero.includes('@') ? numero : `${numero}@c.us`;
        const sentMessagePromises = [];

        // Preparamos el envío de archivo si existe
        if (archivo) {
            const matches = archivo.match(/^data:([A-Za-z-+\/]+);base64,(.+)$/);
            if (matches && matches.length === 3) {
                const media = new MessageMedia(matches[1], matches[2], nombreArchivo);
                // Si hay mensaje de texto, lo enviamos como caption del archivo
                const options = (mensaje) ? { caption: mensaje + '\u200B' } : { caption: '\u200B' };
                sentMessagePromises.push(client.sendMessage(chatId, media, options));
            }
        } else if (mensaje) {
            // Si no hay archivo pero sí mensaje, lo enviamos
            sentMessagePromises.push(client.sendMessage(chatId, mensaje + '\u200B'));
        }

        // Esperamos a que WhatsApp nos devuelva los objetos de los mensajes enviados
        const sentMessages = await Promise.all(sentMessagePromises);

        // Asociamos los IDs de WhatsApp con los IDs de nuestra base de datos
        sentMessages.forEach((sentMsg, index) => {
            const whatsappId = sentMsg.id._serialized;
            const dbId = mensaje_ids[index];
            if (whatsappId && dbId) {
                messageIdMap[whatsappId] = dbId;
                console.log(`🔗 Asociación creada: ${whatsappId} -> ${dbId}`);
            }
        });

        // Devolvemos una respuesta exitosa a PHP
        res.json({ success: true, mensaje_ids: mensaje_ids });

    } catch (err) {
        console.error(`❌ Error al enviar mensaje:`, err.message || err);
        res.status(500).json({ error: err.message || 'Error desconocido' });
    }
});

server.listen(3000, () => console.log('🤖 Node.js Gateway en http://127.0.0.1:3000'));

const gracefulShutdown = async () => {
    console.log('\n🛑 Cerrando la conexión de WhatsApp de forma segura...');
    try {
        await client.destroy();
        console.log('✅ Cliente cerrado correctamente.');
        process.exit(0);
    } catch (err) {
        console.error('❌ Error al cerrar el cliente:', err);
        process.exit(1);
    }
};

process.on('SIGINT', gracefulShutdown);
process.on('SIGTERM', gracefulShutdown);
