<?php
// Extracted variables from $data (handled by Controller::view)
// $view, $target_id, $filters, $office_categories, $users, $pending_users, $target_user, $audit_logs, $user, $notifRepo
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <?php require BASE_PATH . 'includes/admin_head.php'; ?>
    <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/admin/manage_users.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="app-layout">
        <?php require BASE_PATH . 'includes/sidebar.php'; ?>

        <div class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <div class="breadcrumb">
                        <h1 class="page-title">
                            Personnel Management
                            <span class="page-title-secondary">
                                <?php if ($view === 'details' && $target_user): ?>
                                    / Verification / <?php echo htmlspecialchars($target_user['full_name']); ?>
                                <?php endif; ?>
                            </span>
                        </h1>
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
                <div class="management-tabs">
                    <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/manage-users?view=active"
                        class="tab-item <?php echo $view === 'active' ? 'active' : ''; ?>">
                        <i class="bi bi-people-fill"></i> Active Personnel
                    </a>
                    <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/manage-users?view=verification"
                        class="tab-item <?php echo $view === 'verification' ? 'active' : ''; ?>">
                        <i class="bi bi-person-check-fill"></i> Pending Requests
                        <?php if (count($pending_users) > 0): ?>
                            <span class="tab-badge"><?php echo count($pending_users); ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/manage-users?view=notifications"
                        class="tab-item <?php echo $view === 'notifications' ? 'active' : ''; ?>">
                        <i class="bi bi-clock-history"></i> Profile Log
                    </a>
                </div>
                <div style="height: 1px;"></div>

                <?php if ($view === 'active'): ?>
                    <!-- Redesigned High-Fidelity Filter Bar -->
                    <div class="filter-bar filter-bar-card">
                        <form method="GET" class="filter-form filter-form-flex">
                            <input type="hidden" name="view" value="active">
                            <!-- Search Field with Icon -->
                            <div class="filter-item search-wrapper">
                                <i class="bi bi-search search-icon"></i>
                                <input type="text" name="search" value="<?php echo htmlspecialchars($filters['search']); ?>"
                                    placeholder="Search entries..." class="search-input">
                            </div>

                            <!-- Role Field -->
                            <div class="filter-item select-wrapper">
                                <select name="filter_role" class="select-input">
                                    <option value="">All Personnel</option>
                                    <option value="user" <?php echo $filters['role'] === 'user' ? 'selected' : ''; ?>>Staff
                                    </option>
                                    <option value="hr" <?php echo $filters['role'] === 'hr' ? 'selected' : ''; ?>>HR Personnel
                                    </option>
                                    <option value="immediate_head" <?php echo $filters['role'] === 'immediate_head' ? 'selected' : ''; ?>>Immediate Head</option>
                                    <option value="head_hr" <?php echo $filters['role'] === 'head_hr' ? 'selected' : ''; ?>>
                                        Head HR</option>
                                    <option value="admin" <?php echo $filters['role'] === 'admin' ? 'selected' : ''; ?>>System
                                        Admin</option>
                                    <option value="super_admin" <?php echo $filters['role'] === 'super_admin' ? 'selected' : ''; ?>>Super Admin</option>
                                </select>
                                <i class="bi bi-chevron-down select-chevron"></i>
                            </div>

                            <!-- Office Division -->
                            <div class="filter-item select-wrapper min-w-160">
                                <select name="filter_office" class="select-input">
                                    <option value="">All Divisions</option>
                                    <?php if (!empty($office_categories)): ?>
                                        <?php foreach ($office_categories as $cat): ?>
                                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($filters['office'] ?? '') === $cat ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <i class="bi bi-chevron-down select-chevron"></i>
                            </div>

                            <!-- Apply Button -->
                            <div class="filter-actions filter-actions-flex">
                                <button type="submit" class="btn-apply">
                                    <i class="bi bi-funnel-fill" style="font-size: 0.85rem;"></i> Apply
                                </button>
                                <?php if ($filters['search'] || $filters['role'] || $filters['office']): ?>
                                    <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/manage-users?view=active"
                                        class="btn-clear">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <div class="dashboard-card hover-elevate table-card">
                        <div class="card-body p-0">
                            <div class="table-responsive table-wrapper">
                                <table class="data-table">
                                    <thead class="table-thead-bg">
                                        <tr>
                                            <th class="table-th-sticky table-th-w-22">User</th>
                                            <th class="table-th-sticky table-th-w-12">Role</th>
                                            <th class="table-th-sticky table-th-w-15 cell-text-center">Unit</th>
                                            <th class="table-th-sticky table-th-w-10 cell-text-center">Status</th>
                                            <th class="table-th-sticky table-th-w-15">Last Login</th>
                                            <th class="table-th-sticky table-th-w-16 cell-actions-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody style="background: white;">
                                        <?php if (empty($users)): ?>
                                            <tr>
                                                <td colspan="6" class="cell-padding-20 text-center text-muted">No
                                                    staff records found match your criteria.</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php foreach ($users as $u):
                                            $initial = strtoupper(substr($u['full_name'], 0, 1));
                                            $role_class = 'badge-role-' . $u['role'];
                                            $role_label = ['super_admin' => 'Super Admin', 'admin' => 'Admin', 'head_hr' => 'Head HR', 'hr' => 'HR Personnel', 'immediate_head' => 'Head', 'user' => 'Staff'][$u['role']] ?? ucfirst($u['role']);
                                            ?>
                                            <tr class="table-row-border">
                                                <td class="cell-padding-20">
                                                    <div class="user-flex">
                                                        <?php if ($u['profile_picture']): ?>
                                                            <img src="<?php echo PUBLIC_ROOT . $u['profile_picture']; ?>"
                                                                class="user-avatar-img">
                                                        <?php else: ?>
                                                            <div class="user-avatar-placeholder"
                                                                style="width: 38px; height: 38px; font-size: 0.9rem; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                                                <?php echo $initial; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <div class="user-name-text">
                                                                <?php echo htmlspecialchars($u['full_name']); ?>
                                                            </div>
                                                            <div class="user-username-text">
                                                                @<?php echo htmlspecialchars($u['username']); ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span
                                                        class="badge <?php echo $role_class; ?>"><?php echo $role_label; ?></span>
                                                </td>
                                                <td class="cell-text-center">
                                                    <div class="office-text">
                                                        <?php echo $u['office_station'] ?: '-'; ?>
                                                    </div>
                                                </td>
                                                <td class="cell-text-center">
                                                    <span
                                                        class="status-check-badge <?php echo $u['is_active'] ? 'bg-success-light text-success' : 'bg-light text-muted'; ?>">
                                                        <?php echo $u['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="last-login-text">
                                                        <?php echo $u['last_login'] ? date('M d, Y', strtotime($u['last_login'])) : 'Never'; ?>
                                                    </div>
                                                </td>
                                                <td class="cell-actions-right">
                                                    <div class="actions-flex-end">
                                                        <?php
                                                        $can_manage_this = false;
                                                        if ($_SESSION['role'] === 'super_admin') {
                                                            $can_manage_this = true;
                                                        } elseif ($_SESSION['role'] === 'head_hr') {
                                                            if ($u['role'] !== 'super_admin' && $u['role'] !== 'head_hr')
                                                                $can_manage_this = true;
                                                        } elseif ($_SESSION['role'] === 'hr') {
                                                            if ($u['role'] === 'user')
                                                                $can_manage_this = true;
                                                        }
                                                        if ($can_manage_this):
                                                            ?>
                                                            <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/edit-user?id=<?php echo $u['id']; ?>"
                                                                class="btn btn-secondary btn-sm btn-edit-user">Edit</a>
                                                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                                                <button type="button"
                                                                    onclick="confirmDelete(<?php echo $u['id']; ?>, '<?php echo addslashes($u['full_name']); ?>')"
                                                                    class="btn btn-secondary btn-sm btn-delete-user">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted italic px-2">Protected</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php elseif ($view === 'verification'): ?>
                    <div class="verification-view details-container">

                        <!-- Pending Requests Filter Bar -->
                        <div class="filter-bar filter-bar-card">
                            <form method="GET" class="filter-form filter-form-flex">
                                <input type="hidden" name="view" value="verification">

                                <!-- Search -->
                                <div class="filter-item search-wrapper">
                                    <i class="bi bi-search search-icon"></i>
                                    <input type="text" name="ver_search"
                                        value="<?php echo htmlspecialchars($_GET['ver_search'] ?? ''); ?>"
                                        placeholder="Search by Name or Username..." class="search-input">
                                </div>

                                <!-- Office Division -->
                                <div class="filter-item select-wrapper min-w-160">
                                    <select name="ver_office" class="select-input">
                                        <option value="">All Divisions</option>
                                        <?php if (!empty($office_categories)): ?>
                                            <?php foreach ($office_categories as $cat): ?>
                                                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($_GET['ver_office'] ?? '') === $cat ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($cat); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <i class="bi bi-chevron-down select-chevron"></i>
                                </div>

                                <div class="filter-actions filter-actions-flex">
                                    <button type="submit" class="btn-apply text-nowrap">Apply Filters</button>
                                    <?php if (!empty($_GET['ver_search']) || !empty($_GET['ver_office'])): ?>
                                        <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/manage-users?view=verification"
                                            class="btn-clear"><i class="bi bi-x-lg"></i></a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>

                        <?php if (empty($pending_users)): ?>
                            <div class="empty-state-card border-dashed">
                                <i
                                    class="bi <?php echo (!empty($_GET['ver_search']) || !empty($_GET['ver_office'])) ? 'bi-search' : 'bi-check2-all'; ?> empty-state-icon-large"></i>
                                <h2 class="mt-4 user-name-text">
                                    <?php echo (!empty($_GET['ver_search']) || !empty($_GET['ver_office'])) ? 'No Matches Found' : 'All Clear!'; ?>
                                </h2>
                                <p class="text-muted fs-5">
                                    <?php echo (!empty($_GET['ver_search']) || !empty($_GET['ver_office'])) ? 'Try adjusting your filters.' : 'There are no new registration requests to verify.'; ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="verification-list grid-gap-16">
                                <?php foreach ($pending_users as $p): ?>
                                    <div class="ver-item"
                                        onclick="location.href='<?php echo PUBLIC_ROOT; ?>index.php/admin/manage-users?view=details&id=<?php echo $p['id']; ?>'">
                                        <div class="ver-user">
                                            <div class="ver-avatar"><?php echo strtoupper(substr($p['full_name'], 0, 1)); ?></div>
                                            <div><span class="ver-name"><?php echo htmlspecialchars($p['full_name']); ?></span><span
                                                    class="ver-handle">@<?php echo htmlspecialchars($p['username']); ?></span></div>
                                        </div>
                                        <div><span class="ver-info-label">Office</span><span
                                                class="ver-info-value"><?php echo htmlspecialchars($p['office_station']); ?></span>
                                        </div>
                                        <div><span class="ver-info-label">Position</span><span
                                                class="ver-info-value"><?php echo htmlspecialchars($p['position'] ?: 'Staff'); ?></span>
                                        </div>
                                        <div class="ver-actions" onclick="event.stopPropagation()">
                                            <form method="POST" style="margin: 0; display: inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $p['id']; ?>">
                                                <button type="submit" name="approve_registration" class="ver-btn ver-btn-approve"
                                                    title="Approve"><i class="bi bi-check-lg"></i></button>
                                            </form>
                                            <form method="POST" style="margin: 0; display: inline;"
                                                onsubmit="return confirm('Reject this registration?');">
                                                <input type="hidden" name="user_id" value="<?php echo $p['id']; ?>">
                                                <button type="submit" name="reject_registration" class="ver-btn ver-btn-reject"
                                                    title="Reject"><i class="bi bi-trash3-fill"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php elseif ($view === 'details' && $target_user): ?>
                    <div class="details-container">
                        <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/manage-users?view=verification"
                            class="back-btn"><i class="bi bi-arrow-left"></i> Back
                            to Verification Requests</a>
                        <div class="detail-card">
                            <div class="detail-header">
                                <div class="user-flex gap-24">
                                    <div class="ver-avatar size-80 shadow-sm-avatar">
                                        <?php echo strtoupper(substr($target_user['full_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h2 class="text-white user-name-large mb-0">
                                            <?php echo htmlspecialchars($target_user['full_name']); ?>
                                        </h2>
                                        <span
                                            class="badge-username-details">@<?php echo htmlspecialchars($target_user['username']); ?></span>
                                    </div>
                                </div>
                                <div class="actions-flex-end">
                                    <form method="POST" class="m-0">
                                        <input type="hidden" name="user_id" value="<?php echo $target_user['id']; ?>">
                                        <button type="submit" name="reject_registration"
                                            class="btn btn-reject-account">Reject Account</button>
                                    </form>
                                    <form method="POST" class="m-0">
                                        <input type="hidden" name="user_id" value="<?php echo $target_user['id']; ?>">
                                        <button type="submit" name="approve_registration"
                                            class="btn btn-approve-account">Authorize Account</button>
                                    </form>
                                </div>
                            </div>
                            <div class="detail-body">
                                <div class="info-grid-modern">
                                    <div class="info-block"><span class="info-label">Office / Station</span>
                                        <div class="info-value">
                                            <?php echo htmlspecialchars($target_user['office_station']); ?>
                                        </div>
                                    </div>
                                    <div class="info-block"><span class="info-label">Current Position</span>
                                        <div class="info-value">
                                            <?php echo htmlspecialchars($target_user['position'] ?: 'Not Specified'); ?>
                                        </div>
                                    </div>
                                    <div class="info-block"><span class="info-label">Area of Specialization</span>
                                        <div class="info-value">
                                            <?php echo htmlspecialchars($target_user['area_of_specialization'] ?: 'Not Specified'); ?>
                                        </div>
                                    </div>
                                    <div class="info-block"><span class="info-label">Employee Number</span>
                                        <div class="info-value">
                                            <?php echo htmlspecialchars($target_user['employee_number'] ?: 'Not Specified'); ?>
                                        </div>
                                    </div>
                                    <div class="info-block"><span class="info-label">Rating Period</span>
                                        <div class="info-value">
                                            <?php echo htmlspecialchars($target_user['rating_period'] ?: 'Not Specified'); ?>
                                        </div>
                                    </div>
                                    <div class="info-block"><span class="info-label">Age</span>
                                        <div class="info-value"><?php echo $target_user['age'] ?: 'Not Specified'; ?></div>
                                    </div>
                                    <div class="info-block"><span class="info-label">Sex</span>
                                        <div class="info-value">
                                            <?php echo htmlspecialchars($target_user['sex'] ?: 'Not Specified'); ?>
                                        </div>
                                    </div>
                                    <div class="info-block"><span class="info-label">Registration IP</span>
                                        <div class="info-value"><?php echo $_SERVER['REMOTE_ADDR']; ?></div>
                                    </div>
                                    <div class="info-block"><span class="info-label">Registration Date</span>
                                        <div class="info-value">
                                            <?php echo date('F j, Y, g:i A', strtotime($target_user['created_at'])); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif ($view === 'notifications'): ?>
                    <div class="notifications-view details-container max-w-1000 mx-auto">

                        <!-- Profile Log Filter Bar -->
                        <div class="filter-bar mb-24">
                            <form method="GET" class="filter-form filter-form-flex">
                                <input type="hidden" name="view" value="notifications">

                                <!-- Search Input -->
                                <div class="filter-item search-wrapper">
                                    <i class="bi bi-search search-icon"></i>
                                    <input type="text" name="log_search"
                                        value="<?php echo htmlspecialchars($_GET['log_search'] ?? ''); ?>"
                                        placeholder="Search User, Position..." class="search-input">
                                </div>

                                <!-- Office Category -->
                                <div class="filter-item select-wrapper min-w-160">
                                    <select name="log_office" class="select-input">
                                        <option value="">All Offices</option>
                                        <?php if (!empty($office_categories)): ?>
                                            <?php foreach ($office_categories as $cat): ?>
                                                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($_GET['log_office'] ?? '') === $cat ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($cat); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <i class="bi bi-chevron-down select-chevron"></i>
                                </div>

                                <!-- Date Range -->
                                <div class="filter-wrapper px-3 py-1">
                                    <div class="filter-inputs">
                                        <input type="date" name="log_start_date"
                                            value="<?php echo htmlspecialchars($_GET['log_start_date'] ?? ''); ?>"
                                            class="filter-date">
                                        <span class="text-muted small fw-bold">to</span>
                                        <input type="date" name="log_end_date"
                                            value="<?php echo htmlspecialchars($_GET['log_end_date'] ?? ''); ?>"
                                            class="filter-date">
                                    </div>
                                </div>

                                <div class="filter-actions filter-actions-flex">
                                    <button type="submit" class="btn-apply text-nowrap">Apply Filters</button>
                                    <?php if (!empty($_GET['log_search']) || !empty($_GET['log_office']) || !empty($_GET['log_start_date'])): ?>
                                        <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/manage-users?view=notifications"
                                            class="btn-clear"><i class="bi bi-x-lg"></i></a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>

                        <div class="activity-feed">
                            <?php if (empty($audit_logs)): ?>
                                <div class="empty-state-card">
                                    <div class="empty-state-icon-box">
                                        <i class="bi bi-bell-slash"></i>
                                    </div>
                                    <h3 class="user-name-text mb-2">No Recent Activity</h3>
                                    <p class="text-muted">System-wide profile changes will appear here as they occur.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($audit_logs as $log):
                                    $is_admin_action = strpos($log['action'], 'Admin') !== false || strpos($log['action'], 'Modified') !== false;
                                    $badge_class = $is_admin_action ? 'badge-admin' : 'badge-profile';
                                    $initials = strtoupper(substr($log['user_name'], 0, 1));
                                    ?>
                                    <div class="activity-item">
                                        <div class="activity-avatar">
                                            <?php if (!empty($log['profile_picture'])): ?>
                                                <img src="<?php echo PUBLIC_ROOT . $log['profile_picture']; ?>" alt=""
                                                    class="activity-avatar-img">
                                            <?php else: ?>
                                                <?php echo $initials; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-header">
                                                <span
                                                    class="activity-user-name"><?php echo htmlspecialchars($log['user_name']); ?></span>
                                                <span class="activity-time">
                                                    <i class="bi bi-clock"></i>
                                                    <?php
                                                    $time = strtotime($log['created_at']);
                                                    echo date('M d, Y', $time) . ' • ' . date('h:i A', $time);
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="activity-action-badge <?php echo $badge_class; ?>">
                                                <i
                                                    class="bi <?php echo $is_admin_action ? 'bi-shield-shaded' : 'bi-person-circle'; ?> mr-1"></i>
                                                <?php echo htmlspecialchars($log['action']); ?>
                                            </div>
                                            <div class="activity-details">
                                                <?php echo htmlspecialchars($log['details']); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
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
    <!-- Deletion Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="delete-modal shadow-lg-soft">
            <div class="modal-header-danger">
                <div class="danger-icon-circle">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
            <div class="modal-body">
                <h3 class="modal-title">Confirm Deletion</h3>
                <p class="modal-text">Are you sure you want to delete <strong id="deleteTargetName"></strong>? This
                    action will permanently remove all associated data.</p>
            </div>
            <div class="modal-footer-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
                <form id="deleteForm" method="POST" class="m-0">
                    <input type="hidden" name="user_id" id="deleteUserId">
                    <button type="submit" name="delete_user" class="btn-confirm-delete">Delete Account</button>
                </form>
            </div>
        </div>
    </div>

    <script src="<?php echo PUBLIC_ROOT; ?>js/admin/manage_users.js?v=<?php echo time(); ?>"></script>
</body>

</html>