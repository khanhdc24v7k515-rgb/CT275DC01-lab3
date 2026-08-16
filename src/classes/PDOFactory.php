<?php
namespace CT275\Labs;

use PDO;

class PDOFactory {
    public function create(array $config) {
        $dsn = "pgsql:host={$config['dbhost']};dbname={$config['dbname']}";
        return new PDO($dsn, $config['dbuser'], $config['dbpass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }
}