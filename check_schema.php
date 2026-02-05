<?php
require_once 'app/Config/Database.php';
use App\Config\Database;
$db = new Database();
$pdo = $db->getConnection();
$stmt = $pdo->query("DESCRIBE ld_activities");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    echo $col['Field'] . " | " . $col['Type'] . " | " . $col['Null'] . " | " . $col['Default'] . "\n";
}
