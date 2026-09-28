:root {
    --bg: #f5f7fb;
    --panel: #ffffff;
    --primary: #0f6cbd;
    --primary-dark: #0a4d87;
    --secondary: #edf4ff;
    --muted: #8290aa;
    --danger: #d93c3c;
    --success: #2d9f6b;
    --border: #dfe6f1;
    --text: #1a2333;
    --shadow: 0 10px 24px rgba(15, 24, 40, 0.08);
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: var(--bg);
    color: var(--text);
}

.hidden {
    display: none !important;
}

.auth-screen {
    min-height: 100vh;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, #0b2d4f, #1f5c9b);
}

.auth-box {
    width: min(440px, 92vw);
    background: rgba(255,255,255,0.96);
    padding: 28px;
    border-radius: 18px;
    box-shadow: var(--shadow);
}

.auth-box h1 {
    margin: 0 0 8px;
    font-size: 2rem;
    color: var(--primary-dark);
}

.subtitle {
    margin: 0 0 22px;
    color: var(--muted);
}

.auth-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.auth-form label {
    display: flex;
    flex-direction: column;
    gap: 8px;
    font-weight: 600;
}

.auth-form input,
.module-form input,
.module-form select,
.module-form textarea {
    width: 100%;
    border: 1px solid var(--border);
    background: var(--panel);
    border-radius: 10px;
    padding: 12px 14px;
    font-size: 0.98rem;
}

.auth-form button,
.module-form button,
.nav-btn,
.logout-btn {
    cursor: pointer;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    transition: 0.2s ease;
}

.auth-form button,
.module-form .primary {
    background: var(--primary);
    color: white;
    padding: 12px 18px;
}

.auth-form button:hover,
.module-form .primary:hover,
.logout-btn:hover {
    opacity: 0.96;
    transform: translateY(-1px);
}

.demo-info {
    margin-top: 14px;
    color: var(--muted);
    font-size: 0.92rem;
}

.app-layout {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 250px;
    background: #12263d;
    color: white;
    padding: 20px 16px;
}

.brand h2 {
    margin: 0 0 18px;
    font-size: 1.7rem;
    color: #dfefff;
}

.nav {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.nav-btn {
    background: transparent;
    color: #dfefff;
    text-align: left;
    padding: 12px 14px;
    border-left: 3px solid transparent;
}

.nav-btn.active {
    background: rgba(255,255,255,0.07);
    border-left-color: #7bc0ff;
}

.content {
    flex: 1;
    padding: 20px;
}

.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 14px 18px;
    box-shadow: var(--shadow);
    margin-bottom: 18px;
}

.label {
    display: block;
    color: var(--muted);
    font-size: 0.75rem;
    margin-bottom: 6px;
}

.logout-btn {
    background: #dfefff;
    color: var(--primary-dark);
    padding: 11px 16px;
}

.panel {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: var(--shadow);
    padding: 18px;
}

.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}

.panel-header h3 {
    margin: 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 16px;
    margin-bottom: 18px;
}

.stat-card {
    background: var(--secondary);
    border: 1px solid #d9e6ff;
    border-radius: 14px;
    padding: 16px;
}

.stat-card .meta {
    color: var(--muted);
    font-size: 0.78rem;
    margin-bottom: 8px;
    text-transform: uppercase;
}

.stat-card .value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-dark);
}

.two-cols {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 18px;
}

.mini-panel {
    background: #f9fbff;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 16px;
}

.mini-panel h4 {
    margin-top: 0;
}

.module-box {
    display: grid;
    gap: 22px;
}

.module-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 14px;
    align-items: end;
    background: #f9fbff;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 16px;
}

.module-form label {
    display: flex;
    flex-direction: column;
    gap: 8px;
    font-size: 0.8rem;
    color: var(--muted);
    font-weight: 600;
}

.module-form textarea {
    min-height: 90px;
    resize: vertical;
}

.inline-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.module-form .secondary {
    background: #e7edf8;
    color: var(--text);
    padding: 12px 18px;
}

.table-wrap {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.data-table th,
.data-table td {
    border-bottom: 1px solid var(--border);
    padding: 11px 10px;
    text-align: left;
    vertical-align: top;
}

.data-table th {
    background: #f0f5fc;
    color: var(--primary-dark);
    font-size: 0.82rem;
    text-transform: uppercase;
}

.action-btn {
    border: none;
    border-radius: 8px;
    padding: 7px 10px;
    font-weight: 600;
    cursor: pointer;
    margin-right: 6px;
}

.edit-btn {
    background: #e7f1ff;
    color: var(--primary-dark);
}

.delete-btn {
    background: #fde8e8;
    color: var(--danger);
}

.notice {
    color: var(--muted);
    font-size: 0.9rem;
}

@media (max-width: 900px) {
    .app-layout {
        display: block;
    }
    .sidebar {
        width: 100%;
    }
    .nav {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    }
}
