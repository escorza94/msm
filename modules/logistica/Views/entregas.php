<div class="max-w-7xl mx-auto mt-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center"><i class="fas fa-boxes text-blue-500 mr-3"></i> Tablero de Entregas (Kanban)</h2>
            <p class="text-sm text-gray-500 mt-1">Arrastra o usa los botones para avanzar el pedido hacia su entrega final.</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('logistica/historial') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-list mr-2 text-gray-400"></i> Historial Completo
            </a>
            <a href="<?= base_url('logistica/mapaRutas') ?>" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-map-marked-alt mr-2"></i> Mapa de Entregas
            </a>
            <a href="<?= base_url('logistica') ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition text-sm flex items-center shadow-sm">
                <i class="fas fa-arrow-left mr-2 text-gray-400"></i> Volver
            </a>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Pendientes -->
        <div class="bg-gray-100 rounded-xl p-4 border border-gray-200 flex flex-col h-[600px]">
            <h4 class="font-bold text-gray-700 mb-3 flex items-center justify-between">
                <span><i class="fas fa-clock text-amber-500 mr-2"></i> Pendientes</span>
                <span class="bg-white text-gray-600 px-2 py-0.5 rounded-full text-xs shadow-sm" id="count-pendiente">0</span>
            </h4>
            <div class="flex-1 overflow-y-auto space-y-3 pr-1 kanban-column pb-4 border-2 border-dashed border-transparent hover:border-amber-300 rounded-lg p-1 transition" id="col-pendiente" data-estado="pendiente" ondrop="drop(event)" ondragover="allowDrop(event)"></div>
        </div>

        <!-- En Ruta -->
        <div class="bg-blue-50 rounded-xl p-4 border border-blue-100 flex flex-col h-[600px]">
            <h4 class="font-bold text-blue-800 mb-3 flex items-center justify-between">
                <span><i class="fas fa-truck-fast text-blue-500 mr-2"></i> En Ruta</span>
                <span class="bg-white text-blue-600 px-2 py-0.5 rounded-full text-xs shadow-sm" id="count-en_ruta">0</span>
            </h4>
            <div class="flex-1 overflow-y-auto space-y-3 pr-1 kanban-column pb-4 border-2 border-dashed border-transparent hover:border-blue-300 rounded-lg p-1 transition" id="col-en_ruta" data-estado="en_ruta" ondrop="drop(event)" ondragover="allowDrop(event)"></div>
        </div>

        <!-- Entregados -->
        <div class="bg-green-50 rounded-xl p-4 border border-green-100 flex flex-col h-[600px]">
            <h4 class="font-bold text-green-800 mb-3 flex items-center justify-between">
                <span><i class="fas fa-check-circle text-green-500 mr-2"></i> Entregados</span>
                <span class="bg-white text-green-600 px-2 py-0.5 rounded-full text-xs shadow-sm" id="count-entregado">0</span>
            </h4>
            <p class="text-xs text-gray-500 text-center mb-2">Arrastra aquí para marcar como entregado</p>
            <div class="flex-1 overflow-y-auto space-y-3 pr-1 kanban-column pb-4 border-2 border-dashed border-transparent hover:border-green-300 rounded-lg p-1 transition" id="col-entregado" data-estado="entregado" ondrop="drop(event)" ondragover="allowDrop(event)"></div>
        </div>
    </div>
</div>

<script>
const envios = <?= json_encode($envios ?? []) ?>;

document.addEventListener('DOMContentLoaded', () => {
    if(typeof envios !== 'undefined') renderKanban();
});

function renderKanban() {
    const cols = {
        'pendiente': document.getElementById('col-pendiente'),
        'en_ruta': document.getElementById('col-en_ruta'),
        'entregado': document.getElementById('col-entregado')
    };
    
    Object.values(cols).forEach(col => { if(col) col.innerHTML = ''; });
    
    let counts = { 'pendiente': 0, 'en_ruta': 0, 'entregado': 0 };

    envios.forEach(envio => {
        if (!cols[envio.estado]) return;
        counts[envio.estado]++;
        
        if (envio.estado === 'entregado') {
            // No renderizamos las tarjetas entregadas en el kanban
            return;
        }

        let botones = '';
        if (envio.estado === 'pendiente') {
            botones = `<button onclick="cambiarEstado(${envio.id}, 'en_ruta')" class="w-full py-1.5 mt-2 bg-blue-600 text-white text-xs font-bold rounded shadow-sm hover:bg-blue-700 transition">Marcar En Ruta</button>`;
        } else if (envio.estado === 'en_ruta') {
            botones = `
                <div class="flex gap-2 mt-2">
                    <button onclick="cambiarEstado(${envio.id}, 'pendiente')" class="px-3 py-1.5 bg-gray-200 text-gray-700 text-xs font-bold rounded shadow-sm hover:bg-gray-300 transition" title="Regresar a Pendiente"><i class="fas fa-undo"></i></button>
                    <button onclick="cambiarEstado(${envio.id}, 'entregado')" class="flex-1 py-1.5 bg-green-600 text-white text-xs font-bold rounded shadow-sm hover:bg-green-700 transition">Marcar Entregado</button>
                </div>
            `;
        } else if (envio.estado === 'entregado') {
            botones = `<p class="text-[10px] text-green-600 font-bold mt-2 text-center"><i class="fas fa-check-double"></i> Entregado el ${envio.fecha_entrega}</p>`;
        }

        const mRestante = parseFloat(envio.restante) || 0;
        let pagoHtml = '';
        if (mRestante > 0) {
            pagoHtml = `<p class="text-xs font-bold text-red-500 mt-1"><i class="fas fa-hand-holding-usd mr-1 text-xs"></i> Resta Cobrar: $${mRestante.toLocaleString('en-US', {minimumFractionDigits: 2})}</p>`;
        } else {
            pagoHtml = `<p class="text-xs font-bold text-green-600 mt-1"><i class="fas fa-check-circle mr-1 text-xs"></i> Pagado</p>`;
        }

        const html = `
            <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition cursor-move" draggable="true" ondragstart="drag(event)" data-id="${envio.id}">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-xs font-bold px-2 py-1 bg-gray-100 text-gray-600 rounded">Venta #${envio.folio_venta.toString().padStart(5, '0')}</span>
                    <a href="<?= base_url('pos/ver?id=') ?>${envio.folio_venta}" target="_blank" class="text-blue-500 hover:text-blue-700 text-xs" title="Ver Nota"><i class="fas fa-external-link-alt"></i></a>
                </div>
                <h5 class="text-sm font-bold text-gray-800 truncate" title="${envio.cliente_nombre || 'Cliente General'}"><i class="fas fa-user text-gray-400 mr-1 text-xs"></i> ${envio.cliente_nombre || 'Cliente General'}</h5>
                ${envio.cliente_telefono ? `<p class="text-xs text-gray-500 mt-1"><i class="fas fa-phone-alt text-gray-400 mr-1 text-xs"></i> ${envio.cliente_telefono}</p>` : ''}
                ${pagoHtml}
                
                ${envio.direccion_destino ? `<p class="text-[10px] text-gray-500 mt-2 bg-gray-50 p-1.5 rounded border border-gray-100 leading-tight"><i class="fas fa-map-marker-alt text-red-400 mr-1"></i> ${envio.direccion_destino}</p>` : ''}
                ${envio.coordenadas_destino ? `<a href="https://www.google.com/maps/search/?api=1&query=${envio.coordenadas_destino}" target="_blank" class="text-[10px] text-blue-500 mt-2 bg-blue-50 hover:bg-blue-100 transition px-2 py-1 rounded inline-block cursor-pointer"><i class="fas fa-map-marker-alt"></i> Ver en Mapa</a>` : `<p class="text-[10px] text-amber-500 mt-2 bg-amber-50 px-2 py-1 rounded inline-block"><i class="fas fa-exclamation-triangle"></i> Sin coordenadas</p>`}
                
                ${botones}
            </div>
        `;
        cols[envio.estado].insertAdjacentHTML('beforeend', html);
    });
    
    Object.keys(counts).forEach(key => {
        const el = document.getElementById(`count-${key}`);
        if(el) el.innerText = counts[key];
    });
}

function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text/plain", ev.currentTarget.dataset.id);
}

async function drop(ev) {
    ev.preventDefault();
    const id = ev.dataTransfer.getData("text/plain");
    const targetCol = ev.currentTarget;
    const nuevoEstado = targetCol.dataset.estado;
    
    const envio = envios.find(e => e.id == id);
    if (envio && envio.estado !== nuevoEstado) {
        await cambiarEstado(id, nuevoEstado);
    }
}

async function cambiarEstado(id, nuevoEstado) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('estado', nuevoEstado);
    
    try {
        const res = await fetch('<?= base_url('logistica/actualizarEstadoEnvio') ?>', { method: 'POST', body: formData });
        const data = await res.json();
        
        if (data.status === 'success') {
            const envio = envios.find(e => e.id == id);
            if (envio) { envio.estado = nuevoEstado; if (nuevoEstado === 'entregado') { const d = new Date(); envio.fecha_entrega = d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0') + ' ' + String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0') + ':' + String(d.getSeconds()).padStart(2, '0'); } else { envio.fecha_entrega = null; } }
            renderKanban();
        } else { alert('Error: ' + data.error); }
    } catch (e) { alert('Error de conexión'); }
}
</script>