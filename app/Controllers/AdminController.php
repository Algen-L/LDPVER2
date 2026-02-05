<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ActivityRepository;
use App\Models\UserRepository;
use App\Models\ActivityLogRepository;
use App\Models\NotificationRepository;
use App\Models\ILDNRepository;
use PDO;
use Exception;
use DateTime;
use DateInterval;
use DatePeriod;

// Include global utility functions
require_once __DIR__ . '/../../includes/functions/user-functions.php';
require_once __DIR__ . '/../../includes/functions/activity-functions.php';
require_once __DIR__ . '/../../includes/functions/file-functions.php';

class AdminController extends Controller
{
    private $activityRepo;
    private $userRepo;
    private $logRepo;
    private $notifRepo;
    private $ildnRepo;
    private $pdo;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin' && $_SESSION['role'] !== 'immediate_head' && $_SESSION['role'] !== 'head_hr' && $_SESSION['role'] !== 'hr')) {
            $this->redirect('../index.php'); // Redirect to login
        }

        // Init Database and Repositories
        $this->pdo = $this->getDB();
        $this->activityRepo = new ActivityRepository($this->pdo);
        $this->userRepo = new UserRepository($this->pdo);
        $this->logRepo = new ActivityLogRepository($this->pdo);
        $this->notifRepo = new NotificationRepository($this->pdo);
        $this->ildnRepo = new ILDNRepository($this->pdo);
    }

    public function dashboard()
    {
        $data = $this->getDashboardStats();
        $this->view('admin/dashboard', $data);
    }

    public function dashboardApi()
    {
        header('Content-Type: application/json');
        try {
            $data = $this->getDashboardStats();

            $response = [
                'status' => 'success',
                'isHR' => ($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr'),
                'stats' => [],
                'charts' => [
                    'frequency' => [
                        'labels' => ($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') ? array_keys($data['registrationGrowth']) : $data['freqLabels'],
                        'values' => ($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') ? array_values($data['registrationGrowth']) : $data['freqValues']
                    ],
                    'office' => [
                        'osds' => ($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') ? $data['popOSDS'] : $data['osdsCount'],
                        'cid' => ($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') ? $data['popCID'] : $data['cidCount'],
                        'sgod' => ($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') ? $data['popSGOD'] : $data['sgodCount']
                    ]
                ]
            ];

            if ($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') {
                $response['stats'] = [
                    'today_logins' => $data['hrStats']['today_logins'] ?? 0,
                    'total_users' => $data['totalUsers'],
                    'new_registrations' => $data['hrStats']['new_registrations'] ?? 0,
                    'active_today' => $data['hrStats']['active_today'] ?? 0
                ];
            } else {
                $response['stats'] = [
                    'total_submissions' => $data['totalSubmissions'],
                    'total_users' => $data['totalUsers'],
                    'pending' => $data['pendingCount'],
                    'approved' => $data['approvedCount']
                ];
            }

            echo json_encode($response);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    private function getDashboardStats()
    {
        // Filter Logic
        $filter = $_GET['filter'] ?? 'month'; // Default to month
        $date_from = $_GET['date_from'] ?? '';
        $date_to = $_GET['date_to'] ?? '';

        $filters = [
            'filter_type' => $filter,
            'start_date' => $date_from,
            'end_date' => $date_to
        ];

        $activities = $this->activityRepo->getAllActivities($filters);

        // Calculate Statistics
        $totalSubmissions = count($activities);

        // Fetch all offices from DB for categorization
        $office_map = []; // [OfficeName => Category]
        try {
            $stmt_all_offices = $this->pdo->query("SELECT name, category FROM offices");
            while ($row = $stmt_all_offices->fetch(PDO::FETCH_ASSOC)) {
                $office_map[strtoupper($row['name'])] = $row['category'];
            }
        } catch (Exception $e) { /* Fallback or empty */
        }

        // Fetch Users Count
        $totalUsers = $this->userRepo->getTotalUserCount();

        // Count per status
        $pendingCount = 0;
        $approvedCount = 0;
        foreach ($activities as $act) {
            if ($act['status'] === 'Pending')
                $pendingCount++;
            if ($act['status'] === 'Approved')
                $approvedCount++;
        }

        // Analytics: Submissions by General Office
        $osdsCount = 0;
        $cidCount = 0;
        $sgodCount = 0;
        $frequencyData = []; // To store [date => count]

        foreach ($activities as $act) {
            // Categorize using DB map
            $office = strtoupper($act['office_station'] ?? '');
            $category = $office_map[$office] ?? '';

            if ($category === 'OSDS') {
                $osdsCount++;
            } elseif ($category === 'CID') {
                $cidCount++;
            } elseif ($category === 'SGOD') {
                $sgodCount++;
            }

            // Group for frequency chart
            $actDate = $act['activity_created_at'] ?? $act['created_at'];
            if (isset($actDate)) {
                $dateKey = date('Y-m-d', strtotime($actDate));
                $frequencyData[$dateKey] = ($frequencyData[$dateKey] ?? 0) + 1;
            }
        }

        // Sort frequency by date and prepare for JS
        ksort($frequencyData);
        $freqLabels = array_keys($frequencyData);
        $freqValues = array_values($frequencyData);

        // Initialize HR variables
        $popOSDS = 0;
        $popCID = 0;
        $popSGOD = 0;

        // --- HR SPECIFIC ANALYTICS ---
        $hrStats = [];
        $auditTrail = [];
        $activePersonnel = [];
        $registrationGrowth = [];

        if ($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') {
            try {
                // 1. Today's Logins
                $stmt_logins = $this->pdo->prepare("SELECT COUNT(*) FROM activity_logs WHERE action = 'Logged In' AND DATE(created_at) = CURRENT_DATE");
                $stmt_logins->execute();
                $hrStats['today_logins'] = $stmt_logins->fetchColumn();

                // 2. New Registrations (Selected Period)
                $date_filter_sql = "";
                $date_params = [];
                if ($filter === 'today') {
                    $date_filter_sql = "AND DATE(created_at) = CURRENT_DATE";
                } elseif ($filter === 'week') {
                    $date_filter_sql = "AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                } elseif ($filter === 'month') {
                    $date_filter_sql = "AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                } elseif ($filter === 'custom' && $date_from && $date_to) {
                    $date_filter_sql = "AND DATE(created_at) BETWEEN ? AND ?";
                    $date_params = [$date_from, $date_to];
                }

                $stmt_new_users = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE 1=1 $date_filter_sql");
                $stmt_new_users->execute($date_params);
                $hrStats['new_registrations'] = $stmt_new_users->fetchColumn();

                // 3. Active Personnel Today
                $stmt_active_today = $this->pdo->prepare("SELECT COUNT(DISTINCT user_id) FROM activity_logs WHERE DATE(created_at) = CURRENT_DATE");
                $stmt_active_today->execute();
                $hrStats['active_today'] = $stmt_active_today->fetchColumn();

                // 4. Registration Growth (Chart Data)
                $stmt_growth = $this->pdo->prepare("SELECT DATE(created_at) as date, COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY DATE(created_at) ORDER BY date ASC");
                $stmt_growth->execute();
                $growthData = $stmt_growth->fetchAll(PDO::FETCH_KEY_PAIR);

                // Fill gaps in growth data for last 30 days
                $begin = new DateTime('30 days ago');
                $end = new DateTime('tomorrow');
                $interval = new DateInterval('P1D');
                $daterange = new DatePeriod($begin, $interval, $end);

                foreach ($daterange as $date) {
                    $key = $date->format("Y-m-d");
                    $registrationGrowth[$key] = $growthData[$key] ?? 0;
                }

                // 5. System Audit Trail
                $auditTrail = $this->logRepo->getAllLogs([
                    'limit' => 20,
                    'action_type' => ''
                ]);

                // 6. Recently Active Personnel
                $stmt_recent_users = $this->pdo->prepare("
                    SELECT u.id, u.full_name, u.profile_picture, u.office_station, MAX(l.created_at) as last_seen 
                    FROM users u 
                    JOIN activity_logs l ON u.id = l.user_id 
                    GROUP BY u.id 
                    ORDER BY last_seen DESC 
                    LIMIT 10
                ");
                $stmt_recent_users->execute();
                $activePersonnel = $stmt_recent_users->fetchAll(PDO::FETCH_ASSOC);

                // 7. Population by Office
                $popOSDS = 0;
                $popCID = 0;
                $popSGOD = 0;
                $stmt_pop = $this->pdo->query("SELECT office_station FROM users WHERE is_active = 1");
                while ($u = $stmt_pop->fetch(PDO::FETCH_ASSOC)) {
                    $cat = $office_map[strtoupper($u['office_station'])] ?? '';
                    if ($cat === 'OSDS')
                        $popOSDS++;
                    elseif ($cat === 'CID')
                        $popCID++;
                    elseif ($cat === 'SGOD')
                        $popSGOD++;
                }

            } catch (Exception $e) { /* Handle error */
            }
        }

        // Data to pass to view
        return [
            'filter' => $filter,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'activities' => $activities,
            'totalSubmissions' => $totalSubmissions,
            'totalUsers' => $totalUsers,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'osdsCount' => $osdsCount,
            'cidCount' => $cidCount,
            'sgodCount' => $sgodCount,
            'frequencyData' => $frequencyData,
            'freqLabels' => $freqLabels,
            'freqValues' => $freqValues,
            'hrStats' => $hrStats,
            'auditTrail' => $auditTrail,
            'activePersonnel' => $activePersonnel,
            'registrationGrowth' => $registrationGrowth,
            'popOSDS' => $popOSDS,
            'popCID' => $popCID,
            'popSGOD' => $popSGOD,
            'office_map' => $office_map,
            'pdo' => $this->pdo,
            'user' => $this->userRepo->getUserById($_SESSION['user_id']),
            'notifRepo' => $this->notifRepo
        ];
    }

    public function manageUsers()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['update_user'])) {
                $user_id = (int) $_POST['user_id'];
                $office = trim($_POST['office_station']);

                if ($_SESSION['role'] === 'super_admin') {
                    $role = $_POST['role'];
                    $success = $this->userRepo->updateUserRole($user_id, $role, $office);
                } elseif ($_SESSION['role'] === 'head_hr') {
                    // Head HR Case
                    $requested_role = $_POST['role'] ?? null;
                    $target_user = $this->userRepo->getUserById($user_id);

                    if ($target_user && $target_user['role'] === 'super_admin') {
                        $_SESSION['toast'] = ['title' => 'Error', 'message' => 'Head HR cannot edit Super Admin profiles.', 'type' => 'error'];
                        $this->redirect('admin/manage-users');
                    }

                    // If changing role, Head HR cannot set it to super_admin or head_hr
                    if ($requested_role && $requested_role !== 'super_admin' && $requested_role !== 'head_hr') {
                        $success = $this->userRepo->updateUserRole($user_id, $requested_role, $office);
                    } else {
                        $success = $this->userRepo->updateUserProfile($user_id, ['office_station' => $office]);
                    }
                } else {
                    // HR Case: Check if target is super_admin or head_hr
                    $target_user = $this->userRepo->getUserById($user_id);
                    if ($target_user && ($target_user['role'] === 'super_admin' || $target_user['role'] === 'head_hr')) {
                        $_SESSION['toast'] = ['title' => 'Error', 'message' => 'HR cannot edit higher-tier administrative profiles.', 'type' => 'error'];
                        $this->redirect('admin/manage-users');
                    }
                    $success = $this->userRepo->updateUserProfile($user_id, ['office_station' => $office]);
                }

                if ($success) {
                    $_SESSION['toast'] = ['title' => 'Success', 'message' => 'User updated successfully!', 'type' => 'success'];
                    $this->logRepo->logAction($_SESSION['user_id'], 'Updated User Record', "User ID: $user_id");
                    $this->redirect('admin/manage-users');
                }
            } elseif (isset($_POST['delete_user'])) {
                $user_id = (int) $_POST['user_id'];
                if ($user_id != $_SESSION['user_id']) {
                    $target_user = $this->userRepo->getUserById($user_id);
                    $target_role = $target_user['role'] ?? null;

                    $can_delete = false;
                    if ($_SESSION['role'] === 'super_admin')
                        $can_delete = true;
                    elseif ($_SESSION['role'] === 'head_hr' && $target_role !== 'super_admin' && $target_role !== 'head_hr')
                        $can_delete = true;
                    elseif ($_SESSION['role'] === 'hr' && $target_role === 'user')
                        $can_delete = true;

                    // Final safety
                    if (($_SESSION['role'] === 'hr' || $_SESSION['role'] === 'head_hr') && ($target_role === 'super_admin' || $target_role === 'head_hr')) {
                        $can_delete = false;
                    }

                    if ($can_delete) {
                        if ($this->userRepo->deleteUser($user_id)) {
                            $_SESSION['toast'] = ['title' => 'Deleted', 'message' => 'User account permanently removed.', 'type' => 'success'];
                            $this->logRepo->logAction($_SESSION['user_id'], 'Deleted User', "User ID: $user_id removed.");
                            $this->redirect('admin/manage-users');
                        }
                    } else {
                        $_SESSION['toast'] = ['title' => 'Error', 'message' => 'Permission denied.', 'type' => 'error'];
                    }
                } else {
                    $_SESSION['toast'] = ['title' => 'Error', 'message' => 'You cannot delete yourself!', 'type' => 'error'];
                }
                $this->redirect('admin/manage-users');
            } elseif (isset($_POST['toggle_active'])) {
                $user_id = (int) $_POST['user_id'];
                $new_status = (int) $_POST['new_status'];

                if ($user_id != $_SESSION['user_id']) {
                    $target_user = $this->userRepo->getUserById($user_id);
                    $target_role = $target_user['role'] ?? null;

                    $can_toggle = false;
                    if ($_SESSION['role'] === 'super_admin')
                        $can_toggle = true;
                    elseif ($_SESSION['role'] === 'hr' && $target_role === 'user')
                        $can_toggle = true;

                    if ($can_toggle) {
                        if ($this->userRepo->toggleUserStatus($user_id, $new_status)) {
                            $status_text = $new_status ? 'activated' : 'deactivated';
                            $_SESSION['toast'] = ['title' => 'Updated', 'message' => "Account $status_text successfully.", 'type' => 'success'];
                            $this->logRepo->logAction($_SESSION['user_id'], 'Toggled Status', "User ID: $user_id $status_text.");
                        }
                    }
                }
                $this->redirect('admin/manage-users');
            } elseif (isset($_POST['approve_registration'])) {
                $user_id = (int) $_POST['user_id'];
                if ($this->userRepo->activateUser($user_id)) {
                    $_SESSION['toast'] = ['title' => 'Approved', 'message' => 'User registration approved!', 'type' => 'success'];
                    $this->logRepo->logAction($_SESSION['user_id'], 'Approved Registration', "User ID: $user_id");
                }
                $this->redirect('admin/manage-users');
            } elseif (isset($_POST['reject_registration'])) {
                $user_id = (int) $_POST['user_id'];
                if ($this->userRepo->deleteUser($user_id)) {
                    $_SESSION['toast'] = ['title' => 'Rejected', 'message' => 'Registration rejected and user deleted.', 'type' => 'success'];
                    $this->logRepo->logAction($_SESSION['user_id'], 'Rejected Registration', "User ID: $user_id");
                }
                $this->redirect('admin/manage-users');
            }
        }

        // View State Management
        $view = $_GET['view'] ?? 'active';
        $target_id = isset($_GET['id']) ? (int) $_GET['id'] : null;

        // Handle Filtering
        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'role' => trim($_GET['filter_role'] ?? ''),
            'office' => trim($_GET['filter_office'] ?? '')
        ];

        // Verification View Filters
        $ver_filters = [
            'search' => trim($_GET['ver_search'] ?? ''),
            'office' => trim($_GET['ver_office'] ?? '')
        ];

        // Audit Log Filters
        $log_filters = [
            'action_type' => 'Profile',
            'limit' => 100,
            'search' => trim($_GET['log_search'] ?? ''),
            'start_date' => trim($_GET['log_start_date'] ?? ''),
            'end_date' => trim($_GET['log_end_date'] ?? ''),
            'office_filter' => trim($_GET['log_office'] ?? '')
        ];

        // Fetch distinct office categories
        $office_categories = [];
        try {
            $stmt_cats = $this->pdo->query("SELECT DISTINCT category FROM offices ORDER BY category");
            $office_categories = $stmt_cats->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            $office_categories = ['CID', 'SGOD', 'OSDS']; // Fallback
        }

        $users = ($view === 'active') ? $this->userRepo->getUsersForManagement($filters) : [];
        $pending_users = $this->userRepo->getPendingUsers($ver_filters);
        $target_user = ($view === 'details' && $target_id) ? $this->userRepo->getUserById($target_id) : null;

        // Fetch logs for Notifications view
        $audit_logs = ($view === 'notifications') ? $this->logRepo->getAllLogs($log_filters) : [];

        $this->view('admin/manage_users', [
            'view' => $view,
            'target_id' => $target_id,
            'filters' => $filters,
            'office_categories' => $office_categories,
            'users' => $users,
            'pending_users' => $pending_users,
            'target_user' => $target_user,
            'audit_logs' => $audit_logs,
            'pdo' => $this->pdo,
            'user' => $this->userRepo->getUserById($_SESSION['user_id']),
            'notifRepo' => $this->notifRepo
        ]);
    }

    public function profile()
    {
        $user_id = $_SESSION['user_id'];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['update_profile_admin']) || isset($_POST['update_profile_user'])) {
                $full_name = trim($_POST['full_name']);
                $office_station = trim($_POST['office_station']);
                $position = trim($_POST['position']);
                $password = $_POST['password'];

                $updateData = [
                    'full_name' => $full_name,
                    'office_station' => $office_station,
                    'position' => $position
                ];

                // Handle Profile Picture
                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = '../public/uploads/profile_pics/';
                    if (!is_dir($uploadDir))
                        mkdir($uploadDir, 0777, true);
                    $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', basename($_FILES['profile_picture']['name']));
                    $targetPath = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
                        $updateData['profile_picture'] = 'uploads/profile_pics/' . $fileName;
                    }
                }

                if ($password) {
                    $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
                }

                if ($_SESSION['role'] === 'immediate_head') {
                    $updateData['age'] = (int) $_POST['age'];
                    $updateData['sex'] = trim($_POST['sex']);
                    $updateData['rating_period'] = trim($_POST['rating_period']);
                    $updateData['area_of_specialization'] = trim($_POST['area_of_specialization']);
                }

                if ($this->userRepo->updateUserProfile($user_id, $updateData)) {
                    $_SESSION['toast'] = ['title' => 'Profile Updated', 'message' => 'Your profile has been successfully updated.', 'type' => 'success'];
                    $_SESSION['full_name'] = $full_name;

                    if (isset($updateData['profile_picture'])) {
                        $_SESSION['profile_picture'] = $updateData['profile_picture'];
                    }

                    if ($_SESSION['role'] !== 'super_admin') {
                        $this->logRepo->logAction($user_id, 'Profile Updated', 'User updated their personal information and/or profile picture.');
                    }
                } else {
                    $_SESSION['toast'] = ['title' => 'Update Failed', 'message' => 'There was an error updating your profile.', 'type' => 'error'];
                }
                $this->redirect('admin/profile');
            } elseif (isset($_POST['send_notification'])) {
                $recipient_id = $_POST['recipient_id'];
                $notif_message = trim($_POST['notif_message']);

                if (!empty($recipient_id) && !empty($notif_message)) {
                    if ($recipient_id === 'all') {
                        if ($this->notifRepo->sendBroadcastNotification($user_id, $notif_message)) {
                            $_SESSION['toast'] = ['title' => 'Broadcast Sent', 'message' => 'Your notification has been sent to all users.', 'type' => 'success'];
                            $this->logRepo->logAction($user_id, 'Sent Broadcast Notification', "Message: " . substr($notif_message, 0, 50) . "...");
                        }
                    } elseif ((int) $recipient_id > 0) {
                        if ($this->notifRepo->sendNotification($user_id, (int) $recipient_id, $notif_message)) {
                            $_SESSION['toast'] = ['title' => 'Message Sent', 'message' => 'Your notification has been successfully delivered.', 'type' => 'success'];
                            $this->logRepo->logAction($user_id, 'Sent Notification', "Recipient ID: $recipient_id");
                        }
                    }
                }
                $this->redirect('admin/profile');
            }
        }

        $this->view('admin/profile', [
            'user' => $this->userRepo->getUserById($user_id),
            'all_users' => $this->userRepo->getAllUsers(),
            'is_super_admin' => ($_SESSION['role'] === 'super_admin'),
            'pdo' => $this->pdo,
            'notifRepo' => $this->notifRepo
        ]);
    }

    public function activityLogs()
    {
        // Handle Log Filtering
        $filters = [
            'search' => isset($_GET['search']) ? trim($_GET['search']) : '',
            'user_id' => isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0,
            'action_type' => isset($_GET['action_type']) ? $_GET['action_type'] : '',
            'start_date' => isset($_GET['start_date']) ? $_GET['start_date'] : '',
            'end_date' => isset($_GET['end_date']) ? $_GET['end_date'] : '',
            'limit' => 100
        ];

        // Special divisional keyword handling (OSDS, CID, SGOD)
        if ($filters['search']) {
            $search_upper = strtoupper($filters['search']);
            if (in_array($search_upper, ['OSDS', 'CID', 'SGOD'])) {
                $filters['office_filter'] = $search_upper;
                $filters['search'] = '';
            }
        }

        $logs = $this->logRepo->getAllLogs($filters);

        $this->view('admin/activity_logs', [
            'filters' => $filters,
            'logs' => $logs,
            'pdo' => $this->pdo,
            'user' => $this->userRepo->getUserById($_SESSION['user_id']),
            'notifRepo' => $this->notifRepo
        ]);
    }

    public function editUser()
    {
        // Check if user has permission
        if ($_SESSION['role'] !== 'super_admin' && $_SESSION['role'] !== 'hr' && $_SESSION['role'] !== 'head_hr') {
            $this->redirect('admin/dashboard');
        }

        if (!isset($_GET['id'])) {
            $this->redirect('admin/manage-users');
        }

        $id = (int) $_GET['id'];
        $user_to_edit = $this->userRepo->getUserById($id);

        if (!$user_to_edit) {
            $this->redirect('admin/manage-users');
        }

        // Protection: HR and Head HR cannot edit Super Admin
        if (($_SESSION['role'] === 'hr' || $_SESSION['role'] === 'head_hr') && $user_to_edit['role'] === 'super_admin') {
            $_SESSION['toast'] = ['title' => 'Access Denied', 'message' => 'You do not have permission to edit a Super Admin account.', 'type' => 'error'];
            $this->redirect('admin/manage-users');
        }

        $message = '';
        $messageType = '';

        // Handle Form Submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $full_name = trim($_POST['full_name']);
            $username = trim($_POST['username']);
            $office_station = trim($_POST['office_station']);
            $position = trim($_POST['position']);
            $rating_period = trim($_POST['rating_period'] ?? '');
            $area_of_specialization = trim($_POST['area_of_specialization'] ?? '');
            $age = (int) ($_POST['age'] ?? 0);
            $sex = trim($_POST['sex'] ?? '');
            $password = trim($_POST['password']);

            // Only Super Admin and Head HR can change role
            $role = $user_to_edit['role'];
            if ($_SESSION['role'] === 'super_admin') {
                $role = $_POST['role'];
            } elseif ($_SESSION['role'] === 'head_hr') {
                $requested_role = $_POST['role'];
                if ($requested_role !== 'super_admin' && $requested_role !== 'head_hr') {
                    $role = $requested_role;
                }
            }

            // Final security check
            if ($_SESSION['role'] !== 'super_admin' && $user_to_edit['role'] === 'super_admin') {
                $_SESSION['toast'] = ['title' => 'Error', 'message' => 'Critical security check failed.', 'type' => 'error'];
                $this->redirect('admin/manage-users');
            }

            // Handle Profile Picture
            $dbPath = $user_to_edit['profile_picture'];
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../public/uploads/profile_pics/';
                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0777, true);
                $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', basename($_FILES['profile_picture']['name']));
                $targetPath = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
                    $dbPath = 'uploads/profile_pics/' . $fileName;
                }
            }

            $updateData = [
                'full_name' => $full_name,
                'username' => $username,
                'office_station' => $office_station,
                'position' => $position,
                'rating_period' => $rating_period,
                'area_of_specialization' => $area_of_specialization,
                'age' => $age,
                'sex' => $sex,
                'role' => $role,
                'profile_picture' => $dbPath
            ];

            if ($password) {
                $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            if ($this->userRepo->updateUserProfile($id, $updateData)) {
                if ($user_to_edit['role'] !== 'super_admin') {
                    $this->logRepo->logAction($_SESSION['user_id'], 'Profile Modified by Admin', "Profile of $full_name was updated by " . $_SESSION['role']);
                }
                $_SESSION['toast'] = ['title' => 'User Updated', 'message' => 'The user record has been updated successfully.', 'type' => 'success'];
                $this->redirect('admin/manage-users');
            } else {
                $message = "Update failed. Please check for duplicate username.";
                $messageType = "error";
            }
        }

        $this->view('admin/edit_user', [
            'user_to_edit' => $user_to_edit,
            'message' => $message,
            'messageType' => $messageType,
            'pdo' => $this->pdo,
            'user' => $this->userRepo->getUserById($_SESSION['user_id']),
            'notifRepo' => $this->notifRepo
        ]);
    }

    public function userStatus()
    {
        // Fetch Users with Expanded Metrics & Office Categories
        $sql_users = "SELECT 
                        u.id, u.username, u.full_name, u.office_station, u.role, u.position, u.profile_picture, u.created_at as joined_at,
                        o.category as office_division,
                        (SELECT id FROM ld_activities WHERE user_id = u.id ORDER BY created_at DESC LIMIT 1) as latest_activity_id,
                        (SELECT title FROM ld_activities WHERE user_id = u.id ORDER BY created_at DESC LIMIT 1) as latest_activity_title,
                        (SELECT MAX(created_at) FROM ld_activities WHERE user_id = u.id) as latest_submission,
                        (SELECT created_at FROM activity_logs WHERE user_id = u.id ORDER BY id DESC LIMIT 1) as last_action_time
                      FROM users u
                      LEFT JOIN offices o ON UPPER(u.office_station) = UPPER(o.name)
                      WHERE u.role != 'admin' AND u.role != 'super_admin'
                      ORDER BY latest_submission DESC";
        $stmt_users = $this->pdo->query($sql_users);
        $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

        // Fetch Submission Statistics
        $sql_stats = "SELECT 
                        user_id,
                        COUNT(*) as total,
                        SUM(CASE WHEN approved_sds = 1 THEN 1 ELSE 0 END) as approved,
                        SUM(CASE WHEN reviewed_by_supervisor = 0 THEN 1 ELSE 0 END) as pending
                      FROM ld_activities
                      GROUP BY user_id";
        $stmt_stats = $this->pdo->query($sql_stats);
        $stats = [];
        while ($row = $stmt_stats->fetch(PDO::FETCH_ASSOC)) {
            $stats[$row['user_id']] = $row;
        }

        // Fetch ILDN Statistics
        $sql_ildn = "SELECT 
                        i.user_id,
                        COUNT(*) as total_ildns,
                        SUM(CASE WHEN (SELECT COUNT(*) FROM ld_activities l WHERE l.user_id = i.user_id AND FIND_IN_SET(i.need_text, l.competency)) = 0 THEN 1 ELSE 0 END) as unaddressed_ildns
                      FROM user_ildn i
                      GROUP BY i.user_id";
        $stmt_ildn = $this->pdo->query($sql_ildn);
        $ildn_stats = [];
        while ($row = $stmt_ildn->fetch(PDO::FETCH_ASSOC)) {
            $ildn_stats[$row['user_id']] = $row;
        }

        $this->view('admin/user_status', [
            'users' => $users,
            'stats' => $stats,
            'ildn_stats' => $ildn_stats,
            'pdo' => $this->pdo,
            'user' => $this->userRepo->getUserById($_SESSION['user_id']),
            'notifRepo' => $this->notifRepo
        ]);
    }

    public function userDetails()
    {
        header('Content-Type: application/json');
        try {
            if (!isset($_GET['user_id'])) {
                throw new Exception('User ID missing');
            }

            $user_id = (int) $_GET['user_id'];
            $user = $this->userRepo->getUserById($user_id);

            if (!$user) {
                throw new Exception('User not found');
            }

            $stats = $this->activityRepo->getUserStats($user_id);
            $timeline = $_GET['timeline'] ?? 'week';

            // Generate range
            $rangeData = [];
            $now = new DateTime();
            if ($timeline === 'week') {
                for ($i = 6; $i >= 0; $i--) {
                    $d = clone $now;
                    $d->modify("-$i days");
                    $rangeData[$d->format('Y-m-d')] = ['label' => $d->format('D'), 'count' => 0];
                }
            } elseif ($timeline === 'month') {
                for ($i = 3; $i >= 0; $i--) {
                    $d = clone $now;
                    $d->modify("-$i weeks");
                    $d->modify('monday this week');
                    $rangeData[$d->format('oW')] = ['label' => $d->format('M d'), 'count' => 0];
                }
            } else {
                for ($i = 11; $i >= 0; $i--) {
                    $d = clone $now;
                    $d->modify("first day of -$i months");
                    $rangeData[$d->format('Y-m')] = ['label' => $d->format('M'), 'count' => 0];
                }
            }

            $timelineResults = $this->activityRepo->getTimelineData($user_id, $timeline);
            foreach ($timelineResults as $row) {
                if (isset($rangeData[$row['time_key']])) {
                    $rangeData[$row['time_key']]['count'] = (int) $row['count'];
                }
            }

            $all_user_activities = $this->activityRepo->getActivitiesByUser($user_id);
            $certificates = array_values(array_filter($all_user_activities, function ($a) {
                return !empty($a['certificate_path']);
            }));

            $response = [
                'user' => $user,
                'stats' => [
                    'total' => (int) ($stats['total'] ?? 0),
                    'approved' => (int) ($stats['approved'] ?? 0),
                    'pending' => (int) ($stats['pending'] ?? 0),
                    'completion_rate' => isset($stats['total']) && $stats['total'] > 0 ? round(($stats['approved'] / $stats['total']) * 100) : 0
                ],
                'activity_data' => array_values($rangeData),
                'certificates' => $certificates,
                'submissions' => $all_user_activities,
                'logs' => $this->logRepo->getLogsByUser($user_id, 5),
                'ildns' => $this->ildnRepo->getILDNsByUser($user_id)
            ];

            echo json_encode($response);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
    public function registerUser()
    {
        // Check if user is logged in and is Super Admin, Head HR, or HR
        if ($_SESSION['role'] !== 'super_admin' && $_SESSION['role'] !== 'head_hr' && $_SESSION['role'] !== 'hr') {
            $this->redirect('admin/dashboard');
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $full_name = trim($_POST['full_name']);
            $office_station = trim($_POST['office_station'] ?? '');
            $position = trim($_POST['position'] ?? '');
            $role = trim($_POST['role'] ?? 'user'); // Default to user if not set
            $rating_period = trim($_POST['rating_period'] ?? '');
            $area_of_specialization = trim($_POST['area_of_specialization'] ?? '');
            $age = isset($_POST['age']) ? (int) $_POST['age'] : 0;
            $sex = trim($_POST['sex'] ?? '');

            // Basic validation
            if (empty($username) || empty($password) || empty($full_name) || empty($role)) {
                $_SESSION['toast'] = ['title' => 'Missing Fields', 'message' => 'Please fill in all required fields.', 'type' => 'error'];
            } else {
                // Check if username exists
                if ($this->userRepo->getUserByUsername($username)) {
                    $_SESSION['toast'] = ['title' => 'Registration Error', 'message' => 'Username already exists.', 'type' => 'error'];
                } else {
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Handle Profile Picture Upload
                    $dbPath = NULL;
                    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = '../public/uploads/profile_pics/';
                        if (!is_dir($uploadDir))
                            mkdir($uploadDir, 0777, true);
                        $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', basename($_FILES['profile_picture']['name']));
                        $targetPath = $uploadDir . $fileName;
                        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
                            $dbPath = 'uploads/profile_pics/' . $fileName;
                        }
                    }

                    // Insert user
                    $userData = [
                        'username' => $username,
                        'password' => $hashed_password,
                        'full_name' => $full_name,
                        'office_station' => $office_station,
                        'position' => $position,
                        'rating_period' => $rating_period,
                        'area_of_specialization' => $area_of_specialization,
                        'age' => $age,
                        'sex' => $sex,
                        'profile_picture' => $dbPath,
                        'role' => $role,
                        'created_by' => $_SESSION['user_id'],
                        'is_active' => 1
                    ];

                    if ($this->userRepo->createUser($userData)) {
                        $_SESSION['toast'] = ['title' => 'Account Created', 'message' => "Account for $username has been created successfully!", 'type' => 'success'];
                        $this->logRepo->logAction($_SESSION['user_id'], 'Created User (Super Admin)', "Created new $role: $username");
                        $this->redirect('admin/register-user');
                    } else {
                        $_SESSION['toast'] = ['title' => 'Creation Failed', 'message' => 'Something went wrong. Please try again.', 'type' => 'error'];
                    }
                }
            }
        }

        // Fetch Offices for Dropdown
        try {
            $stmt_offices = $this->pdo->query("SELECT category, name, id FROM offices ORDER BY category, name");
            $offices_list = $stmt_offices->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $offices_list = [];
        }

        $this->view('admin/register_user', [
            'offices_list' => $offices_list,
            'pdo' => $this->pdo,
            'user' => $this->userRepo->getUserById($_SESSION['user_id']),
            'notifRepo' => $this->notifRepo
        ]);
    }

    public function submissions()
    {
        // Check if user is logged in and is admin
        if (!in_array($_SESSION['role'], ['admin', 'super_admin', 'immediate_head', 'head_hr'])) {
            $this->redirect('admin/dashboard');
        }

        // Fetch all users for filtering
        $all_users = $this->userRepo->getAllUsers(['admin']);

        // Handle Filtering
        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'status_filter' => trim($_GET['status'] ?? ''),
            'start_date' => trim($_GET['start_date'] ?? ''),
            'end_date' => trim($_GET['end_date'] ?? ''),
            'user_id' => isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0
        ];

        // Special divisional keyword handling (OSDS, CID, SGOD)
        if ($filters['search']) {
            $search_upper = strtoupper($filters['search']);
            if (in_array($search_upper, ['OSDS', 'CID', 'SGOD'])) {
                $filters['office_division'] = $search_upper;
                $filters['search'] = '';
            }
        }

        $activities = $this->activityRepo->getAllActivities($filters);

        // Status labels for display
        $statuses = [
            'Pending' => 'Pending',
            'Viewed' => 'Viewed',
            'Reviewed' => 'Reviewed',
            'Recommended' => 'Recommended',
            'Approved' => 'Approved'
        ];

        $this->view('admin/submissions', [
            'all_users' => $all_users,
            'filters' => $filters,
            'activities' => $activities,
            'statuses' => $statuses,
            'pdo' => $this->pdo,
            'user' => $this->userRepo->getUserById($_SESSION['user_id']),
            'notifRepo' => $this->notifRepo
        ]);
    }

    public function viewActivity()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$id) {
            $this->redirect('admin/submissions');
        }

        $activity = $this->activityRepo->getActivityById($id);
        if (!$activity) {
            $this->redirect('admin/submissions');
        }

        // Handle Approval Actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_approval'])) {
            $stage = $_POST['stage'];
            $now = date('Y-m-d H:i:s');
            $success = false;
            $actionDesc = "";

            if ($stage === 'supervisor') {
                $success = $this->activityRepo->updateApprovalStatus($id, 'supervisor', $now);
                $actionDesc = "Reviewed Activity Submission";
            } elseif ($stage === 'asds') {
                $conductedBy = $_POST['conducted_by'] ?? '';
                $signaturePath = saveAdminSignature('organizer_signature_data', 'organizer_sig', 'organizer_sig_file');

                if ($signaturePath && !empty($conductedBy)) {
                    $success = $this->activityRepo->updateApprovalStatus($id, 'asds', $now, [
                        'conducted_by' => $conductedBy,
                        'organizer_signature_path' => $signaturePath
                    ]);
                    $actionDesc = "Recommended Activity Submission";
                }
            } elseif ($stage === 'sds') {
                $approvedBy = $_POST['approved_by'] ?? '';
                $signaturePath = saveAdminSignature('signature_data', 'admin_sds');
                if ($signaturePath) {
                    $success = $this->activityRepo->updateApprovalStatus($id, 'sds', $now, [
                        'approved_by' => $approvedBy,
                        'signature_path' => $signaturePath
                    ]);
                    $actionDesc = "SDS Final Approval Given";
                }
            }

            if ($success) {
                $this->logRepo->logAction($_SESSION['user_id'], $actionDesc, $activity['title']);
                $_SESSION['toast'] = ['title' => 'Success', 'message' => 'Status updated successfully.', 'type' => 'success'];
            } else {
                $_SESSION['toast'] = ['title' => 'Error', 'message' => 'Failed to update status.', 'type' => 'error'];
            }
            $this->redirect("admin/view-activity?id=" . $id);
        }

        // Auto-update to 'Viewed' if it's Pending
        if (in_array($_SESSION['role'], ['admin', 'super_admin', 'immediate_head']) && $activity['status'] === 'Pending') {
            $this->activityRepo->updateStatus($id, 'Viewed');
            $activity['status'] = 'Viewed';
        }

        $this->logRepo->logAction($_SESSION['user_id'], 'Viewed Specific Activity', $activity['title']);

        $this->view('admin/view_activity', [
            'activity' => $activity,
            'pdo' => $this->pdo,
            'user' => $this->userRepo->getUserById($_SESSION['user_id']),
            'notifRepo' => $this->notifRepo
        ]);
    }

    public function editActivity()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$id) {
            $this->redirect('admin/submissions');
        }

        $activity = $this->activityRepo->getActivityById($id);
        if (!$activity) {
            $this->redirect('admin/submissions');
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $title = trim($_POST['title']);
            $date_attended = trim($_POST['date_attended'] ?? '');
            $venue = trim($_POST['venue']);
            $modality = isset($_POST['modality']) ? implode(', ', $_POST['modality']) : '';
            $competency = isset($_POST['competency']) ? (is_array($_POST['competency']) ? implode(', ', $_POST['competency']) : trim($_POST['competency'])) : '';
            $type_ld = isset($_POST['type_ld']) ? implode(', ', $_POST['type_ld']) : '';
            $type_ld_others = trim($_POST['type_ld_others'] ?? '');
            $conducted_by = trim($_POST['conducted_by']);
            $reflection = trim($_POST['reflection']);

            $new_work_images = saveUpload('workplace_image', 'work', 'workplace');
            $work_image_path = $new_work_images ?: $activity['workplace_image_path'];

            $updateData = [
                'title' => $title,
                'date_attended' => $date_attended,
                'venue' => $venue,
                'modality' => $modality,
                'competency' => $competency,
                'type_ld' => $type_ld,
                'type_ld_others' => $type_ld_others,
                'conducted_by' => $conducted_by,
                'workplace_image_path' => $work_image_path,
                'reflection' => $reflection,
                'rating_period' => $activity['rating_period']
            ];

            if ($this->activityRepo->updateActivity($id, null, $updateData)) {
                $this->logRepo->logAction($_SESSION['user_id'], 'Updated Activity', "Activity ID: $id (Admin Edit)");
                $_SESSION['toast'] = ['title' => 'Success', 'message' => 'Activity updated successfully!', 'type' => 'success'];
                $this->redirect("admin/view-activity?id=" . $id);
            } else {
                $_SESSION['toast'] = ['title' => 'Error', 'message' => 'Error updating activity.', 'type' => 'error'];
            }
        }

        $this->view('admin/edit_activity', [
            'activity' => $activity,
            'ildnRepo' => $this->ildnRepo,
            'pdo' => $this->pdo,
            'user' => $this->userRepo->getUserById($_SESSION['user_id']),
            'notifRepo' => $this->notifRepo
        ]);
    }
}
