<?php
// migrate_classification.php

require_once 'app/Config/Database.php';

use App\Config\Database;

$db = new Database();
$pdo = $db->getConnection();

echo "Starting Classification migration...\n";

// 1. Create classifications table
$sql_table = "CREATE TABLE IF NOT EXISTS classifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
)";
$pdo->exec($sql_table);
echo "Checked/Created classifications table.\n";

// 2. Seed classifications
$classifications = [
    'Persons with Disability (PWD)',
    'Solo Parent',
    'Senior Citizen',
    'Indigenous Peoples (IPs)',
    'SOGIE-Diverse / Member of LGBTQ+',
    'Not Applicable'
];

foreach ($classifications as $item) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO classifications (name) VALUES (:name)");
    $stmt->execute(['name' => $item]);
}
echo "Seeded classifications.\n";

// 3. Add column to ld_activities table
// Check if column exists first to avoid error
$stmt = $pdo->prepare("SHOW COLUMNS FROM ld_activities LIKE 'classification'");
$stmt->execute();
if (!$stmt->fetch()) {
    $sql_alter = "ALTER TABLE ld_activities ADD COLUMN classification VARCHAR(255) DEFAULT '' AFTER competency";
    $pdo->exec($sql_alter);
    echo "Added 'classification' column to ld_activities.\n";
} else {
    echo "Column 'classification' already exists in ld_activities.\n";
}

echo "Migration completed successfully.\n";
