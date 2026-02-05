<?php
// update_ld_options.php

require_once 'app/Config/Database.php';

use App\Config\Database;

$db = new Database();
$pdo = $db->getConnection();

echo "Starting option update...\n";

// 1. Add new Modalities
$new_modalities = [
    'Formal Training',
    'Job-Embedded Learning',
    'Relationship Discussion Learning',
    'Learning Action Cell'
];

foreach ($new_modalities as $modality) {
    // Check if exists (simple check)
    $stmt = $pdo->prepare("SELECT id FROM modalities WHERE name = :name");
    $stmt->execute(['name' => $modality]);
    if (!$stmt->fetch()) {
        $insert = $pdo->prepare("INSERT INTO modalities (name) VALUES (:name)");
        $insert->execute(['name' => $modality]);
        echo "Added Modality: $modality\n";
    } else {
        echo "Skipped (Exists): $modality\n";
    }
}

// 2. Add 'Others' to Types
$new_types = [
    'Others'
];

foreach ($new_types as $type) {
    $stmt = $pdo->prepare("SELECT id FROM ld_types WHERE name = :name");
    $stmt->execute(['name' => $type]);
    if (!$stmt->fetch()) {
        $insert = $pdo->prepare("INSERT INTO ld_types (name) VALUES (:name)");
        $insert->execute(['name' => $type]);
        echo "Added Type: $type\n";
    } else {
        echo "Skipped (Exists): $type\n";
    }
}

echo "Option update completed.\n";
