<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserRepository;
use App\Models\ActivityRepository;
use App\Models\ILDNRepository;
use App\Models\NotificationRepository;

use App\Models\ReferenceRepository;

class UserController extends Controller
{
    private $userRepo;
    private $activityRepo;
    private $ildnRepo;
    private $notifRepo;
    private $refRepo;
    private $pdo;

    public function __construct()
    {
        $this->pdo = $this->getDB();
        $this->userRepo = new UserRepository($this->pdo);
        $this->activityRepo = new ActivityRepository($this->pdo);
        $this->ildnRepo = new ILDNRepository($this->pdo);
        $this->notifRepo = new NotificationRepository($this->pdo);
        $this->refRepo = new ReferenceRepository($this->pdo);

        if (!isset($_SESSION['user_id'])) {
            $this->redirect('');
        }
    }
    // ... (home and profile methods unchanged)


    public function profile()
    {
        $user_id = $_SESSION['user_id'];
        $user = $this->userRepo->getUserById($user_id);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            // 1. Update Profile Information
            if (isset($_POST['update_profile'])) {
                $updateData = [
                    'full_name' => trim($_POST['full_name']),
                    'position' => trim($_POST['position']),
                    'office_station' => trim($_POST['office_station']),
                    'rating_period' => trim($_POST['rating_period']),
                    'area_of_specialization' => trim($_POST['area_of_specialization']),
                    'age' => (int) $_POST['age'],
                    'sex' => trim($_POST['sex'])
                ];

                if (!empty($_POST['password'])) {
                    $updateData['password'] = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
                }

                // Handle Profile Picture Upload
                $profile_pic_path = $this->saveUpload('profile_picture', 'avatars', 'avatar_' . $user_id);
                if ($profile_pic_path) {
                    $updateData['profile_picture'] = $profile_pic_path;
                }

                if ($this->userRepo->updateUser($user_id, $updateData)) {
                    $_SESSION['toast'] = ['title' => 'Success', 'message' => 'Profile updated successfully!', 'type' => 'success'];
                }
                $this->redirect('user/profile');
            }

            // 2. Add ILDN
            if (isset($_POST['add_ildn'])) {
                $need_text = trim($_POST['need_text']);
                $description = trim($_POST['description']);
                if ($this->ildnRepo->createILDN($user_id, $need_text, $description)) {
                    $_SESSION['toast'] = ['title' => 'Success', 'message' => 'Development need added!', 'type' => 'success'];
                }
                $this->redirect('user/profile');
            }

            // 3. Delete ILDN
            if (isset($_POST['delete_ildn'])) {
                $ildn_id = (int) $_POST['ildn_id'];
                if ($this->ildnRepo->deleteILDN($ildn_id, $user_id)) {
                    $_SESSION['toast'] = ['title' => 'Success', 'message' => 'Development need removed.', 'type' => 'success'];
                }
                $this->redirect('user/profile');
            }
        }

        $user_ildns = $this->ildnRepo->getILDNsByUser($user_id);
        $notifications = $this->notifRepo->getUnreadNotifications($user_id);

        // Fetch Certificates (from activities with certificate_path)
        $all_activities = $this->activityRepo->getActivitiesByUser($user_id, []);
        $certificates = [];
        foreach ($all_activities as $act) {
            if (!empty($act['certificate_path'])) {
                $certificates[] = $act;
            }
        }

        $this->view('user/profile', [
            'user' => $user,
            'user_ildns' => $user_ildns,
            'notifications' => $notifications,
            'certificates' => $certificates,
            'activities' => $all_activities
        ]);
    }

    public function home()
    {
        $user_id = $_SESSION['user_id'];

        // Handle Notification Actions (AJAX)
        if (isset($_GET['action']) && $_GET['action'] == 'read_notif' && isset($_GET['notif_id'])) {
            $success = $this->notifRepo->markAsRead($_GET['notif_id'], $user_id);
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool) $success]);
            exit;
        }

        $user = $this->userRepo->getUserById($user_id);

        // Activity Stats
        $all_activities = $this->activityRepo->getActivitiesByUser($user_id, []);
        $total_count = count($all_activities);
        $approved_count = 0;

        foreach ($all_activities as $act) {
            if (!empty($act['approved_sds'])) {
                $approved_count++;
            }
        }

        // Unaddressed Needs
        $all_ildns = $this->ildnRepo->getILDNsByUser($user_id);
        $unaddressed_needs = array_filter($all_ildns, function ($ildn) {
            return $ildn['usage_count'] == 0;
        });

        // Notifications
        $notifications = $this->notifRepo->getUnreadNotifications($user_id);

        // Progress Calculation
        $total_needs = count($all_ildns);
        $addressed_needs = $total_needs - count($unaddressed_needs);

        if ($total_needs > 0) {
            $progress_pct = round(($addressed_needs / $total_needs) * 100);
        } else {
            $progress_pct = 0;
        }

        $this->view('user/home', [
            'user' => $user,
            'activities' => array_slice($all_activities, 0, 5), // Recent 5
            'total_count' => $total_count,
            'approved_count' => $approved_count,
            'progress_pct' => $progress_pct,
            'unaddressed_needs' => $unaddressed_needs,
            'notifications' => $notifications
        ]);
    }

    public function addActivity()
    {
        $user_id = $_SESSION['user_id'];
        $user = $this->userRepo->getUserById($user_id);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $current_rating_period = $user['rating_period'] ?? 'Not Set';

            $modality = trim($_POST['modality'] ?? '');
            $competency = isset($_POST['competency']) ? (is_array($_POST['competency']) ? implode(', ', $_POST['competency']) : trim($_POST['competency'])) : '';
            $type_ld = trim($_POST['type_ld'] ?? '');
            $classification = isset($_POST['classification']) ? (is_array($_POST['classification']) ? implode(', ', $_POST['classification']) : trim($_POST['classification'])) : '';

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
                'classification' => $classification,
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
                $this->redirect('user/submissions-progress');
            }
        }

        $user_ildns = $this->ildnRepo->getILDNList($user_id);
        $ld_types = $this->refRepo->getAllLDTypes();
        $modalities = $this->refRepo->getAllModalities();
        $classifications = $this->refRepo->getAllClassifications();

        $this->view('user/add_activity', [
            'user' => $user,
            'user_ildns' => $user_ildns,
            'ld_types' => $ld_types,
            'modalities' => $modalities,
            'classifications' => $classifications
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
            $modality = trim($_POST['modality'] ?? '');
            $competency = isset($_POST['competency']) ? (is_array($_POST['competency']) ? implode(', ', $_POST['competency']) : trim($_POST['competency'])) : '';
            $type_ld = trim($_POST['type_ld'] ?? '');
            $classification = isset($_POST['classification']) ? (is_array($_POST['classification']) ? implode(', ', $_POST['classification']) : trim($_POST['classification'])) : '';

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
                'classification' => $classification,
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
        $ld_types = $this->refRepo->getAllLDTypes();
        $modalities = $this->refRepo->getAllModalities();
        $classifications = $this->refRepo->getAllClassifications();

        $this->view('user/edit_activity', [
            'activity' => $activity,
            'user_ildns' => $user_ildns,
            'ld_types' => $ld_types,
            'modalities' => $modalities,
            'classifications' => $classifications
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

        // Fetch Immediate Head Name (SDS)
        $sdsName = 'SDS';
        try {
            $stmt = $this->pdo->prepare("SELECT full_name FROM users WHERE role = 'immediate_head' LIMIT 1");
            $stmt->execute();
            $sdsUser = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($sdsUser && !empty($sdsUser['full_name'])) {
                $sdsName = $sdsUser['full_name'];
            }
        } catch (\Exception $e) {
        }

        // Fetch Head HR Name
        $hrName = 'HR OFFICER';
        try {
            $stmt = $this->pdo->prepare("SELECT full_name FROM users WHERE role = 'head_hr' LIMIT 1");
            $stmt->execute();
            $hrUser = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($hrUser && !empty($hrUser['full_name'])) {
                $hrName = $hrUser['full_name'];
            }
        } catch (\Exception $e) {
        }

        $this->view('user/view_activity', [
            'activity' => $activity,
            'sds_name' => $sdsName,
            'hr_name' => $hrName
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

        // Fetch user data for sidebar display
        $user = $this->userRepo->getUserById($user_id);
        $activities = $this->activityRepo->getActivitiesByUser($user_id, $filters);

        $this->view('user/submissions_progress', [
            'user' => $user,
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
