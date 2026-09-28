<?php
require_once __DIR__ . '/config.php';

class WorkshopDB {
    private SQLite3 $db;

    public function __construct() {
        $this->db = new SQLite3(DB_FILE, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
        $this->db->busyTimeout(5000);
        $this->db->exec('PRAGMA foreign_keys = ON;');
        $this->db->exec('PRAGMA journal_mode = WAL;');
    }

    public function init(): void {
        $queries = [
            // Tabla de usuarios con roles mejorados
            "CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                name TEXT NOT NULL,
                email TEXT,
                role TEXT DEFAULT 'mecanico',
                status TEXT DEFAULT 'activo',
                permissions TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                last_login TEXT
            );",

            // Clientes
            "CREATE TABLE IF NOT EXISTS clients (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                dni TEXT UNIQUE,
                phone TEXT,
                email TEXT,
                address TEXT,
                city TEXT,
                postal_code TEXT,
                notes TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            );",

            // Vehículos
            "CREATE TABLE IF NOT EXISTS vehicles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                client_id INTEGER NOT NULL,
                brand TEXT NOT NULL,
                model TEXT NOT NULL,
                plate TEXT UNIQUE NOT NULL,
                vin TEXT UNIQUE,
                year INTEGER,
                km INTEGER,
                color TEXT,
                fuel_type TEXT,
                notes TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE CASCADE
            );",

            // Presupuestos
            "CREATE TABLE IF NOT EXISTS budgets (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                client_id INTEGER NOT NULL,
                vehicle_id INTEGER NOT NULL,
                number TEXT NOT NULL UNIQUE,
                date TEXT NOT NULL,
                labor_amount REAL DEFAULT 0,
                parts_amount REAL DEFAULT 0,
                tax_amount REAL DEFAULT 0,
                total_amount REAL DEFAULT 0,
                status TEXT DEFAULT 'pendiente',
                valid_until TEXT,
                notes TEXT,
                created_by INTEGER,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE CASCADE,
                FOREIGN KEY(vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
                FOREIGN KEY(created_by) REFERENCES users(id)
            );",

            // Órdenes de trabajo
            "CREATE TABLE IF NOT EXISTS work_orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                client_id INTEGER NOT NULL,
                vehicle_id INTEGER NOT NULL,
                budget_id INTEGER,
                number TEXT NOT NULL UNIQUE,
                issue TEXT NOT NULL,
                diagnosis TEXT,
                date TEXT NOT NULL,
                status TEXT DEFAULT 'abierta',
                priority TEXT DEFAULT 'normal',
                assigned_to INTEGER,
                labor_hours REAL DEFAULT 0,
                labor_amount REAL DEFAULT 0,
                parts_amount REAL DEFAULT 0,
                tax_amount REAL DEFAULT 0,
                total_amount REAL DEFAULT 0,
                notes TEXT,
                created_by INTEGER,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
                completed_at TEXT,
                FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE CASCADE,
                FOREIGN KEY(vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
                FOREIGN KEY(budget_id) REFERENCES budgets(id) ON DELETE SET NULL,
                FOREIGN KEY(assigned_to) REFERENCES users(id),
                FOREIGN KEY(created_by) REFERENCES users(id)
            );",

            // Facturas
            "CREATE TABLE IF NOT EXISTS invoices (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                client_id INTEGER NOT NULL,
                work_order_id INTEGER,
                number TEXT NOT NULL UNIQUE,
                date TEXT NOT NULL,
                due_date TEXT,
                labor_amount REAL DEFAULT 0,
                parts_amount REAL DEFAULT 0,
                tax_rate REAL DEFAULT 21,
                tax_amount REAL DEFAULT 0,
                total_amount REAL DEFAULT 0,
                status TEXT DEFAULT 'pendiente',
                payment_method TEXT,
                payment_date TEXT,
                notes TEXT,
                created_by INTEGER,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE CASCADE,
                FOREIGN KEY(work_order_id) REFERENCES work_orders(id) ON DELETE SET NULL,
                FOREIGN KEY(created_by) REFERENCES users(id)
            );",

            // Agenda
            "CREATE TABLE IF NOT EXISTS agenda (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                date TEXT NOT NULL,
                time TEXT,
                title TEXT NOT NULL,
                type TEXT DEFAULT 'cita',
                client_id INTEGER,
                vehicle_id INTEGER,
                assigned_to INTEGER,
                notes TEXT,
                status TEXT DEFAULT 'programado',
                reminder_sent INTEGER DEFAULT 0,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE SET NULL,
                FOREIGN KEY(vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,
                FOREIGN KEY(assigned_to) REFERENCES users(id)
            );",

            // Repuestos
            "CREATE TABLE IF NOT EXISTS spare_parts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code TEXT NOT NULL UNIQUE,
                name TEXT NOT NULL,
                category TEXT,
                supplier TEXT,
                unit_price REAL DEFAULT 0,
                stock_min INTEGER DEFAULT 5,
                stock_max INTEGER DEFAULT 50,
                unit TEXT DEFAULT 'und',
                description TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            );",

            // Compras de repuestos
            "CREATE TABLE IF NOT EXISTS purchases (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                part_id INTEGER NOT NULL,
                supplier TEXT,
                quantity REAL DEFAULT 0,
                unit_price REAL DEFAULT 0,
                total_price REAL DEFAULT 0,
                date TEXT NOT NULL,
                invoice_number TEXT,
                status TEXT DEFAULT 'recibido',
                notes TEXT,
                created_by INTEGER,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(part_id) REFERENCES spare_parts(id) ON DELETE CASCADE,
                FOREIGN KEY(created_by) REFERENCES users(id)
            );",

            // Stock de almacén
            "CREATE TABLE IF NOT EXISTS stock (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                part_id INTEGER NOT NULL UNIQUE,
                quantity REAL DEFAULT 0,
                warehouse TEXT DEFAULT 'almacen',
                last_update TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(part_id) REFERENCES spare_parts(id) ON DELETE CASCADE
            );",

            // Movimientos de stock
            "CREATE TABLE IF NOT EXISTS stock_movements (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                part_id INTEGER NOT NULL,
                type TEXT NOT NULL,
                quantity REAL DEFAULT 0,
                reference_id INTEGER,
                reference_type TEXT,
                notes TEXT,
                created_by INTEGER,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(part_id) REFERENCES spare_parts(id) ON DELETE CASCADE,
                FOREIGN KEY(created_by) REFERENCES users(id)
            );",

            // Reparaciones (seguimiento detallado)
            "CREATE TABLE IF NOT EXISTS repairs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                client_id INTEGER NOT NULL,
                vehicle_id INTEGER NOT NULL,
                issue TEXT NOT NULL,
                diagnosis TEXT,
                status TEXT DEFAULT 'en_revision',
                priority TEXT DEFAULT 'normal',
                assigned_to INTEGER,
                started_at TEXT,
                completed_at TEXT,
                estimated_hours REAL,
                actual_hours REAL,
                labor_cost REAL DEFAULT 0,
                parts_cost REAL DEFAULT 0,
                notes TEXT,
                created_by INTEGER,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE CASCADE,
                FOREIGN KEY(vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
                FOREIGN KEY(assigned_to) REFERENCES users(id),
                FOREIGN KEY(created_by) REFERENCES users(id)
            );",

            // Historial de auditoría
            "CREATE TABLE IF NOT EXISTS audit_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                action TEXT NOT NULL,
                table_name TEXT,
                record_id INTEGER,
                old_values TEXT,
                new_values TEXT,
                ip_address TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id)
            );",

            // Configuración del sistema
            "CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                key TEXT NOT NULL UNIQUE,
                value TEXT,
                type TEXT DEFAULT 'text',
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            );"
        ];

        foreach ($queries as $sql) {
            $this->db->exec($sql);
        }

        $this->ensureAdminUser();
        $this->ensureDefaultSettings();
    }

    private function ensureAdminUser(): void {
        $exists = $this->db->querySingle("SELECT COUNT(*) FROM users WHERE username = 'admin'");
        if ((int) $exists === 0) {
            $hash = password_hash('admin123', PASSWORD_DEFAULT);
            $permissions = json_encode(['all' => true]);
            $sql = "INSERT INTO users (username, password_hash, name, email, role, permissions) VALUES ('admin', :hash, 'Administrador', 'admin@taller.local', 'admin', :perms)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':hash', $hash, SQLITE3_TEXT);
            $stmt->bindValue(':perms', $permissions, SQLITE3_TEXT);
            $stmt->execute();
            logEvent('SYSTEM', 'Usuario admin creado');
        }
    }

    private function ensureDefaultSettings(): void {
        $defaults = [
            'company_name' => 'Mi Taller',
            'company_address' => 'Dirección del taller',
            'company_phone' => '000000000',
            'company_email' => 'taller@example.com',
            'company_tax_id' => 'CIF/NIF',
            'currency' => 'EUR',
            'tax_rate' => '21',
            'labor_hourly_rate' => '50',
            'invoice_prefix' => 'FAC-',
            'budget_prefix' => 'PRE-',
            'workorder_prefix' => 'OT-',
            'backup_frequency' => 'daily'
        ];

        foreach ($defaults as $key => $value) {
            $exists = $this->db->querySingle("SELECT COUNT(*) FROM settings WHERE key = :key", [':key' => $key]);
            if ((int) $exists === 0) {
                $sql = "INSERT INTO settings (key, value, type) VALUES (:key, :value, 'text')";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':key', $key, SQLITE3_TEXT);
                $stmt->bindValue(':value', $value, SQLITE3_TEXT);
                $stmt->execute();
            }
        }
    }

    public function getUserByUsername(string $username): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row ?: null;
    }

    public function getUserById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row ?: null;
    }

    public function updateLastLogin(int $userId): void {
        $stmt = $this->db->prepare("UPDATE users SET last_login = datetime('now') WHERE id = :id");
        $stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
        $stmt->execute();
    }

    public function dashboardData(int $userId): array {
        $stats = [
            'clients' => (int) $this->db->querySingle('SELECT COUNT(*) FROM clients'),
            'vehicles' => (int) $this->db->querySingle('SELECT COUNT(*) FROM vehicles'),
            'budgets_pending' => (int) $this->db->querySingle("SELECT COUNT(*) FROM budgets WHERE status = 'pendiente'"),
            'work_orders_active' => (int) $this->db->querySingle("SELECT COUNT(*) FROM work_orders WHERE status IN ('abierta', 'en_proceso')"),
            'invoices_pending' => (int) $this->db->querySingle("SELECT COUNT(*) FROM invoices WHERE status IN ('pendiente', 'vencida')"),
            'low_stock' => (int) $this->db->querySingle("SELECT COUNT(*) FROM stock s JOIN spare_parts p ON p.id = s.part_id WHERE s.quantity <= p.stock_min"),
            'repairs_ongoing' => (int) $this->db->querySingle("SELECT COUNT(*) FROM repairs WHERE status IN ('en_revision', 'en_reparacion')"),
        ];

        $revenue_month = $this->db->querySingle(
            "SELECT COALESCE(SUM(total_amount), 0) FROM invoices WHERE strftime('%Y-%m', date) = strftime('%Y-%m', 'now') AND status = 'pagada'"
        );
        $stats['revenue_month'] = (float) $revenue_month;

        $recent = [
            'clients' => $this->getRecent('clients', 5),
            'work_orders' => $this->getRecent('work_orders', 5),
            'invoices' => $this->getRecent('invoices', 5),
        ];

        return ['stats' => $stats, 'recent' => $recent];
    }

    private function getRecent(string $table, int $limit): array {
        $sql = "SELECT * FROM $table ORDER BY id DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $rows = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function listModule(string $module, array $filters = []): array {
        $limit = isset($filters['limit']) ? (int) $filters['limit'] : 0;
        $offset = isset($filters['offset']) ? (int) $filters['offset'] : 0;
        $sql = '';

        switch ($module) {
            case 'clients':
                $sql = 'SELECT * FROM clients ORDER BY name ASC';
                break;
            case 'vehicles':
                $sql = 'SELECT v.*, c.name AS client_name FROM vehicles v LEFT JOIN clients c ON c.id = v.client_id ORDER BY v.plate ASC';
                break;
            case 'budgets':
                $sql = 'SELECT b.*, c.name AS client_name, v.plate AS vehicle_plate FROM budgets b LEFT JOIN clients c ON c.id = b.client_id LEFT JOIN vehicles v ON v.id = b.vehicle_id ORDER BY b.date DESC';
                break;
            case 'work_orders':
                $sql = 'SELECT wo.*, c.name AS client_name, v.plate AS vehicle_plate, u.name AS assigned_name FROM work_orders wo LEFT JOIN clients c ON c.id = wo.client_id LEFT JOIN vehicles v ON v.id = wo.vehicle_id LEFT JOIN users u ON u.id = wo.assigned_to ORDER BY wo.date DESC';
                break;
            case 'invoices':
                $sql = 'SELECT i.*, c.name AS client_name FROM invoices i LEFT JOIN clients c ON c.id = i.client_id ORDER BY i.date DESC';
                break;
            case 'agenda':
                $sql = 'SELECT a.*, c.name AS client_name, v.plate AS vehicle_plate, u.name AS assigned_name FROM agenda a LEFT JOIN clients c ON c.id = a.client_id LEFT JOIN vehicles v ON v.id = a.vehicle_id LEFT JOIN users u ON u.id = a.assigned_to ORDER BY a.date DESC, a.time DESC';
                break;
            case 'spare_parts':
                $sql = 'SELECT p.*, COALESCE(s.quantity, 0) AS stock_quantity FROM spare_parts p LEFT JOIN stock s ON s.part_id = p.id ORDER BY p.name ASC';
                break;
            case 'purchases':
                $sql = 'SELECT pu.*, p.name AS part_name, p.code AS part_code FROM purchases pu LEFT JOIN spare_parts p ON p.id = pu.part_id ORDER BY pu.date DESC';
                break;
            case 'stock':
                $sql = 'SELECT s.*, p.name AS part_name, p.code AS part_code, p.stock_min FROM stock s LEFT JOIN spare_parts p ON p.id = s.part_id ORDER BY s.quantity ASC';
                break;
            case 'repairs':
                $sql = 'SELECT r.*, c.name AS client_name, v.plate AS vehicle_plate, u.name AS assigned_name FROM repairs r LEFT JOIN clients c ON c.id = r.client_id LEFT JOIN vehicles v ON v.id = r.vehicle_id LEFT JOIN users u ON u.id = r.assigned_to ORDER BY r.created_at DESC';
                break;
            case 'users':
                $sql = 'SELECT id, username, name, email, role, status, created_at, last_login FROM users ORDER BY name ASC';
                break;
            default:
                return [];
        }

        if ($limit > 0) {
            $sql = $sql . ' LIMIT :limit OFFSET :offset';
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
            $stmt->bindValue(':offset', $offset, SQLITE3_INTEGER);
            $result = $stmt->execute();
        } else {
            $result = $this->db->query($sql);
        }

        $rows = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function saveModule(string $module, array $record): array {
        $id = isset($record['id']) && (int) $record['id'] > 0 ? (int) $record['id'] : null;
        unset($record['id']);

        if ($id === null) {
            return $this->insertRecord($module, $record);
        } else {
            return $this->updateRecord($module, $id, $record);
        }
    }

    private function insertRecord(string $module, array $record): array {
        $fields = array_keys($record);
        $columns = implode(', ', $fields);
        $placeholders = implode(', ', array_map(fn($field) => ':' . $field, $fields));

        $sql = "INSERT INTO {$this->tableName($module)} ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        foreach ($record as $key => $value) {
            $stmt->bindValue(':' . $key, $this->normalizeValue($value), SQLITE3_TEXT);
        }
        $stmt->execute();
        $insertId = $this->db->lastInsertRowID();

        logEvent('INSERT', "$module ID: $insertId");
        return ['id' => $insertId];
    }

    private function updateRecord(string $module, int $id, array $record): array {
        $fields = array_keys($record);
        $updates = implode(', ', array_map(fn($field) => $field . ' = :' . $field, $fields));
        $sql = "UPDATE {$this->tableName($module)} SET $updates WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        foreach ($record as $key => $value) {
            $stmt->bindValue(':' . $key, $this->normalizeValue($value), SQLITE3_TEXT);
        }
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();

        logEvent('UPDATE', "$module ID: $id");
        return ['id' => $id];
    }

    public function deleteModule(string $module, int $id): bool {
        if ($id <= 0) return false;
        $sql = "DELETE FROM {$this->tableName($module)} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
        logEvent('DELETE', "$module ID: $id");
        return true;
    }

    private function tableName(string $module): string {
        $map = [
            'clients' => 'clients',
            'vehicles' => 'vehicles',
            'budgets' => 'budgets',
            'work_orders' => 'work_orders',
            'invoices' => 'invoices',
            'agenda' => 'agenda',
            'spare_parts' => 'spare_parts',
            'purchases' => 'purchases',
            'stock' => 'stock',
            'repairs' => 'repairs',
            'users' => 'users',
        ];
        return $map[$module] ?? 'clients';
    }

    private function normalizeValue($value) {
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        if ($value === '' || $value === null) {
            return null;
        }
        if (is_bool($value)) {
            return (int) $value;
        }
        return (string) $value;
    }

    public function createBackup(): string {
        $timestamp = date('Y-m-d_H-i-s');
        $backup_file = BACKUP_DIR . '/backup_' . $timestamp . '.db';
        copy(DB_FILE, $backup_file);
        logEvent('BACKUP', 'Backup creado: ' . $backup_file);
        return $backup_file;
    }

    public function getSetting(string $key, string $default = ''): string {
        $value = $this->db->querySingle("SELECT value FROM settings WHERE key = :key", [':key' => $key]);
        return $value !== null ? (string) $value : $default;
    }

    public function setSetting(string $key, string $value): void {
        $sql = "INSERT INTO settings (key, value) VALUES (:key, :value) ON CONFLICT(key) DO UPDATE SET value = excluded.value, updated_at = datetime('now')";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':key', $key, SQLITE3_TEXT);
        $stmt->bindValue(':value', $value, SQLITE3_TEXT);
        $stmt->execute();
    }
}
