<?php
namespace App\Core;

class Controller
{
    public function view($__view_name, $data = [])
    {
        extract($data, EXTR_SKIP);
        $__view_path = BASE_PATH . "app/Views/" . $__view_name . ".php";

        if (file_exists($__view_path)) {
            require_once $__view_path;
        } else {
            // Fallback for legacy relative paths if BASE_PATH fails or for debugging
            require_once "../app/Views/" . $__view_name . ".php";
        }
    }

    public function redirect($url)
    {
        if (strpos($url, 'http') === 0 || strpos($url, '/') === 0) {
            header("Location: " . $url);
        } else {
            // Prepend the script name (public/index.php) to make it relative to the router
            $base = $_SERVER['SCRIPT_NAME'];
            header("Location: " . $base . "/" . ltrim($url, '/'));
        }
        exit;
    }

    // Helper to get database connection if needed
    protected function getDB()
    {
        $db = new \App\Config\Database();
        return $db->getConnection();
    }
}
