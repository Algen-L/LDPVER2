<?php
// Extracted variables from $data (handled by Controller::view)
// $offices_list, $user, $notifRepo, $pdo
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Account - Admin</title>
    <?php require BASE_PATH . 'includes/admin_head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/admin/register_user.css?v=<?php echo time(); ?>">

</head>

<body>
    <div class="app-layout">
        <?php require BASE_PATH . 'includes/sidebar.php'; ?>

        <div class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <div class="breadcrumb">
                        <h1 class="page-title">Registration Panel</h1>
                    </div>
                </div>
                <div class="top-bar-right">
                    <div class="current-date-box">
                        <div class="time-section">
                            <span id="real-time-clock"><?php echo date('h:i:s A'); ?></span>
                        </div>
                        <div class="date-section">
                            <i class="bi bi-calendar3"></i>
                            <span><?php echo date('F j, Y'); ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <main class="content-wrapper">
                <form method="POST" action="" enctype="multipart/form-data" id="registerForm">
                    <div class="register-container">
                        <!-- Left Column: Account Access -->
                        <div class="register-card register-card-accent">
                            <div class="card-header-custom register-card-header-accent">
                                <h2><i class="bi bi-shield-lock-fill"></i> Account Access</h2>
                            </div>
                            <div class="card-body-custom">
                                <div class="mb-3 text-center">
                                    <div class="avatar-wrapper">
                                        <div class="profile-upload-zone"
                                            onclick="document.getElementById('profile_picture').click()">
                                            <i class="bi bi-camera-fill upload-icon"></i>
                                        </div>
                                        <img src="<?php echo PUBLIC_ROOT; ?>assets/human_avatar.png" id="preview-image"
                                            class="shadow-lg preview-img">
                                    </div>
                                    <input type="file" name="profile_picture" id="profile_picture" class="d-none"
                                        accept="image/*" onchange="previewFile()" style="display: none;">
                                    <p class="mt-2 upload-hint">Click to upload photo</p>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label label-accent">System Role</label>
                                    <select name="role" class="form-select select-accent" required>
                                        <option value="user">User (L&D Personnel)</option>
                                        <option value="hr">HR Personnel</option>
                                        <option value="immediate_head">Immediate Head (Approver)</option>
                                        <option value="admin">System Admin</option>
                                        <option value="head_hr">Head HR Personnel</option>
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label label-accent">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" name="username" class="form-control input-accent" required>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label label-accent">Gmail Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="gmail" class="form-control input-accent"
                                            placeholder="example@gmail.com" required>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label label-accent">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                                        <input type="password" name="password" class="form-control input-accent"
                                            required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg mt-1 btn-register">
                                    <i class="bi bi-person-plus-fill"></i> CREATE ACCOUNT
                                </button>
                            </div>
                        </div>

                        <!-- Right Column: Personnel Info -->
                        <div class="register-card">
                            <div class="card-header-custom">
                                <h2><i class="bi bi-person-lines-fill text-primary"></i> Personnel Information</h2>
                            </div>
                            <div class="card-body-custom">
                                <div class="form-section-header">Personal Details</div>

                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Full Name</label>
                                    <input type="text" name="full_name" class="form-control"
                                        placeholder="First Name M.I. Last Name" required>
                                </div>

                                <div class="form-grid-2">
                                    <div class="form-group">
                                        <label class="form-label">Office / Station</label>
                                        <select id="office-select" name="office_station" autocomplete="off"
                                            placeholder="Search office...">
                                            <option value="">Select Office/Station...</option>
                                            <?php if (!empty($offices_list)): ?>
                                                <?php foreach ($offices_list as $category => $items): ?>
                                                    <optgroup label="<?php echo htmlspecialchars($category); ?>">
                                                        <?php foreach ($items as $office): ?>
                                                            <option value="<?php echo htmlspecialchars($office['name']); ?>">
                                                                <?php echo htmlspecialchars($office['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Position / Designation</label>
                                        <input type="text" name="position" class="form-control"
                                            placeholder="e.g. Teacher I">
                                    </div>
                                </div>

                                <div class="form-section-header">Employment Details</div>

                                <div class="form-grid-2">
                                    <div class="form-group">
                                        <label class="form-label">Employee Number</label>
                                        <input type="text" name="employee_number" class="form-control"
                                            placeholder="e.g. 1234567">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Area of Specialization</label>
                                        <input type="text" name="area_of_specialization" class="form-control">
                                    </div>
                                </div>

                                <div class="form-grid-2" style="margin-bottom: 0;">
                                    <div class="form-group">
                                        <label class="form-label">Age</label>
                                        <input type="number" name="age" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Sex</label>
                                        <select name="sex" class="form-select">
                                            <option value="">Select...</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (document.getElementById('office-select')) {
                new TomSelect("#office-select", {
                    create: true,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
            }
        });

        function previewFile() {
            var preview = document.querySelector('#preview-image');
            var file = document.querySelector('#profile_picture').files[0];
            var reader = new FileReader();

            reader.onloadend = function () {
                preview.src = reader.result;
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = "<?php echo PUBLIC_ROOT; ?>assets/human_avatar.png";
            }
        }
    </script>
</body>

</html>