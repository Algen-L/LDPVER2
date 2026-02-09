<?php
// Extracted variables from $data (handled by Controller::view)
// $user, $all_users, $is_super_admin, $notifRepo, $pdo
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - LDP</title>
    <?php require BASE_PATH . 'includes/admin_head.php'; ?>
    <!-- Tom Select CSS -->
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
                        <h1 class="page-title"><?php echo $is_super_admin ? 'Admin Profile' : 'My Profile'; ?></h1>
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
                    <?php if (!$is_super_admin): ?>
                        <button id="toggleSettings" class="toggle-settings-btn">
                            <i class="bi bi-person-gear"></i> Account Information
                        </button>
                    <?php endif; ?>
                </div>
            </header>

            <main class="content-wrapper">
                <?php if ($is_super_admin): ?>
                    <div class="admin-profile-container">
                        <div class="dashboard-card hover-elevate">
                            <div class="card-header">
                                <h2><i class="bi bi-person-vcard text-gradient"></i> Core Identification</h2>
                            </div>
                            <div class="card-body admin-profile-card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="update_profile_admin" value="1">
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
                                                    <?php if ($user['office_station'] && !empty($user['office_station'])): ?>
                                                        <option value="<?php echo htmlspecialchars($user['office_station']); ?>"
                                                            selected><?php echo htmlspecialchars($user['office_station']); ?>
                                                            (Current)</option>
                                                    <?php endif; ?>
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
                                        <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/dashboard"
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

                        <div class="dashboard-card privilege-card">
                            <div class="card-header">
                                <h2><i class="bi bi-shield-check text-primary"></i> Administrative Privileges</h2>
                            </div>
                            <div class="card-body privilege-card-body">
                                <div class="d-flex align-items-center gap-20">
                                    <div class="privilege-icon-box">
                                        <i class="bi bi-key-fill"></i>
                                    </div>
                                    <div>
                                        <div class="privilege-role-level">
                                            Role Level: <?php echo strtoupper($_SESSION['role']); ?>
                                        </div>
                                        <div class="privilege-description">
                                            Your account is authorized with <strong>Higher Level</strong> administrative
                                            permissions. You can manage personnel records and audit system logs.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Notification Card (Super Admin) -->
                        <div class="dashboard-card notification-broadcast-card">
                            <div class="card-header">
                                <h2><i class="bi bi-megaphone text-warning"></i> Send System Notification</h2>
                            </div>
                            <div class="card-body notification-card-body">
                                <form method="POST" action="">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Recipient <span class="text-danger">*</span></label>
                                        <div style="position: relative;">
                                            <i class="bi bi-people"
                                                style="position: absolute; left: 14px; top: 18px; transform: translateY(-50%); color: var(--text-muted); z-index: 10;"></i>
                                            <select name="recipient_id" id="recipient_select_super" class="form-control"
                                                required style="padding-left: 42px;">
                                                <option value="">Search for a user...</option>
                                                <option value="all" style="font-weight: bold; color: var(--primary);">All
                                                    Users (Broadcast)</option>
                                                <?php foreach ($all_users as $u): ?>
                                                    <?php if ($u['id'] != $user['id']): ?>
                                                        <option value="<?php echo $u['id']; ?>">
                                                            <?php echo htmlspecialchars($u['full_name']); ?>
                                                            (<?php echo htmlspecialchars($u['office_station']); ?>)
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="form-label">Message <span class="text-danger">*</span></label>
                                        <textarea name="notif_message" class="form-control" rows="3"
                                            placeholder="Type your notification message here..." required
                                            style="min-height: 100px; padding: 15px; border-radius: 12px;"></textarea>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" name="send_notification"
                                            class="btn btn-primary notification-send-btn">
                                            <i class="bi bi-send"></i> Send Notification
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Redesign for Admin/Immediate Head -->
                    <div class="profile-container">
                        <!-- Hero Section -->
                        <div class="profile-hero">
                            <div class="hero-main">
                                <?php if (!empty($user['profile_picture'])): ?>
                                    <img src="<?php echo PUBLIC_ROOT . $user['profile_picture']; ?>" class="hero-avatar">
                                <?php else: ?>
                                    <div class="hero-avatar">
                                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="hero-info">
                                    <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                                    <p>
                                        <i class="bi bi-person-badge"></i>
                                        <?php echo htmlspecialchars($user['position'] ?: 'Administrative Professional'); ?>
                                        <span
                                            style="opacity: 0.5; margin: 0 4px; color: rgba(255,255,255,0.5) !important;">•</span>
                                        <i class="bi bi-building"></i>
                                        <?php echo htmlspecialchars($user['office_station']); ?>
                                    </p>
                                </div>
                            </div>

                            <?php if (empty($user['rating_period']) && $_SESSION['role'] === 'immediate_head'): ?>
                                <div class="rating-period-alert" id="ratingPeriodAlert">
                                    <div class="alert-content">
                                        <div class="alert-icon-box">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                        </div>
                                        <div class="alert-text">
                                            <strong>Rating Period Missing</strong>
                                            <p>Please set your current Rating Period.</p>
                                        </div>
                                    </div>
                                    <button type="button" class="alert-action-btn"
                                        onclick="document.getElementById('toggleSettings').click(); document.getElementById('accountSettings').scrollIntoView({behavior: 'smooth'});">
                                        FIX NOW
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Account Information (Hidden by default) -->
                        <div id="accountSettings" class="account-settings-card">
                            <div class="dashboard-card" style="margin-bottom: 24px; border: 1px solid #e2e8f0;">
                                <div class="card-header profile-settings-header">
                                    <h2 class="profile-settings-title"><i class="bi bi-shield-lock"></i> Account Settings
                                    </h2>
                                </div>
                                <div class="card-body profile-settings-body">
                                    <form method="POST" enctype="multipart/form-data">
                                        <?php if ($_SESSION['role'] === 'immediate_head'): ?>
                                            <input type="hidden" name="update_profile_user" value="1">
                                        <?php else: ?>
                                            <input type="hidden" name="update_profile_admin" value="1">
                                        <?php endif; ?>
                                        <div class="form-group mb-4">
                                            <label class="form-label avatar-edit-label">Personal Avatar</label>
                                            <div class="avatar-edit-container">
                                                <div id="avatarPreviewContainer" class="avatar-preview-box">
                                                    <?php if (!empty($user['profile_picture'])): ?>
                                                        <img src="<?php echo PUBLIC_ROOT . $user['profile_picture']; ?>"
                                                            id="currentAvatar" class="avatar-img">
                                                    <?php else: ?>
                                                        <div class="avatar-placeholder-text">
                                                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="mb-3">
                                                        <button type="button"
                                                            onclick="document.getElementById('profile_pic_input').click()"
                                                            class="btn btn-outline-primary photo-upload-trigger">
                                                            <i class="bi bi-camera"></i> Update Photo
                                                        </button>
                                                        <input type="file" name="profile_picture" id="profile_pic_input"
                                                            style="display: none;" accept="image/*"
                                                            onchange="updateFileName(this)">
                                                    </div>
                                                    <div id="fileNameDisplay" class="avatar-controls-text">
                                                        Recommended: Square image, max 2MB (JPG, PNG)
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <script>
                                            function updateFileName(input) {
                                                const display = document.getElementById('fileNameDisplay');
                                                if (input.files && input.files[0]) {
                                                    display.innerHTML = `<i class="bi bi-file-earmark-check"></i> Selected: <strong>${input.files[0].name}</strong>`;
                                                    display.style.color = "var(--primary)";
                                                }
                                            }
                                        </script>
                                        <div>
                                            <div class="form-group">
                                                <label class="form-label">Full Name</label>
                                                <input type="text" name="full_name" class="form-control" required
                                                    value="<?php echo htmlspecialchars($user['full_name']); ?>">
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label class="form-label">New Password (Leave blank to keep current)</label>
                                            <input type="password" name="password" class="form-control"
                                                placeholder="••••••••" autocomplete="new-password">
                                        </div>
                                        <div class="form-group mt-3">
                                            <label class="form-label" style="color: #dc2626; font-weight: 800;">
                                                <i class="bi bi-shield-lock-fill"></i> Security Passkey
                                            </label>
                                            <input type="text" name="passkey_input" class="form-control"
                                                placeholder="Enter 6-digit code" maxlength="6"
                                                style="border-color: #fca5a5;">
                                            <small style="color: #dc2626; font-style: italic; font-weight: 600;">
                                                * Required only for password changes. Use the code from your registration
                                                email.
                                            </small>
                                        </div>
                                        <div style="text-align: right; margin-top: 20px;">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- System Notification Card (Admin/Head) -->
                        <div class="dashboard-card notification-broadcast-card">
                            <div class="card-header">
                                <h2><i class="bi bi-megaphone text-warning"></i> Send System Notification</h2>
                            </div>
                            <div class="card-body notification-card-body">
                                <form method="POST" action="">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Recipient <span class="text-danger">*</span></label>
                                        <div style="position: relative;">
                                            <i class="bi bi-people"
                                                style="position: absolute; left: 14px; top: 18px; transform: translateY(-50%); color: var(--text-muted); z-index: 10;"></i>
                                            <select name="recipient_id" id="recipient_select_admin" class="form-control"
                                                required style="padding-left: 42px;">
                                                <option value="">Search for a user...</option>
                                                <option value="all" style="font-weight: bold; color: var(--primary);">All
                                                    Users (Broadcast)</option>
                                                <?php foreach ($all_users as $u): ?>
                                                    <?php if ($u['id'] != $user['id']): ?>
                                                        <option value="<?php echo $u['id']; ?>">
                                                            <?php echo htmlspecialchars($u['full_name']); ?>
                                                            (<?php echo htmlspecialchars($u['office_station']); ?>)
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="form-label">Message <span class="text-danger">*</span></label>
                                        <textarea name="notif_message" class="form-control" rows="3"
                                            placeholder="Type your notification message here..." required
                                            style="min-height: 100px; border-radius: 12px; padding: 15px;"></textarea>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" name="send_notification"
                                            class="btn btn-primary notification-send-btn">
                                            <i class="bi bi-send"></i> Send Notification
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                <?php endif; ?>
            </main>

            <footer class="admin-footer">
                <p>&copy; <?php echo date('Y'); ?> SDO L&D Passbook System. <span class="text-muted">Developed by Algen
                        D. Loveres and Cedrick V. Bacaresas</span></p>
            </footer>
        </div>
    </div>
    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php if ($is_super_admin): ?>
                const officeSelect = document.getElementById('office_select');
                if (officeSelect) {
                    new TomSelect('#office_select', {
                        create: false,
                        sortField: { field: "text", direction: "asc" },
                        placeholder: "Type to search office...",
                        maxOptions: 50
                    });
                }

                if (document.getElementById('recipient_select_super')) {
                    new TomSelect('#recipient_select_super', {
                        create: false,
                        sortField: [],
                        placeholder: "Search for a user...",
                        maxOptions: 100
                    });
                }
            <?php else: ?>
                if (document.getElementById('recipient_select_admin')) {
                    new TomSelect('#recipient_select_admin', {
                        create: false,
                        sortField: [],
                        placeholder: "Search for a user...",
                        maxOptions: 100
                    });
                }
            <?php endif; ?>

        // toggleBtn listener removed (handled by profile-actions.js)
        });
    </script>
    <script src="<?php echo PUBLIC_ROOT; ?>js/profile-actions.js?v=<?php echo time(); ?>"></script>
</body>

</html>