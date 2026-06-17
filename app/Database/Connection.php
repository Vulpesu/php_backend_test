<?php
namespace App\Database;

use PDO;
use PDOException;
use App\Database\DatabaseConfig;

class Connection
{
    private static ?PDO $pdo = null;

    public static function get(DatabaseConfig $config): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = new PDO(
                $config->getDsn(), 
                $config->username, 
                $config->password, 
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        }
        return self::$pdo;
    }
}