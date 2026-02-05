<?php
// migrate_ld_tables.php

require_once 'app/Config/Database.php';

use App\Config\Database;

$db = new Database();
$pdo = $db->getConnection();

echo "Starting migration...\n";

// 1. Create ld_types table
$sql_ld_types = "CREATE TABLE IF NOT EXISTS ld_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
)";
$pdo->exec($sql_ld_types);
echo "Checked/Created ld_types table.\n";

// 2. Create modalities table
$sql_modalities = "CREATE TABLE IF NOT EXISTS modalities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
)";
$pdo->exec($sql_modalities);
echo "Checked/Created modalities table.\n";

// 3. Seed ld_types
$ld_types_data = [
    'Instructional Learning and Development',
    'Supervisory Learning and Development',
    'Curriculum Learning and Development',
    'Leadership and Management Development',
    'Human Resource and Organizational Development',
    'Technical and Functional Learning and Development',
    'Research and Innovation Development',
    'Values, Ethics, and Professional Development',
    'Learning and Development for Learner Support Services'
];

foreach ($ld_types_data as $type) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO ld_types (name) VALUES (:name)");
    $stmt->execute(['name' => $type]);
}
echo "Seeded ld_types.\n";

// 4. Seed modalities
$modalities_data = [
    'Face to Face Training',
    'Online or Virtual Training',
    'Blended Learning',
    'Self Paced or Independent Learning',
    'Coaching and Mentoring',
    'Mobile Learning'
];

foreach ($modalities_data as $modality) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO modalities (name) VALUES (:name)");
    $stmt->execute(['name' => $modality]);
}
echo "Seeded modalities.\n";

echo "Migration completed successfully.\n";
