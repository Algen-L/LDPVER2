<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database;
use PDO;

class AuthController extends Controller
{
    public function index()
    {
        $db = new Database();
        $pdo = $db->getConnection();

        $message = '';
        $isRegistration = false;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['register'])) {
                $isRegistration = true;
                $username = trim($_POST['reg_username']);
                $password = trim($_POST['reg_password']);
                $full_name = trim($_POST['full_name']);
                $office_station = trim($_POST['office_station'] ?? '');
                $position = trim($_POST['position'] ?? '');
                $rating_period = trim($_POST['rating_period'] ?? '');
                $area_of_specialization = trim($_POST['area_of_specialization'] ?? '');
                $age = isset($_POST['age']) ? (int) $_POST['age'] : 0;
                $sex = trim($_POST['sex'] ?? '');

                if (empty($username) || empty($password) || empty($full_name)) {
                    $message = "Please fill in all required fields.";
                } else {
                    // Check if username exists
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                    $stmt->execute([$username]);
                    if ($stmt->fetch()) {
                        $message = "Username already exists.";
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $sql = "INSERT INTO users (username, password, full_name, office_station, position, rating_period, area_of_specialization, age, sex, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$username, $hashed_password, $full_name, $office_station, $position, $rating_period, $area_of_specialization, $age, $sex])) {
                            $message = "Registration successful! Your account is pending HR verification.";
                            $isRegistration = false; // Switch back to login
                        } else {
                            $message = "Something went wrong. Please try again.";
                        }
                    }
                }
            } else {
                $username = trim($_POST['username']);
                $password = trim($_POST['password']);

                if (empty($username) || empty($password)) {
                    $message = "Please enter both username and password.";
                } else {
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
                    $stmt->execute([$username]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($user && password_verify($password, $user['password'])) {
                        if (isset($user['is_active']) && $user['is_active'] == 0) {
                            $message = "Your account is pending HR verification. Please wait for approval.";
                        } else {
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['full_name'] = $user['full_name'];
                            $_SESSION['role'] = $user['role'];
                            $_SESSION['position'] = $user['position'];

                            $logStmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, ip_address) VALUES (?, ?, ?)");
                            $logStmt->execute([$user['id'], 'Logged In', $_SERVER['REMOTE_ADDR']]);

                            // REDIRECT TO APPROPRIATE DASHBOARD
                            if ($user['role'] === 'admin' || $user['role'] === 'super_admin' || $user['role'] === 'immediate_head' || $user['role'] === 'head_hr') {
                                $this->redirect('admin/dashboard');
                            } elseif ($user['role'] === 'hr') {
                                $this->redirect('hr/dashboard');
                            } else {
                                $this->redirect('user/home');
                            }
                        }
                    } else {
                        $message = "Invalid username or password.";
                    }
                }
            }
        }

        // Fetch Offices for Dropdown
        try {
            $stmt_offices = $pdo->query("SELECT category, name, id FROM offices ORDER BY category, name");
            $offices_list = $stmt_offices->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $offices_list = [];
        }

        // Pass data to view
        $this->view('auth/login', [
            'message' => $message,
            'isRegistration' => $isRegistration,
            'offices_list' => $offices_list
        ]);
    }
}
