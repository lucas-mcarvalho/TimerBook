<?php

class DatabaseTestHelper {
    private static $pdo = null;

    public static function setPdo(PDO $pdo) {
        self::$pdo = $pdo;
    }

    public static function hasPdo(): bool {
        return self::$pdo !== null;
    }

    public static function getPdo(): ?PDO {
        return self::$pdo;
    }
}
