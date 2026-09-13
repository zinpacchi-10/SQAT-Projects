<?php
/**
 * Database
 * Thin wrapper around mysqli that forces the use of prepared statements
 * for every query executed through it, as required by the project spec.
 */
class Database
{
    private static ?mysqli $connection = null;

    public static function getConnection(): mysqli
    {
        if (self::$connection === null) {
            $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($conn->connect_error) {
                die('Database connection failed: ' . $conn->connect_error .
                    '<br>Make sure you created the database using database/schema.sql in phpMyAdmin.');
            }
            $conn->set_charset('utf8mb4');
            self::$connection = $conn;
        }
        return self::$connection;
    }

    /**
     * Run a prepared statement.
     * $types: mysqli bind_param type string e.g. "isi"
     * $params: array of values matching $types
     * Returns the mysqli_stmt object (already executed).
     */
    public static function run(string $sql, string $types = '', array $params = []): mysqli_stmt
    {
        $conn = self::getConnection();
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('SQL prepare error: ' . $conn->error . ' | Query: ' . $sql);
        }
        if ($types !== '' && count($params) > 0) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt;
    }

    /** Fetch all rows as associative array for a prepared SELECT */
    public static function fetchAll(string $sql, string $types = '', array $params = []): array
    {
        $stmt = self::run($sql, $types, $params);
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }

    /** Fetch a single row (or null) for a prepared SELECT */
    public static function fetchOne(string $sql, string $types = '', array $params = []): ?array
    {
        $stmt = self::run($sql, $types, $params);
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $row ?: null;
    }

    /** Execute an INSERT/UPDATE/DELETE and return affected rows */
    public static function execute(string $sql, string $types = '', array $params = []): int
    {
        $stmt = self::run($sql, $types, $params);
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected;
    }

    /** Execute an INSERT and return the new insert id */
    public static function insert(string $sql, string $types = '', array $params = []): int
    {
        $stmt = self::run($sql, $types, $params);
        $id = self::getConnection()->insert_id;
        $stmt->close();
        return $id;
    }
}
