<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserRepository;
use App\Models\ActivityRepository;
use App\Models\ILDNRepository;
use App\Models\NotificationRepository;

class HRController extends Controller
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

        if ($_SESSION['role'] !== 'hr') {
            $this->redirect('');
        }
    }

    public function dashboard()
    {
        // HR users should use the admin dashboard like head_hr
        $this->redirect('admin/dashboard');
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
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile_hr'])) {
            $updateData = [
                'full_name' => trim($_POST['full_name']),
                'office_station' => trim($_POST['office_station']),
                'position' => trim($_POST['position'])
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
                $this->redirect('hr/profile');
            }
        }

        $this->view('hr/profile', [
            'user' => $user,
            'pdo' => $this->pdo,
            'notifRepo' => $this->notifRepo
        ]);
    }
}
