<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

try {
    $mail = new PHPMailer(true);
    echo "PHPMailer loaded successfully.\n";
} catch (Exception $e) {
    echo "Failed to load PHPMailer: " . $e->getMessage() . "\n";
}
