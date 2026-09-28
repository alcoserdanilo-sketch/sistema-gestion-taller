<?php
class WorkshopDB {
    private SQLite3 $db;

    public function __construct() {
        $this->db = new SQLite3(__DIR__ . '/workshop.db', SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
        $this->db->exec('PRAGMA foreign_keys = ON;');
    }

    public function init(): void {
        $queries = [
            "CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                name TEXT NOT NULL,
                role TEXT DEFAULT 'admin'
            );",
            "CREATE TABLE IF NOT EXISTS clients (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                dni TEXT,
                phone TEXT,
                email TEXT,
                address TEXT,
                notes TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            );",
            "CREATE TABLE IF NOT EXISTS vehicles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                client_id INTEGER NOT NULL,
                brand TEXT NOT NULL,
                model TEXT NOT NULL,
                plate TEXT NOT NULL,
                vin TEXT,
                year INTEGER,
                km INTEGER,
                notes TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE CASCADE
            );",
            "CREATE TABLE IF NOT EXISTS budgets (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                client_id INTEGER NOT NULL,
                vehicle_id INTEGER NOT NULL,
                number TEXT NOT NULL,
                date TEXT NOT NULL,
                amount REAL DEFAULT 0,
                status TEXT DEFAULT 'pendiente',
                notes TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE CASCADE,
                FOREIGN KEY(vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
            );",
            "CREATE TABLE IF NOT EXISTS work_orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                client_id INTEGER NOT NULL,
                vehicle_id INTEGER NOT NULL,
                number TEXT NOT NULL,
                issue TEXT NOT NULL,
                date TEXT NOT NULL,
                status TEXT DEFAULT 'abierta',
                labor_hours REAL DEFAULT 0,
                amount REAL DEFAULT 0,
                notes TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE CASCADE,
                FOREIGN KEY(vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
            );",
            "CREATE TABLE IF NOT EXISTS invoices (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                client_id INTEGER NOT NULL,
                work_order_id INTEGER,
                number TEXT NOT NULL,
                date TEXT NOT NULL,
                amount REAL DEFAULT 0,
                status TEXT DEFAULT 'pendiente',
                notes TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE CASCADE,
                FOREIGN KEY(work_order_id) REFERENCES work_orders(id) ON DELETE SET NULL
            );",
            "CREATE TABLE IF NOT EXISTS agenda (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                date TEXT NOT NULL,
                time TEXT,
                title TEXT NOT NULL,
                client_id INTEGER,
                vehicle_id INTEGER,
                notes TEXT,
                status TEXT DEFAULT 'programado',
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE SET NULL,
                FOREIGN KEY(vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL
            );",
            "CREATE TABLE IF NOT EXISTS spare_parts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code TEXT NOT NULL UNIQUE,
                name TEXT NOT NULL,
                category TEXT,
                unit_price REAL DEFAULT 0,
                stock_min INTEGER DEFAULT 0,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            );",
            "CREATE TABLE IF NOT EXISTS purchases (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                part_id INTEGER NOT NULL,
                supplier TEXT,
                quantity REAL DEFAULT 0,
                unit_price REAL DEFAULT 0,
                date TEXT NOT NULL,
                invoice_number TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(part_id) REFERENCES spare_parts(id) ON DELETE CASCADE
            );",
            "CREATE TABLE IF NOT EXISTS stock (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                part_id INTEGER NOT NULL UNIQUE,
                quantity REAL DEFAULT 0,
                warehouse TEXT DEFAULT 'almacen',
                last_update TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(part_id) REFERENCES spare_parts(id) ON DELETE CASCADE
            );",
            "CREATE TABLE IF NOT EXISTS repairs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                client_id INTEGER NOT NULL,
                vehicle_id INTEGER NOT NULL,
                issue TEXT NOT NULL,
                diagnosis TEXT,
                status TEXT DEFAULT 'en_revision',
                started_at TEXT,
                completed_at TEXT,
                notes TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE CASCADE,
                FOREIGN KEY(vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
            );"
        ];

        foreach ($queries as $sql) {
            $this->db->exec($sql);
        }

        $this->ensureAdminUser();
    }

    private function ensureAdminUser(): void {
        $exists = $this->db->querySingle("SELECT COUNT(*) FROM users WHERE username = 'admin'");
        if ((int) $exists === 0) {
            $hash = password_hash('admin123', PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password_hash, name, role) VALUES ('admin', :hash, 'Administrador', 'admin')";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':hash', $hash, SQLITE3_TEXT);
            $stmt->execute();
        }
    }

    public function getUserByUsername(string $username): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row ?: null;
    }

    public function dashboardData(): array {
        $stats = [
            'clients' => (int) $this->db->querySingle('SELECT COUNT(*) FROM clients'),
            'vehicles' => (int) $this->db->querySingle('SELECT COUNT(*) FROM vehicles'),
            'budgets' => (int) $this->db->querySingle("SELECT COUNT(*) FROM budgets WHERE status != 'cerrado'"),
            'work_orders' => (int) $this->db->querySingle("SELECT COUNT(*) FROM work_orders WHERE status != 'finalizado'"),
            'invoices_pending' => (int) $this->db->querySingle("SELECT COUNT(*) FROM invoices WHERE status != 'pagada'"),
            'low_stock' => (int) $this->db->querySingle("SELECT COUNT(*) FROM stock s JOIN spare_parts p ON p.id = s.part_id WHERE s.quantity <= p.stock_min"),
        ];

        $recent = [
            'clients' => $this->listModule('clients', ['limit' => 5]),
            'work_orders' => $this->listModule('work_orders', ['limit' => 5]),
            'repairs' => $this->listModule('repairs', ['limit' => 5]),
        ];

        return ['stats' => $stats, 'recent' => $recent];
    }

    public function listModule(string $module, array $filters = []): array {
        $limit = isset($filters['limit']) ? (int) $filters['limit'] : 0;
        $sql = '';

        switch ($module) {
            case 'clients':
                $sql = 'SELECT * FROM clients ORDER BY id DESC';
                break;
            case 'vehicles':
                $sql = 'SELECT v.*, c.name AS client_name FROM vehicles v LEFT JOIN clients c ON c.id = v.client_id ORDER BY v.id DESC';
                break;
            case 'budgets':
                $sql = 'SELECT b.*, c.name AS client_name, v.plate AS vehicle_plate FROM budgets b LEFT JOIN clients c ON c.id = b.client_id LEFT JOIN vehicles v ON v.id = b.vehicle_id ORDER BY b.id DESC';
                break;
            case 'work_orders':
                $sql = 'SELECT wo.*, c.name AS client_name, v.plate AS vehicle_plate FROM work_orders wo LEFT JOIN clients c ON c.id = wo.client_id LEFT JOIN vehicles v ON v.id = wo.vehicle_id ORDER BY wo.id DESC';
                break;
            case 'invoices':
                $sql = 'SELECT i.*, c.name AS client_name FROM invoices i LEFT JOIN clients c ON c.id = i.client_id ORDER BY i.id DESC';
                break;
            case 'agenda':
                $sql = 'SELECT a.*, c.name AS client_name, v.plate AS vehicle_plate FROM agenda a LEFT JOIN clients c ON c.id = a.client_id LEFT JOIN vehicles v ON v.id = a.vehicle_id ORDER BY a.date DESC, a.time DESC';
                break;
            case 'spare_parts':
                $sql = 'SELECT p.*, COALESCE(s.quantity, 0) AS stock_quantity FROM spare_parts p LEFT JOIN stock s ON s.part_id = p.id ORDER BY p.id DESC';
                break;
            case 'purchases':
                $sql = 'SELECT pu.*, p.name AS part_name FROM purchases pu LEFT JOIN spare_parts p ON p.id = pu.part_id ORDER BY pu.id DESC';
                break;
            case 'stock':
                $sql = 'SELECT s.*, p.name AS part_name, p.code AS part_code, p.stock_min FROM stock s LEFT JOIN spare_parts p ON p.id = s.part_id ORDER BY s.quantity ASC';
                break;
            case 'repairs':
                $sql = 'SELECT r.*, c.name AS client_name, v.plate AS vehicle_plate FROM repairs r LEFT JOIN clients c ON c.id = r.client_id LEFT JOIN vehicles v ON v.id = r.vehicle_id ORDER BY r.id DESC';
                break;
            default:
                return [];
        }

        if ($limit > 0) {
            $sql = $sql . ' LIMIT ' . $limit;
        }

        $result = $this->db->query($sql);
        $rows = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function saveModule(string $module, array $record): array {
        $id = isset($record['id']) && (int) $record['id'] > 0 ? (int) $record['id'] : null;
        unset($record['id']);

        $fields = array_keys($record);
        $columns = implode(', ', $fields);
        $placeholders = implode(', ', array_map(fn($field) => ':' . $field, $fields));

        if ($id === null) {
            $sql = "INSERT INTO {$this->tableName($module)} ($columns) VALUES ($placeholders)";
            $stmt = $this->db->prepare($sql);
            foreach ($record as $key => $value) {
                $stmt->bindValue(':' . $key, $this->normalizeValue($value), SQLITE3_TEXT);
            }
            $stmt->execute();
            $insertId = $this->db->lastInsertRowID();

            if ($module === 'purchases') {
                $this->recalculateStock((int) $record['part_id'], (float) ($record['quantity'] ?? 0));
            }

            return ['id' => $insertId];
        }

        $updates = implode(', ', array_map(fn($field) => $field . ' = :' . $field, $fields));
        $sql = "UPDATE {$this->tableName($module)} SET $updates WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        foreach ($record as $key => $value) {
            $stmt->bindValue(':' . $key, $this->normalizeValue($value), SQLITE3_TEXT);
        }
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();

        if ($module === 'purchases') {
            $this->recalculateStock((int) $record['part_id'], (float) ($record['quantity'] ?? 0));
        }

        return ['id' => $id];
    }

    public function deleteModule(string $module, int $id): bool {
        if ($id <= 0) {
            return false;
        }

        $sql = "DELETE FROM {$this->tableName($module)} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
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

        if (is_numeric($value)) {
            return $value;
        }

        return (string) $value;
    }

    private function recalculateStock(int $partId, float $quantity): void {
        $existing = $this->db->querySingle("SELECT quantity FROM stock WHERE part_id = :partId", [':partId' => $partId]);
        $current = $existing !== false ? (float) $existing : 0.0;
        $updated = $current + $quantity;

        $stmt = $this->db->prepare("INSERT INTO stock (part_id, quantity, warehouse, last_update) VALUES (:partId, :quantity, 'almacen', datetime('now')) ON CONFLICT(part_id) DO UPDATE SET quantity = excluded.quantity, last_update = datetime('now')");
        $stmt->bindValue(':partId', $partId, SQLITE3_INTEGER);
        $stmt->bindValue(':quantity', $updated, SQLITE3_FLOAT);
        $stmt->execute();
    }
}
