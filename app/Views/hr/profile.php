<?php
// Extracted variables from $data (handled by Controller::view)
// $user, $all_ildns, $certificates, $notifRepo, $pdo
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Profile - LDP</title>
    <?php require BASE_PATH . 'includes/admin_head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/admin/profile.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="app-layout">
        <?php require BASE_PATH . 'includes/sidebar.php'; ?>

        <div class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <div class="breadcrumb">
                        <h1 class="page-title">My Profile</h1>
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
                <div class="admin-profile-container">
                    <div class="dashboard-card hover-elevate">
                        <div class="card-header">
                            <h2><i class="bi bi-person-vcard text-gradient"></i> Core Identification</h2>
                        </div>
                        <div class="card-body admin-profile-card-body">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <input type="hidden" name="update_profile_hr" value="1">

                                <div class="filter-group profile-input-container">
                                    <label>Primary Full Name</label>
                                    <div class="profile-input-wrapper">
                                        <i class="bi bi-person profile-input-icon"></i>
                                        <input type="text" name="full_name" class="form-control profile-input-field"
                                            required value="<?php echo htmlspecialchars($user['full_name']); ?>">
                                    </div>
                                </div>

                                <div class="profile-input-row">
                                    <div class="filter-group">
                                        <label>Current Office / Assignment</label>
                                        <div class="profile-input-container">
                                            <i class="bi bi-building profile-input-icon" style="z-index: 10;"></i>
                                            <select name="office_station" id="office_select"
                                                class="form-control profile-input-field" required>
                                                <option value="">Select your office...</option>
                                                <optgroup label="OSDS">
                                                    <option value="ADMINISTRATIVE (PERSONEL)" <?php echo ($user['office_station'] == 'ADMINISTRATIVE (PERSONEL)') ? 'selected' : ''; ?>>ADMINISTRATIVE (PERSONEL)</option>
                                                    <option value="ADMINISTRATIVE (PROPERTY AND SUPPLY)" <?php echo ($user['office_station'] == 'ADMINISTRATIVE (PROPERTY AND SUPPLY)') ? 'selected' : ''; ?>>ADMINISTRATIVE (PROPERTY AND
                                                        SUPPLY)</option>
                                                    <option value="ADMINISTRATIVE (RECORDS)" <?php echo ($user['office_station'] == 'ADMINISTRATIVE (RECORDS)') ? 'selected' : ''; ?>>ADMINISTRATIVE (RECORDS)</option>
                                                    <option value="ADMINISTRATIVE (CASH)" <?php echo ($user['office_station'] == 'ADMINISTRATIVE (CASH)') ? 'selected' : ''; ?>>ADMINISTRATIVE (CASH)</option>
                                                    <option value="ADMINISTRATIVE (GENERAL SERVICES)" <?php echo ($user['office_station'] == 'ADMINISTRATIVE (GENERAL SERVICES)') ? 'selected' : ''; ?>>ADMINISTRATIVE (GENERAL SERVICES)</option>
                                                    <option value="FINANCE (ACCOUNTING)" <?php echo ($user['office_station'] == 'FINANCE (ACCOUNTING)') ? 'selected' : ''; ?>>FINANCE (ACCOUNTING)</option>
                                                    <option value="FINANCE (BUDGET)" <?php echo ($user['office_station'] == 'FINANCE (BUDGET)') ? 'selected' : ''; ?>>FINANCE (BUDGET)</option>
                                                    <option value="LEGAL" <?php echo ($user['office_station'] == 'LEGAL') ? 'selected' : ''; ?>>LEGAL</option>
                                                    <option value="ICT" <?php echo ($user['office_station'] == 'ICT') ? 'selected' : ''; ?>>ICT</option>
                                                </optgroup>
                                                <optgroup label="SGOD">
                                                    <option value="SCHOOL MANAGEMENT MONITORING & EVALUATION" <?php echo ($user['office_station'] == 'SCHOOL MANAGEMENT MONITORING & EVALUATION') ? 'selected' : ''; ?>>SCHOOL MANAGEMENT MONITORING
                                                        & EVALUATION</option>
                                                    <option value="HUMAN RESOURCES DEVELOPMENT" <?php echo ($user['office_station'] == 'HUMAN RESOURCES DEVELOPMENT') ? 'selected' : ''; ?>>HUMAN RESOURCES DEVELOPMENT</option>
                                                    <option value="DISASTER RISK REDUCTION AND MANAGEMENT" <?php echo ($user['office_station'] == 'DISASTER RISK REDUCTION AND MANAGEMENT') ? 'selected' : ''; ?>>DISASTER RISK REDUCTION AND
                                                        MANAGEMENT</option>
                                                    <option value="EDUCATION FACILITIES" <?php echo ($user['office_station'] == 'EDUCATION FACILITIES') ? 'selected' : ''; ?>>EDUCATION FACILITIES</option>
                                                    <option value="SCHOOL HEALTH AND NUTRITION" <?php echo ($user['office_station'] == 'SCHOOL HEALTH AND NUTRITION') ? 'selected' : ''; ?>>SCHOOL HEALTH AND NUTRITION</option>
                                                    <option value="SCHOOL HEALTH AND NUTRITION (DENTAL)" <?php echo ($user['office_station'] == 'SCHOOL HEALTH AND NUTRITION (DENTAL)') ? 'selected' : ''; ?>>SCHOOL HEALTH AND NUTRITION
                                                        (DENTAL)</option>
                                                    <option value="SCHOOL HEALTH AND NUTRITION (MEDICAL)" <?php echo ($user['office_station'] == 'SCHOOL HEALTH AND NUTRITION (MEDICAL)') ? 'selected' : ''; ?>>SCHOOL HEALTH AND NUTRITION
                                                        (MEDICAL)</option>
                                                </optgroup>
                                                <optgroup label="CID">
                                                    <option value="CURRICULUM IMPLEMENTATION DIVISION" <?php echo ($user['office_station'] == 'CURRICULUM IMPLEMENTATION DIVISION') ? 'selected' : ''; ?>>CURRICULUM IMPLEMENTATION DIVISION
                                                    </option>
                                                    <option
                                                        value="CURRICULUM IMPLEMENTATION DIVISION (INSTRUCTIONAL MANAGEMENT)"
                                                        <?php echo ($user['office_station'] == 'CURRICULUM IMPLEMENTATION DIVISION (INSTRUCTIONAL MANAGEMENT)') ? 'selected' : ''; ?>>
                                                        CURRICULUM IMPLEMENTATION DIVISION (INSTRUCTIONAL MANAGEMENT)
                                                    </option>
                                                    <option
                                                        value="CURRICULUM IMPLEMENTATION DIVISION (LEARNING RESOURCES MANAGEMENT)"
                                                        <?php echo ($user['office_station'] == 'CURRICULUM IMPLEMENTATION DIVISION (LEARNING RESOURCES MANAGEMENT)') ? 'selected' : ''; ?>>CURRICULUM IMPLEMENTATION DIVISION (LEARNING RESOURCES
                                                        MANAGEMENT)</option>
                                                    <option
                                                        value="CURRICULUM IMPLEMENTATION DIVISION (ALTERNATIVE LEARNING SYSTEM)"
                                                        <?php echo ($user['office_station'] == 'CURRICULUM IMPLEMENTATION DIVISION (ALTERNATIVE LEARNING SYSTEM)') ? 'selected' : ''; ?>>
                                                        CURRICULUM IMPLEMENTATION DIVISION (ALTERNATIVE LEARNING SYSTEM)
                                                    </option>
                                                    <option
                                                        value="CURRICULUM IMPLEMENTATION DIVISION (DISTRICT INSTRUCTIONAL SUPERVISION)"
                                                        <?php echo ($user['office_station'] == 'CURRICULUM IMPLEMENTATION DIVISION (DISTRICT INSTRUCTIONAL SUPERVISION)') ? 'selected' : ''; ?>>CURRICULUM IMPLEMENTATION DIVISION (DISTRICT
                                                        INSTRUCTIONAL SUPERVISION)</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="filter-group">
                                        <label>Official Position</label>
                                        <div class="profile-input-container">
                                            <i class="bi bi-briefcase profile-input-icon"></i>
                                            <input type="text" name="position" class="form-control profile-input-field"
                                                value="<?php echo htmlspecialchars($user['position']); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="filter-group profile-input-container" style="margin-bottom: 35px;">
                                    <label>Security Override (Leave blank to keep password)</label>
                                    <div class="profile-input-container mb-0">
                                        <i class="bi bi-shield-lock profile-input-icon"></i>
                                        <input type="password" name="password" class="form-control profile-input-field"
                                            placeholder="••••••••">
                                    </div>
                                </div>

                                <div class="profile-action-footer">
                                    <a href="<?php echo PUBLIC_ROOT; ?>index.php/hr/dashboard"
                                        class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Return
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-cloud-arrow-up"></i> Synchronize Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        // Tom Select for office dropdown
        new TomSelect('#office_select', {
            create: false,
            sortField: { field: 'text', direction: 'asc' }
        });

        // Profile picture preview
        function previewProfilePic(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById('profilePicPreview');
                    const placeholder = document.getElementById('profilePicPlaceholder');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if (placeholder) placeholder.style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Real-time clock
        function updateClock() {
            const now = new Date();
            const hours = now.getHours() % 12 || 12;
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const ampm = now.getHours() >= 12 ? 'PM' : 'AM';
            document.getElementById('real-time-clock').textContent = `${hours}:${minutes}:${seconds} ${ampm}`;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>

</html>