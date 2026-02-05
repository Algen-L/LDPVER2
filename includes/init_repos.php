<?php
/**
 * Repository Initializer
 * Includes all repository classes and instantiates them using the global $pdo connection.
 */
require_once __DIR__ . '/error_handler.php';
require_once __DIR__ . '/../app/Models/UserRepository.php';
require_once __DIR__ . '/../app/Models/ActivityRepository.php';
require_once __DIR__ . '/../app/Models/ILDNRepository.php';
require_once __DIR__ . '/../app/Models/ActivityLogRepository.php';
require_once __DIR__ . '/../app/Models/NotificationRepository.php';

// Include global utility functions (unchanged)
require_once __DIR__ . '/functions/user-functions.php';
require_once __DIR__ . '/functions/activity-functions.php';

if (!isset($pdo)) {
    require_once __DIR__ . '/db.php';
}

$userRepo = new \App\Models\UserRepository($pdo);
$activityRepo = new \App\Models\ActivityRepository($pdo);
$ildnRepo = new \App\Models\ILDNRepository($pdo);
$logRepo = new \App\Models\ActivityLogRepository($pdo);
$notifRepo = new \App\Models\NotificationRepository($pdo);

