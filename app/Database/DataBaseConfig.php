<?php
namespace App\Database;

class DatabaseConfig
{
    public function __construct(
        public readonly string $host,
        public readonly string $database,
        public readonly string $username,
        public readonly string $password,
        public readonly int $port = 3306
    ) {}

    // Метод для автоматической сборки DSN строки для PDO
    public function getDsn(): string
    {
        return "mysql:host={$this->host};port={$this->port};dbname={$this->database};charset=utf8mb4";
    }
}