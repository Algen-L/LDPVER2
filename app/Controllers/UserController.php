<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserRepository;
use App\Models\ActivityRepository;
use App\Models\ILDNRepository;
use App\Models\NotificationRepository;

class UserController extends Controller
{
    private $userRepo;
    private $activityRepo;
    private $ildnRepo;
    private $notifRepo;
    private $pdo;

    public function __construct()
    {
        $this->pdo = $this->getDB();
        $this->userRepo = new UserRepository($this->pdo);
        $this->activityRepo = new ActivityRepository($this->pdo);
        $this->ildnRepo = new ILDNRepository($this->pdo);
        $this->notifRepo = new NotificationRepository($this->pdo);

        if (!isset($_SESSION['user_id'])) {
            $this->redirect('');
        }
    }

    public function home()
    {
        $user_id = $_SESSION['user_id'];
        $user = $this->userRepo->getUserById($user_id);

        if (!$user) {
            session_destroy();
            $this->redirect('');
        }

        // AJAX handler for marking as read
        if (isset($_GET['action']) && $_GET['action'] === 'read_notif' && isset($_GET['notif_id'])) {
            $notif_id = (int) $_GET['notif_id'];
            if ($this->notifRepo->markAsRead($notif_id, $user_id)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false]);
            }
            exit;
        }

        $activities = $this->activityRepo->getActivitiesByUser($user_id, ['limit' => 10]);
        $stats = $this->activityRepo->getUserStats($user_id);
        $all_ildns = $this->ildnRepo->getILDNsByUser($user_id);
        $notifications = $this->notifRepo->getUnreadNotifications($user_id);

        $total_count = $stats['total'];
        $approved_count = $stats['approved'] ?: 0;
        $progress_pct = $total_count > 0 ? round(($approved_count / $total_count) * 100) : 0;

        $unaddressed_needs = array_filter($all_ildns, function ($item) {
            return $item['usage_count'] == 0;
        });

        $this->view('user/home', [
            'user' => $user,
            'activities' => $activities,
            'stats' => $stats,
            'total_count' => $total_count,
            'approved_count' => $approved_count,
            'progress_pct' => $progress_pct,
            'unaddressed_needs' => $unaddressed_needs,
            'notifications' => $notifications,
            'pdo' => $this->pdo,
            'notifRepo' => $this->notifRepo
        ]);
    }

    public function profile()
    {
        $user_id = $_SESSION['user_id'];
        $user = $this->userRepo->getUserById($user_id);

        if (!$user) {
            session_destroy();
            $this->redirect('');
        }

        // Handle Profile Update
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
            $updateData = [
                'full_name' => trim($_POST['full_name']),
                'office_station' => trim($_POST['office_station']),
                'position' => trim($_POST['position']),
                'rating_period' => trim($_POST['rating_period']),
                'area_of_specialization' => trim($_POST['area_of_specialization']),
                'age' => (int) $_POST['age'],
                'sex' => trim($_POST['sex'])
            ];

            if (!empty($_POST['password'])) {
                $updateData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/profile_pics/';
                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0777, true);
                $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', basename($_FILES['profile_picture']['name']));
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadDir . $fileName)) {
                    $updateData['profile_picture'] = 'uploads/profile_pics/' . $fileName;
                }
            }

            if ($this->userRepo->updateUserProfile($user_id, $updateData)) {
                $_SESSION['toast'] = ['title' => 'Profile Updated', 'message' => 'Your profile has been successfully updated.', 'type' => 'success'];
                $_SESSION['full_name'] = $updateData['full_name'];
                if (isset($updateData['profile_picture']))
                    $_SESSION['profile_picture'] = $updateData['profile_picture'];
                $this->redirect('user/profile');
            }
        }

        // Handle Certificate Upload
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_certificate'])) {
            $activity_id = (int) $_POST['activity_id'];
            if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/certificates/';
                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0777, true);
                $fileExt = strtolower(pathinfo($_FILES['certificate']['name'], PATHINFO_EXTENSION));
                $fileName = uniqid() . '_cert_' . $activity_id . '.' . $fileExt;
                if (move_uploaded_file($_FILES['certificate']['tmp_name'], $uploadDir . $fileName)) {
                    $dbPath = 'uploads/certificates/' . $fileName;
                    $this->activityRepo->updateActivity($activity_id, $user_id, ['certificate_path' => $dbPath]);
                    $_SESSION['toast'] = ['title' => 'Success', 'message' => 'Certificate uploaded successfully!', 'type' => 'success'];
                    $this->redirect('user/profile');
                }
            }
        }

        // Handle ILDN Management
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_ildn'])) {
            $this->ildnRepo->createILDN($user_id, trim($_POST['need_text']), trim($_POST['description'] ?? ''));
            $this->redirect('user/profile');
        }

        if (isset($_GET['delete_ildn'])) {
            $this->ildnRepo->deleteILDN((int) $_GET['delete_ildn'], $user_id);
            $this->redirect('user/profile');
        }

        // Handle Clear Notifications
        if (isset($_GET['clear_notifications'])) {
            $this->notifRepo->deleteAllNotifications($user_id);
            $_SESSION['toast'] = ['title' => 'Success', 'message' => 'Message log cleared.', 'type' => 'success'];
            $this->redirect('user/profile');
        }

        $activities = $this->activityRepo->getActivitiesByUser($user_id);
        $user_ildns = $this->ildnRepo->getILDNsByUser($user_id);
        // Fetch ALL notifications for the log, not just unread
        $notifications = $this->notifRepo->getAllUserNotifications($user_id);

        $this->view('user/profile', [
            'user' => $user,
            'activities' => $activities,
            'user_ildns' => $user_ildns,
            'notifications' => $notifications,
            'pdo' => $this->pdo,
            'notifRepo' => $this->notifRepo
        ]);
    }

    public function addActivity()
    {
        $user_id = $_SESSION['user_id'];
        $user = $this->userRepo->getUserById($user_id);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $current_rating_period = $user['rating_period'] ?? 'Not Set';

            $modality = isset($_POST['modality']) ? implode(', ', $_POST['modality']) : '';
            $competency = isset($_POST['competency']) ? (is_array($_POST['competency']) ? implode(', ', $_POST['competency']) : trim($_POST['competency'])) : '';
            $type_ld = isset($_POST['type_ld']) ? implode(', ', $_POST['type_ld']) : '';

            $workplace_image_path = $this->saveUpload('workplace_image', 'work', 'workplace');
            $application_file_path = $this->saveUpload('application_file', 'app_learning', 'application_files');
            $certificate_path = $this->saveUpload('certificate_image', 'cert', 'certificates');

            $activityData = [
                'user_id' => $user_id,
                'title' => trim($_POST['title']),
                'date_attended' => trim($_POST['date_attended']),
                'venue' => trim($_POST['venue']),
                'modality' => $modality,
                'competency' => $competency,
                'type_ld' => $type_ld,
                'type_ld_others' => trim($_POST['type_ld_others'] ?? ''),
                'conducted_by' => '',
                'organizer_signature_path' => '',
                'workplace_application' => '',
                'workplace_image_path' => $workplace_image_path,
                'certificate_path' => $certificate_path,
                'reflection' => trim($_POST['reflection']),
                'application_learning' => '',
                'application_file_path' => $application_file_path,
                'rating_period' => $current_rating_period
            ];

            if ($this->activityRepo->createActivity($activityData)) {
                $_SESSION['toast'] = ['title' => 'Success', 'message' => 'Activity submitted successfully!', 'type' => 'success'];
                $this->redirect('user/home');
            }
        }

        $user_ildns = $this->ildnRepo->getILDNList($user_id);
        $this->view('user/add_activity', [
            'user' => $user,
            'user_ildns' => $user_ildns
        ]);
    }

    public function editActivity()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $user_id = $_SESSION['user_id'];
        $activity_id = $id;
        $activity = $this->activityRepo->getActivityById($activity_id);

        if (!$activity) {
            $this->redirect('user/home');
        }

        // Access Control
        $allowed_admin_roles = ['admin', 'super_admin', 'head_hr', 'immediate_head'];
        $is_admin_edit = in_array($_SESSION['role'], $allowed_admin_roles);

        if (!$is_admin_edit && $activity['user_id'] != $user_id) {
            $_SESSION['toast'] = ['title' => 'Access Restricted', 'message' => 'You do not have permission to modify this activity.', 'type' => 'warning'];
            $this->redirect('user/home');
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $modality = isset($_POST['modality']) ? implode(', ', $_POST['modality']) : '';
            $competency = isset($_POST['competency']) ? (is_array($_POST['competency']) ? implode(', ', $_POST['competency']) : trim($_POST['competency'])) : '';
            $type_ld = isset($_POST['type_ld']) ? implode(', ', $_POST['type_ld']) : '';

            $new_work_images = $this->saveUpload('workplace_image', 'work', 'workplace');
            $work_image_path = $new_work_images ?: $activity['workplace_image_path'];

            $updateData = [
                'title' => trim($_POST['title']),
                'date_attended' => trim($_POST['date_attended']),
                'venue' => trim($_POST['venue']),
                'modality' => $modality,
                'competency' => $competency,
                'type_ld' => $type_ld,
                'type_ld_others' => trim($_POST['type_ld_others'] ?? ''),
                'conducted_by' => trim($_POST['conducted_by'] ?? ''),
                'workplace_image_path' => $work_image_path,
                'reflection' => trim($_POST['reflection']),
                'rating_period' => $activity['rating_period']
            ];

            $updateContextId = $is_admin_edit ? null : $user_id;

            if ($this->activityRepo->updateActivity($activity_id, $updateContextId, $updateData)) {
                $_SESSION['toast'] = ['title' => 'Success', 'message' => 'Activity updated successfully!', 'type' => 'success'];
                $this->redirect('user/view-activity/' . $activity_id);
            }
        }

        $user_ildns = $this->ildnRepo->getILDNList($activity['user_id']);
        $this->view('user/edit_activity', [
            'activity' => $activity,
            'user_ildns' => $user_ildns
        ]);
    }

    public function viewActivity()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $user_id = $_SESSION['user_id'];
        $activity_id = $id;
        $activity = $this->activityRepo->getActivityById($activity_id);

        if (!$activity) {
            $this->redirect('user/home');
        }

        $this->view('user/view_activity', [
            'activity' => $activity
        ]);
    }

    public function submissionsProgress()
    {
        $user_id = $_SESSION['user_id'];
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? ''
        ];

        $activities = $this->activityRepo->getActivitiesByUser($user_id, $filters);

        $this->view('user/submissions_progress', [
            'activities' => $activities,
            'filters' => $filters
        ]);
    }

    private function saveUpload($fieldName, $subDir, $customName = '')
    {
        if (isset($_FILES[$fieldName]) && !empty($_FILES[$fieldName]['name'])) {
            $files = $_FILES[$fieldName];
            $is_multiple = is_array($files['name']);
            $upload_paths = [];

            $names = $is_multiple ? $files['name'] : [$files['name']];
            $tmp_names = $is_multiple ? $files['tmp_name'] : [$files['tmp_name']];
            $errors = $is_multiple ? $files['error'] : [$files['error']];

            foreach ($names as $key => $name) {
                if ($errors[$key] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../../public/uploads/' . $subDir . '/';
                    if (!is_dir($uploadDir))
                        mkdir($uploadDir, 0777, true);

                    $fileExt = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                    // Security: Allow List Validation
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
                    if (!in_array($fileExt, $allowed)) {
                        // Skip invalid file types
                        continue;
                    }

                    $fileName = uniqid() . ($customName ? '_' . $customName : '') . '.' . $fileExt;

                    if (move_uploaded_file($tmp_names[$key], $uploadDir . $fileName)) {
                        $upload_paths[] = 'uploads/' . $subDir . '/' . $fileName;
                    }
                }
            }
            return $is_multiple ? implode(', ', $upload_paths) : ($upload_paths[0] ?? null);
        }
        return null;
    }
}
