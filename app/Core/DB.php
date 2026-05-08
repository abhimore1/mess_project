<?php
/**
 * PDO Database Singleton with tenant-aware query logging.
 */
class DB
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                env('DB_HOST', '127.0.0.1'),
                env('DB_PORT', '3306'),
                env('DB_NAME', 'mess_saas')
            );

            self::$instance = new PDO($dsn, env('DB_USER', 'root'), env('DB_PASS', ''), [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ]);
        }

        return self::$instance;
    }

    /**
     * Execute a prepared statement and return the statement object.
     */
    public static function execute(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function query(string $sql, array $params = []): array
    {
        return self::execute($sql, $params)->fetchAll();
    }

    public static function queryOne(string $sql, array $params = []): ?array
    {
        $row = self::execute($sql, $params)->fetch();
        return $row ?: null;
    }

    public static function insert(string $table, array $data): int
    {
        $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($data)));
        $vals = implode(', ', array_fill(0, count($data), '?'));
        self::execute("INSERT INTO `$table` ($cols) VALUES ($vals)", array_values($data));
        return (int) self::getInstance()->lastInsertId();
    }

    public static function update(string $table, array $data, array $where): int
    {
        $set   = implode(', ', array_map(fn($k) => "`$k` = ?", array_keys($data)));
        $cond  = implode(' AND ', array_map(fn($k) => "`$k` = ?", array_keys($where)));
        $stmt  = self::execute(
            "UPDATE `$table` SET $set WHERE $cond",
            [...array_values($data), ...array_values($where)]
        );
        return $stmt->rowCount();
    }

    public static function delete(string $table, array $where): int
    {
        $cond = implode(' AND ', array_map(fn($k) => "`$k` = ?", array_keys($where)));
        return self::execute("DELETE FROM `$table` WHERE $cond", array_values($where))->rowCount();
    }

    public static function lastId(): int
    {
        return (int) self::getInstance()->lastInsertId();
    }

    public static function beginTransaction(): void { self::getInstance()->beginTransaction(); }
    public static function commit(): void           { self::getInstance()->commit(); }
    public static function rollBack(): void         { self::getInstance()->rollBack(); }
}
