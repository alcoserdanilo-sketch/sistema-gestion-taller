const API = 'api.php';

const MODULE_DEFINITIONS = {
    clients: {
        title: 'Clientes',
        fields: [
            { name: 'name', label: 'Nombre', type: 'text' },
            { name: 'dni', label: 'DNI', type: 'text' },
            { name: 'phone', label: 'Teléfono', type: 'text' },
            { name: 'email', label: 'Email', type: 'email' },
            { name: 'address', label: 'Dirección', type: 'text' },
            { name: 'notes', label: 'Notas', type: 'textarea' }
        ]
    },
    vehicles: {
        title: 'Vehículos',
        fields: [
            { name: 'client_id', label: 'Cliente', type: 'number' },
            { name: 'brand', label: 'Marca', type: 'text' },
            { name: 'model', label: 'Modelo', type: 'text' },
            { name: 'plate', label: 'Matrícula', type: 'text' },
            { name: 'vin', label: 'VIN', type: 'text' },
            { name: 'year', label: 'Año', type: 'number' },
            { name: 'km', label: 'Km', type: 'number' },
            { name: 'notes', label: 'Notas', type: 'textarea' }
        ]
    },
    budgets: {
        title: 'Presupuestos',
        fields: [
            { name: 'client_id', label: 'Cliente', type: 'number' },
            { name: 'vehicle_id', label: 'Vehículo', type: 'number' },
            { name: 'number', label: 'Número', type: 'text' },
            { name: 'date', label: 'Fecha', type: 'date' },
            { name: 'amount', label: 'Importe', type: 'number', step: '0.01' },
            { name: 'status', label: 'Estado', type: 'select', options: ['pendiente','aceptado','rechazado','cerrado'] },
            { name: 'notes', label: 'Notas', type: 'textarea' }
        ]
    },
    work_orders: {
        title: 'Órdenes de trabajo',
        fields: [
            { name: 'client_id', label: 'Cliente', type: 'number' },
            { name: 'vehicle_id', label: 'Vehículo', type: 'number' },
            { name: 'number', label: 'Número', type: 'text' },
            { name: 'issue', label: 'Avería', type: 'textarea' },
            { name: 'date', label: 'Fecha', type: 'date' },
            { name: 'status', label: 'Estado', type: 'select', options: ['abierta','en_proceso','finalizado','parada'] },
            { name: 'labor_hours', label: 'Horas', type: 'number', step: '0.5' },
            { name: 'amount', label: 'Importe', type: 'number', step: '0.01' },
            { name: 'notes', label: 'Notas', type: 'textarea' }
        ]
    },
    invoices: {
        title: 'Facturas',
        fields: [
            { name: 'client_id', label: 'Cliente', type: 'number' },
            { name: 'work_order_id', label: 'Orden', type: 'number' },
            { name: 'number', label: 'Número', type: 'text' },
            { name: 'date', label: 'Fecha', type: 'date' },
            { name: 'amount', label: 'Importe', type: 'number', step: '0.01' },
            { name: 'status', label: 'Estado', type: 'select', options: ['pendiente','pagada','anulada'] },
            { name: 'notes', label: 'Notas', type: 'textarea' }
        ]
    },
    agenda: {
        title: 'Agenda',
        fields: [
            { name: 'date', label: 'Fecha', type: 'date' },
            { name: 'time', label: 'Hora', type: 'time' },
            { name: 'title', label: 'Título', type: 'text' },
            { name: 'client_id', label: 'Cliente', type: 'number' },
            { name: 'vehicle_id', label: 'Vehículo', type: 'number' },
            { name: 'status', label: 'Estado', type: 'select', options: ['programado','realizado','pendiente','cancelado'] },
            { name: 'notes', label: 'Notas', type: 'textarea' }
        ]
    },
    spare_parts: {
        title: 'Repuestos',
        fields: [
            { name: 'code', label: 'Código', type: 'text' },
            { name: 'name', label: 'Nombre', type: 'text' },
            { name: 'category', label: 'Categoría', type: 'text' },
            { name: 'unit_price', label: 'Precio unitario', type: 'number', step: '0.01' },
            { name: 'stock_min', label: 'Stock mínimo', type: 'number', step: '1' }
        ]
    },
    purchases: {
        title: 'Compras',
        fields: [
            { name: 'part_id', label: 'Repuesto', type: 'number' },
            { name: 'supplier', label: 'Proveedor', type: 'text' },
            { name: 'quantity', label: 'Cantidad', type: 'number', step: '0.01' },
            { name: 'unit_price', label: 'Precio unitario', type: 'number', step: '0.01' },
            { name: 'date', label: 'Fecha', type: 'date' },
            { name: 'invoice_number', label: 'Nº Factura', type: 'text' }
        ]
    },
    stock: {
        title: 'Stock',
        fields: [
            { name: 'part_id', label: 'Repuesto', type: 'number' },
            { name: 'quantity', label: 'Cantidad', type: 'number', step: '0.01' },
            { name: 'warehouse', label: 'Almacén', type: 'text' }
        ]
    },
    repairs: {
        title: 'Reparaciones',
        fields: [
            { name: 'client_id', label: 'Cliente', type: 'number' },
            { name: 'vehicle_id', label: 'Vehículo', type: 'number' },
            { name: 'issue', label: 'Avería', type: 'textarea' },
            { name: 'diagnosis', label: 'Diagnóstico', type: 'textarea' },
            { name: 'status', label: 'Estado', type: 'select', options: ['en_revision','en_reparacion','pendiente_piezas','finalizado'] },
            { name: 'started_at', label: 'Inicio', type: 'date' },
            { name: 'completed_at', label: 'Fin', type: 'date' },
            { name: 'notes', label: 'Notas', type: 'textarea' }
        ]
    }
};

const state = {
    activeSection: 'dashboard',
    user: null
};

async function apiFetch(action, payload = {}) {
    const response = await fetch(`${API}?action=${encodeURIComponent(action)}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    });

    return await response.json();
}

function showLogin() {
    document.getElementById('login-screen').classList.remove('hidden');
    document.getElementById('app-screen').classList.add('hidden');
}

function showApp() {
    document.getElementById('login-screen').classList.add('hidden');
    document.getElementById('app-screen').classList.remove('hidden');
    document.getElementById('userLabel').textContent = state.user ? state.user.name : '-';
}

async function checkSession() {
    const result = await apiFetch('session');
    if (result.success && result.logged) {
        state.user = result.user;
        showApp();
        loadDashboard();
        renderModules();
    } else {
        state.user = null;
        showLogin();
    }
}

async function handleLogin(event) {
    event.preventDefault();
    const form = new FormData(event.target);
    const username = String(form.get('username') || '').trim();
    const password = String(form.get('password') || '').trim();

    const response = await apiFetch('login', { username, password });
    if (response.success) {
        state.user = response.user;
        showApp();
        loadDashboard();
        renderModules();
    } else {
        alert(response.error || 'Error al iniciar sesión');
    }
}

async function handleLogout() {
    await apiFetch('logout');
    state.user = null;
    showLogin();
}

function initNavigation() {
    document.querySelectorAll('.nav-btn').forEach(button => {
        button.addEventListener('click', () => {
            state.activeSection = button.dataset.section;
            document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.toggle('active', btn === button));
            document.querySelectorAll('.panel').forEach(panel => {
                const isVisible = panel.id === `${state.activeSection}-section`;
                panel.classList.toggle('hidden', !isVisible);
                panel.classList.toggle('active', isVisible);
            });
        });
    });
}

function getTableColumns(moduleKey) {
    const columns = MODULE_DEFINITIONS[moduleKey].fields.map(field => field.label);
    return columns.concat(['Acciones']);
}

function renderModuleForm(moduleKey) {
    const definition = MODULE_DEFINITIONS[moduleKey];
    const fields = definition.fields.map(field => {
        const label = `<label><span>${field.label}</span>${renderInput(field)}</label>`;
        return label;
    }).join('');

    const section = document.getElementById(`${moduleKey}-section`);
    section.innerHTML = `
        <div class="panel-header">
            <h3>${definition.title}</h3>
        </div>
        <div class="module-box">
            <form class="module-form" data-module="${moduleKey}">
                <input type="hidden" name="id" value="">
                ${fields}
                <div class="inline-actions">
                    <button type="submit" class="primary">Guardar</button>
                    <button type="button" class="secondary clear-form">Limpiar</button>
                </div>
            </form>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>${getTableColumns(moduleKey).map(col => `<th>${col}</th>`).join('')}</tr>
                    </thead>
                    <tbody id="${moduleKey}-tbody"></tbody>
                </table>
            </div>
        </div>
    `;

    const form = section.querySelector('form');
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const data = Object.fromEntries(new FormData(form).entries());
        const result = await apiFetch(moduleKey, { mode: 'save', ...data });
        if (result.success) {
            await loadModule(moduleKey);
            form.reset();
        } else {
            alert(result.error || 'Error guardando registro');
        }
    });

    section.querySelector('.clear-form').addEventListener('click', () => {
        form.reset();
        form.querySelector('[name="id"]').value = '';
    });

    loadModule(moduleKey);
}

function renderInput(field) {
    const common = `name="${field.name}"` + (field.step ? ` step="${field.step}"` : '') + (field.type === 'number' ? ' inputmode="numeric"' : '');

    if (field.type === 'textarea') {
        return `<textarea ${common}></textarea>`;
    }

    if (field.type === 'select') {
        const options = field.options.map(option => `<option value="${option}">${option}</option>`).join('');
        return `<select ${common}>${options}</select>`;
    }

    return `<input type="${field.type}" ${common}>`;
}

function formatValue(value) {
    if (value === null || value === undefined || value === '') {
        return '-';
    }
    return value;
}

function renderTableRows(moduleKey, rows) {
    const tbody = document.getElementById(`${moduleKey}-tbody`);
    if (!tbody) return;

    if (!Array.isArray(rows) || rows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${MODULE_DEFINITIONS[moduleKey].fields.length + 1}" class="notice">No hay registros.</td></tr>`;
        return;
    }

    const headers = MODULE_DEFINITIONS[moduleKey].fields.map(field => field.name);

    tbody.innerHTML = rows.map(row => {
        const cells = headers.map(field => `<td>${formatValue(row[field])}</td>`).join('');
        return `
            <tr>
                ${cells}
                <td>
                    <button type="button" class="action-btn edit-btn" data-id="${row.id}" data-module="${moduleKey}">Editar</button>
                    <button type="button" class="action-btn delete-btn" data-id="${row.id}" data-module="${moduleKey}">Borrar</button>
                </td>
            </tr>
        `;
    }).join('');

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', async () => {
            const { module, id } = button.dataset;
            const response = await apiFetch(module, { mode: 'list' });
            const row = response.data.find(item => String(item.id) === String(id));
            if (!row) return;

            const form = document.querySelector(`#${module}-section form`);
            form.querySelector('[name="id"]').value = row.id;
            MODULE_DEFINITIONS[module].fields.forEach(field => {
                const input = form.querySelector(`[name="${field.name}"]`);
                if (input) {
                    input.value = row[field.name] ?? '';
                }
            });
        });
    });

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', async () => {
            const ok = confirm('¿Eliminar este registro?');
            if (!ok) return;
            const { module, id } = button.dataset;
            const response = await apiFetch(module, { mode: 'delete', id });
            if (response.success) {
                await loadModule(module);
            } else {
                alert('No se pudo borrar el registro');
            }
        });
    });
}

async function loadModule(moduleKey) {
    const response = await apiFetch(moduleKey, { mode: 'list' });
    if (!response.success) {
        return;
    }
    renderTableRows(moduleKey, response.data || []);
}

function renderDashboardStats(stats) {
    const cards = [
        { label: 'Clientes', value: stats.clients },
        { label: 'Vehículos', value: stats.vehicles },
        { label: 'Presupuestos activos', value: stats.budgets },
        { label: 'Órdenes activas', value: stats.work_orders },
        { label: 'Facturas pendientes', value: stats.invoices_pending },
        { label: 'Repuestos bajos', value: stats.low_stock }
    ];

    document.getElementById('dashboardStats').innerHTML = cards.map(card => `
        <div class="stat-card">
            <div class="meta">${card.label}</div>
            <div class="value">${card.value}</div>
        </div>
    `).join('');
}

function renderRecentList(targetId, items, titleField = 'name') {
    const container = document.getElementById(targetId);
    if (!items || items.length === 0) {
        container.innerHTML = '<div class="notice">Sin registros recientes.</div>';
        return;
    }

    container.innerHTML = items.slice(0, 5).map(item => `
        <div class="notice">• ${item[titleField] || item.issue || item.number || 'Registro'}</div>
    `).join('');
}

async function loadDashboard() {
    const response = await apiFetch('dashboard');
    if (!response.success) {
        return;
    }

    const data = response.data;
    renderDashboardStats(data.stats);
    renderRecentList('recentClients', data.recent.clients, 'name');
    renderRecentList('recentOrders', data.recent.work_orders, 'issue');
}

function renderModules() {
    Object.keys(MODULE_DEFINITIONS).forEach(moduleKey => {
        renderModuleForm(moduleKey);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    document.getElementById('loginForm').addEventListener('submit', handleLogin);
    document.getElementById('logoutBtn').addEventListener('click', handleLogout);
    checkSession();
});

window.addEventListener('load', () => {
    const defaultSection = document.querySelector('#dashboard-section');
    if (defaultSection) {
        defaultSection.classList.remove('hidden');
    }
});

