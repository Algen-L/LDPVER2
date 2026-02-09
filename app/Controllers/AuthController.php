<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database;
use PDO;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
                $employee_number = trim($_POST['employee_number'] ?? '');
                $area_of_specialization = trim($_POST['area_of_specialization'] ?? '');
                $age = isset($_POST['age']) ? (int) $_POST['age'] : 0;
                $sex = trim($_POST['sex'] ?? '');
                $gmail = trim($_POST['gmail'] ?? '');
                if (empty($username) || empty($password) || empty($full_name) || empty($gmail)) {
                    $message = "Please fill in all required fields.";
                } else {
                    // Check if username or email exists
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR gmail = ?");
                    $stmt->execute([$username, $gmail]);
                    if ($stmt->fetch()) {
                        $message = "Username or Gmail already exists.";
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $sql = "INSERT INTO users (username, password, full_name, office_station, position, area_of_specialization, age, sex, gmail, employee_number, is_active, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 'user')";
                        $stmt = $pdo->prepare($sql);
                        try {
                            if ($stmt->execute([$username, $hashed_password, $full_name, $office_station, $position, $area_of_specialization, $age, $sex, $gmail, $employee_number])) {
                                $message = "Registration successful! Your account is pending HR verification.";
                                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || isset($_POST['register'])) {
                                    header('Content-Type: application/json');
                                    echo json_encode(['status' => 'success', 'message' => $message]);
                                    exit;
                                }
                                $isRegistration = false; // Switch back to login
                            } else {
                                $message = "Registration failed. Please try again.";
                            }
                        } catch (\PDOException $e) {
                            $message = "Something went wrong: " . $e->getMessage();
                        }
                    }
                }


                // If AJAX failed or was not successful yet
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => $message]);
                    exit;
                }
            } elseif (isset($_POST['forgot_password'])) {
                $email = trim($_POST['email']);
                $stmt = $pdo->prepare("SELECT username, full_name, gmail, role FROM users WHERE gmail = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    if ($user['role'] === 'super_admin') {
                        echo json_encode(['status' => 'error', 'message' => 'Password reset is not available for Super Admin accounts.']);
                        exit;
                    }

                    // 0. Check Hourly Rate Limit (3 per hour)
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reset_request_logs WHERE email = ? AND requested_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
                    $stmt->execute([$email]);
                    $requestCount = $stmt->fetchColumn();

                    if ($requestCount >= 3) {
                        echo json_encode(['status' => 'error', 'message' => 'Maximum reset requests reached for this hour. Please try again in an hour.']);
                        exit;
                    }

                    $username = $user['username'];
                    // 1. Check for active token (non-expired) and cooldown
                    $stmt = $pdo->prepare("SELECT token, created_at, TIMESTAMPDIFF(SECOND, created_at, NOW()) as diff FROM password_resets WHERE username = ? AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
                    $stmt->execute([$username]);
                    $activeToken = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($activeToken) {
                        $token = $activeToken['token'];
                        $message = "Your active verification token has been re-sent to your Gmail. It will expire in 5 minutes.";
                        $requestType = 'resend';
                    } else {
                        // 2. Invalidate all previous/expired tokens for this user
                        $pdo->prepare("DELETE FROM password_resets WHERE username = ?")->execute([$username]);

                        $token = sprintf("%06d", mt_rand(1, 999999));
                        $message = "A reset token has been sent to your registered Gmail address. Note: The token expires in 5 minutes.";
                        $requestType = 'request';

                        // 3. Store new token with 5-minute expiration
                        $stmt = $pdo->prepare("INSERT INTO password_resets (username, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 5 MINUTE))");
                        $stmt->execute([$username, $token]);
                    }

                    // Log the request with type
                    $pdo->prepare("INSERT INTO reset_request_logs (email, type) VALUES (?, ?)")->execute([$email, $requestType]);

                    // Increment page visits / activity for this email
                    $pdo->prepare("INSERT INTO security_tracking (email, page_visits) VALUES (?, 1) ON DUPLICATE KEY UPDATE page_visits = page_visits + 1, last_activity = CURRENT_TIMESTAMP")->execute([$email]);

                    // Send email
                    $subject = "Security Verification Token - SDO L&D Passbook System";
                    $body = $this->getEmailTemplate(
                        "Password Reset Request",
                        "Please use the following 6-digit code to reset your password. This token is valid for 5 minutes.",
                        $token
                    );

                    if ($this->sendEmail($user['gmail'], $user['full_name'], $subject, $body)) {
                        echo json_encode(['status' => 'success', 'message' => $message]);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Failed to send email.']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Gmail address not found.']);
                }
                exit;
            } elseif (isset($_POST['verify_token'])) {
                $email = trim($_POST['email']);
                $token = trim($_POST['token']);

                $stmt = $pdo->prepare("SELECT username FROM users WHERE gmail = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $username = $user['username'];
                    $stmt = $pdo->prepare("SELECT id, token, attempts FROM password_resets WHERE username = ? AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
                    $stmt->execute([$username]);
                    $resetRecord = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($resetRecord) {
                        if ($resetRecord['token'] === $token) {
                            echo json_encode(['status' => 'success', 'message' => 'Token verified successfully.']);
                        } else {
                            $newAttempts = $resetRecord['attempts'] + 1;
                            if ($newAttempts >= 5) {
                                $pdo->prepare("DELETE FROM password_resets WHERE id = ?")->execute([$resetRecord['id']]);
                                echo json_encode(['status' => 'attempts_exceeded', 'message' => 'Too many failed attempts. Please request a new token.']);
                            } else {
                                $pdo->prepare("UPDATE password_resets SET attempts = ? WHERE id = ?")->execute([$newAttempts, $resetRecord['id']]);
                                $remaining = 5 - $newAttempts;
                                echo json_encode(['status' => 'error', 'message' => "Invalid token. $remaining attempts remaining."]);
                            }
                        }
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Invalid or expired token. Please request a new one.']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Gmail address not found.']);
                }
                exit;
            } elseif (isset($_POST['reset_password'])) {
                $email = trim($_POST['email']);
                $token = trim($_POST['token']);
                $password = trim($_POST['password']);

                $stmt = $pdo->prepare("SELECT username FROM users WHERE gmail = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $username = $user['username'];
                    $stmt = $pdo->prepare("SELECT id FROM password_resets WHERE username = ? AND token = ? AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
                    $stmt->execute([$username, $token]);

                    if ($stmt->fetch()) {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
                        if ($stmt->execute([$hashed_password, $username])) {
                            $pdo->prepare("DELETE FROM password_resets WHERE username = ?")->execute([$username]);
                            echo json_encode(['status' => 'success', 'message' => 'Password reset successful! You can now login with your new password.']);
                        } else {
                            echo json_encode(['status' => 'error', 'message' => 'Failed to reset password.']);
                        }
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Invalid session or token expired.']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Gmail address not found.']);
                }
                exit;
            } elseif (isset($_POST['log_reset_visit'])) {
                $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                if ($email) {
                    $pdo->prepare("INSERT INTO security_tracking (email, page_visits) VALUES (?, 1) ON DUPLICATE KEY UPDATE page_visits = page_visits + 1, last_activity = CURRENT_TIMESTAMP")->execute([$email]);
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Email missing']);
                }
                exit;
            } else {
                $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                $password = isset($_POST['password']) ? trim($_POST['password']) : '';

                if (empty($email) || empty($password)) {
                    $message = "Please enter both Gmail address and password.";
                } else {
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE gmail = ?");
                    $stmt->execute([$email]);
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
                        $message = "Invalid Gmail address or password.";
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
