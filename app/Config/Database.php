<?php
namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $dbname = 'ldpver2';
    public $pdo;

    public function getConnection()
    {
        $this->pdo = null;

        try {
            $this->pdo = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Connect to specific database
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS `" . $this->dbname . "`");
            $this->pdo->exec("USE `" . $this->dbname . "`");

            // Set timezone
            date_default_timezone_set('Asia/Manila');

        } catch (PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }

        return $this->pdo;
    }
}
