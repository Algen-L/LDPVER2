<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - HR Panel</title>
    <?php require BASE_PATH . 'includes/head.php'; ?>
    <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/hr/dashboard.css?v=<?php echo time(); ?>">
</head>

<body>

    <div class="app-layout">
        <!-- Sidebar -->
        <?php require BASE_PATH . 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <div class="breadcrumb">
                        <h1 class="page-title">My Dashboard</h1>
                    </div>
                </div>
                <div class="top-bar-right">
                    <div class="current-date-box">
                        <div class="time-section">
                            <span id="real-time-clock">
                                <?php echo date('h:i:s A'); ?>
                            </span>
                        </div>
                        <div class="date-section">
                            <i class="bi bi-calendar3"></i>
                            <span>
                                <?php echo date('F j, Y'); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            <main class="content-wrapper">
                <div class="user-dashboard-grid">
                    <div class="dashboard-card user-profile-card">
                        <div class="card-profile-header"></div>
                        <div class="card-profile-body">
                            <div class="profile-avatar-container">
                                <?php if (!empty($user['profile_picture'])): ?>
                                    <img src="<?php echo PUBLIC_ROOT . htmlspecialchars($user['profile_picture']); ?>"
                                        alt="Profile" class="profile-avatar">
                                <?php else: ?>
                                    <div class="profile-avatar-placeholder">
                                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <h2 class="profile-name">
                                <?php echo htmlspecialchars($user['full_name']); ?>
                            </h2>
                            <p class="hr-profile-role">
                                HR Personnel
                            </p>
                            <p class="profile-position">
                                <?php echo htmlspecialchars($user['position'] ?: 'HR Management'); ?>
                            </p>

                            <div class="profile-stats">
                                <div class="profile-stat-item">
                                    <span class="profile-stat-val">
                                        <?php echo $total_count; ?>
                                    </span>
                                    <span class="profile-stat-label">Activities</span>
                                </div>
                                <div class="profile-stat-item">
                                    <span class="profile-stat-val" style="color: var(--success);">
                                        <?php echo $approved_count; ?>
                                    </span>
                                    <span class="profile-stat-label">Approved</span>
                                </div>
                            </div>

                            <div class="progress-section mb-24">
                                <div class="progress-label-flex">
                                    <span>Goal Completion</span>
                                    <span class="completion-pct">
                                        <?php echo $progress_pct; ?>%
                                    </span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar-inner" style="width: <?php echo $progress_pct; ?>%;">
                                    </div>
                                </div>
                            </div>

                            <div class="profile-info-list profile-info-divider">
                                <div class="profile-info-item">
                                    <div class="profile-info-icon"><i class="bi bi-building"></i></div>
                                    <div class="profile-info-content">
                                        <span class="profile-info-label">Office / Station</span>
                                        <span class="profile-info-value">
                                            <?php echo htmlspecialchars($user['office_station'] ?: 'Not Set'); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="profile-info-item">
                                    <div class="profile-info-icon icon-warning-bg"><i class="bi bi-person-badge"></i>
                                    </div>
                                    <div class="profile-info-content">
                                        <span class="profile-info-label">Employee Number</span>
                                        <span class="profile-info-value">
                                            <?php echo htmlspecialchars($user['employee_number'] ?: 'Not Set'); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="profile-info-item">
                                    <div class="profile-info-icon icon-info-bg">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
                                    <div class="profile-info-content">
                                        <span class="profile-info-label">Rating Period</span>
                                        <span class="profile-info-value">
                                            <?php echo htmlspecialchars($user['rating_period'] ?: 'Not Set'); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="profile-info-item">
                                    <div class="profile-info-icon icon-success-bg">
                                        <i class="bi bi-award"></i>
                                    </div>
                                    <div class="profile-info-content">
                                        <span class="profile-info-label">Specialization</span>
                                        <span class="profile-info-value">
                                            <?php echo htmlspecialchars($user['area_of_specialization'] ?: 'Generalist'); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="profile-info-item">
                                    <div class="profile-info-icon icon-danger-bg">
                                        <i class="bi bi-lightbulb-fill"></i>
                                    </div>
                                    <div class="profile-info-content">
                                        <span class="profile-info-label">Unaddressed Needs</span>
                                        <div class="needs-list-container">
                                            <?php if (!empty($unaddressed_needs)): ?>
                                                <ul class="needs-list-styled">
                                                    <?php foreach ($unaddressed_needs as $need): ?>
                                                        <li>
                                                            <?php echo htmlspecialchars($need['need_text']); ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <span class="needs-all-addressed">✓ All needs currently
                                                    addressed</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Right Column: Activity Center -->
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <!-- Quick Actions -->
                        <div class="dashboard-card dashboard-hero-card">
                            <div class="hero-content">
                                <div class="hero-text">
                                    <h3>Welcome to HR Portal</h3>
                                    <p>As HR Personnel, you can track and manage learning and development activities
                                        for all staff members within the system.
                                    </p>
                                </div>
                                <div class="hero-action">
                                    <a href="<?php echo PUBLIC_ROOT; ?>index.php/user/add-activity"
                                        class="hero-action-btn">
                                        <i class="bi bi-plus"></i>
                                        <span>ADD ACTIVITY</span>
                                    </a>
                                </div>
                                <img src="<?php echo PUBLIC_ROOT; ?>assets/logologo.png" alt="Decorative"
                                    class="hero-decorative-img">
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="dashboard-card recent-activity-card">
                            <div class="card-header">
                                <h2><i class="bi bi-clock-history"></i> My Recent Activity</h2>
                                <a href="<?php echo PUBLIC_ROOT; ?>index.php/user/submissions-progress"
                                    class="view-history-link">View
                                    My History <i class="bi bi-arrow-right"></i></a>
                            </div>
                            <div class="card-body activity-list-card-body">
                                <div class="activity-list activity-list-scroll">
                                    <?php if (count($activities) > 0): ?>
                                        <?php foreach ($activities as $act): ?>
                                            <div class="activity-item">
                                                <div class="activity-icon">
                                                    <i class="bi bi-journal-check"></i>
                                                </div>
                                                <div class="activity-content">
                                                    <div class="activity-header">
                                                        <span class="activity-title">
                                                            <?php echo htmlspecialchars($act['title']); ?>
                                                        </span>
                                                        <span class="activity-time">
                                                            <?php
                                                            $dates = explode(', ', $act['date_attended']);
                                                            echo date('M d, Y', strtotime($dates[0]));
                                                            ?>
                                                        </span>
                                                    </div>
                                                    <div class="activity-meta-flex">
                                                        <span class="competency-text">
                                                            <?php echo htmlspecialchars($act['competency']); ?>
                                                        </span>
                                                        <?php
                                                        $statusLabel = 'Pending';
                                                        $statusClass = 'status-pending';

                                                        if ($act['approved_sds']) {
                                                            $statusLabel = 'Approved';
                                                            $statusClass = 'status-approved';
                                                        } elseif ($act['recommending_asds']) {
                                                            $statusLabel = 'Recommending';
                                                            $statusClass = 'status-recommending';
                                                        } elseif ($act['reviewed_by_supervisor']) {
                                                            $statusLabel = 'Reviewed';
                                                            $statusClass = 'status-reviewed';
                                                        }
                                                        ?>
                                                        <span class="activity-status-badge <?php echo $statusClass; ?>">
                                                            <i class="bi bi-circle-fill"
                                                                style="font-size: 0.4rem; margin-right: 4px;"></i>
                                                            <?php echo $statusLabel; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="empty-activity-state">
                                            <i class="bi bi-inbox empty-activity-icon"></i>
                                            <p class="empty-activity-text">No activities recorded yet.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="user-footer">
                <p>&copy;
                    <?php echo date('Y'); ?> SDO L&D Passbook System. <span class="text-muted">Developed by Algen
                        D. Loveres and Cedrick V. Bacaresas</span>
                </p>
            </footer>
        </div>
    </div>

</body>

</html>