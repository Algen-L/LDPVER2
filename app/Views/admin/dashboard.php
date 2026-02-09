<?php
// Extracted variables from $data (handled by Controller::view)
// $filter, $date_from, $date_to, $totalSubmissions, $totalUsers, $pendingCount, $approvedCount,
// $osdsCount, $cidCount, $sgodCount, $freqLabels, $freqValues, $popOSDS, $popCID, $popSGOD,
// $hrStats, $auditTrail, $activePersonnel, $registrationGrowth, $activities, $user, $notifRepo
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LDP</title>
    <?php require BASE_PATH . 'includes/admin_head.php'; ?>
    <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/admin/dashboard.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="app-layout">
        <?php require BASE_PATH . 'includes/sidebar.php'; ?>

        <div class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <div class="breadcrumb">
                        <h1 class="page-title">Dashboard Overview</h1>
                    </div>
                </div>
                <div class="top-bar-right top-bar-right-actions">
                    <div class="current-date-box">
                        <div class="time-section">
                            <span id="real-time-clock"><?php echo date('h:i:s A'); ?></span>
                        </div>
                        <div class="date-section">
                            <i class="bi bi-calendar3"></i>
                            <span><?php echo date('F j, Y'); ?></span>
                        </div>
                    </div>
                    <form method="GET" id="filterForm" class="filter-form">
                        <div class="custom-dropdown" id="filterDropdown">
                            <input type="hidden" name="filter" id="filterInput"
                                value="<?php echo htmlspecialchars($filter); ?>">
                            <div class="dropdown-trigger" id="dropdownTrigger">
                                <div>
                                    <i class="bi bi-funnel funnel-icon"></i>
                                    <span id="selectedFilterText">
                                        <?php
                                        switch ($filter) {
                                            case 'today':
                                                echo 'Today';
                                                break;
                                            case 'week':
                                                echo 'This Week';
                                                break;
                                            case 'month':
                                                echo 'This Month';
                                                break;
                                            case 'all':
                                                echo 'All Time';
                                                break;
                                            case 'custom':
                                                echo 'Custom Range';
                                                break;
                                            default:
                                                echo 'Sort By';
                                        }
                                        ?>
                                    </span>
                                </div>
                                <i class="bi bi-chevron-down chevron-down"></i>
                            </div>

                            <div class="dropdown-menu-custom" id="dropdownMenu">
                                <div class="dropdown-item-custom <?php echo ($filter === 'today') ? 'active' : ''; ?>"
                                    data-value="today">
                                    <i class="bi bi-calendar-event"></i> Today
                                </div>
                                <div class="dropdown-item-custom <?php echo ($filter === 'week') ? 'active' : ''; ?>"
                                    data-value="week">
                                    <i class="bi bi-calendar-range"></i> This Week
                                </div>
                                <div class="dropdown-item-custom <?php echo ($filter === 'month') ? 'active' : ''; ?>"
                                    data-value="month">
                                    <i class="bi bi-calendar-month"></i> This Month
                                </div>
                                <div class="dropdown-item-custom <?php echo ($filter === 'all') ? 'active' : ''; ?>"
                                    data-value="all">
                                    <i class="bi bi-infinity"></i> All Time
                                </div>
                                <div class="dropdown-item-custom <?php echo ($filter === 'custom') ? 'active' : ''; ?>"
                                    data-value="custom">
                                    <i class="bi bi-calendar-plus"></i> Custom Range
                                </div>
                            </div>

                            <div id="customDateInputs" class="custom-range-inputs"
                                style="display: <?php echo ($filter === 'custom') ? 'flex' : 'none'; ?>;">
                                <div class="date-input-wrapper">
                                    <input type="date" name="date_from"
                                        value="<?php echo htmlspecialchars($date_from); ?>"
                                        class="form-control form-control-sm custom-date-input" />
                                </div>
                                <span class="date-range-to">to</span>
                                <div class="date-input-wrapper">
                                    <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>"
                                        class="form-control form-control-sm custom-date-input" />
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm apply-btn">
                                    Apply
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </header>

            <main class="content-wrapper">
                <?php if (($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') && ($hrStats['pending_registrations'] ?? 0) > 0): ?>
                    <div class="dashboard-alert-banner" onclick="location.href='<?php echo PUBLIC_ROOT; ?>index.php/admin/manage-users?view=verification'" style="cursor: pointer; background: linear-gradient(135, #fff5f5 0%, #fff 100%); border-left: 5px solid #f43f5e; padding: 15px 20px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 15px rgba(244, 63, 94, 0.1);">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="background: rgba(244, 63, 94, 0.1); width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #f43f5e;">
                                <i class="bi bi-person-plus-fill" style="font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <h4 style="margin: 0; color: #1e293b; font-size: 1rem; font-weight: 700;">Action Required: Pending Registrations</h4>
                                <p style="margin: 0; color: #64748b; font-size: 0.85rem;">There are <?php echo $hrStats['pending_registrations']; ?> new personnel accounts awaiting your verification and approval.</p>
                            </div>
                        </div>
                        <div style="color: #f43f5e; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; gap: 5px;">
                            Review Now <i class="bi bi-arrow-right"></i>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="stats-row <?php echo ($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') ? 'stats-row-5' : ''; ?>">
                    <?php if ($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr'): ?>
                        <!-- HR Specific Stats -->
                        <div class="stat-card" style="--accent-color: var(--vibrant-blue);">
                            <div class="stat-icon" style="background: rgba(14, 165, 233, 0.1); color: var(--vibrant-blue);">
                                <i class="bi bi-box-arrow-in-right"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-label">Today's Logins</span>
                                <span class="stat-value"><?php echo number_format($hrStats['today_logins'] ?? 0); ?></span>
                            </div>
                        </div>

                        <div class="stat-card" style="--accent-color: var(--vibrant-orange);">
                            <div class="stat-icon"
                                style="background: rgba(249, 115, 22, 0.1); color: var(--vibrant-orange);">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-label">Total Users</span>
                                <span class="stat-value"><?php echo number_format($totalUsers); ?></span>
                            </div>
                        </div>

                        <div class="stat-card" style="--accent-color: #6366f1;">
                            <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                                <i class="bi bi-person-plus-fill"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-label">New Registrations</span>
                                <span class="stat-value"><?php echo number_format($hrStats['new_registrations'] ?? 0); ?></span>
                            </div>
                        </div>

                        <div class="stat-card" style="--accent-color: #10b981;">
                            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-label">Active Today</span>
                                <span class="stat-value"><?php echo number_format($hrStats['active_today'] ?? 0); ?></span>
                            </div>
                        </div>

                        <?php if (($hrStats['pending_registrations'] ?? 0) > 0): ?>
                            <div class="stat-card" style="--accent-color: #f43f5e; cursor: pointer;" 
                                 onclick="location.href='<?php echo PUBLIC_ROOT; ?>index.php/admin/manage-users?view=verification'">
                                <div class="stat-icon" style="background: rgba(244, 63, 94, 0.1); color: #f43f5e;">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="stat-label">Pending Verification</span>
                                    <span class="stat-value" style="color: #f43f5e;"><?php echo number_format($hrStats['pending_registrations']); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Standard Admin Stats -->
                        <div class="stat-card" style="--accent-color: var(--vibrant-blue);">
                            <div class="stat-icon" style="background: rgba(14, 165, 233, 0.1); color: var(--vibrant-blue);">
                                <i class="bi bi-journal-text"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-label">Submissions</span>
                                <span class="stat-value"><?php echo number_format($totalSubmissions); ?></span>
                            </div>
                        </div>

                        <div class="stat-card" style="--accent-color: var(--vibrant-orange);">
                            <div class="stat-icon"
                                style="background: rgba(249, 115, 22, 0.1); color: var(--vibrant-orange);">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-label">Total Users</span>
                                <span class="stat-value"><?php echo number_format($totalUsers); ?></span>
                            </div>
                        </div>

                        <div class="stat-card" style="--accent-color: #6366f1;">
                            <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-label">Pending</span>
                                <span class="stat-value"><?php echo number_format($pendingCount); ?></span>
                            </div>
                        </div>

                        <div class="stat-card" style="--accent-color: #10b981;">
                            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-label">Approved</span>
                                <span class="stat-value"><?php echo number_format($approvedCount); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="dashboard-row-middle">
                    <div class="dashboard-card hover-elevate">
                        <div class="card-header card-header-gradient">
                            <h2 class="card-title-white"><i class="bi bi-bar-chart-line card-title-icon-white"></i>
                                <?php echo ($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') ? 'User Registration Growth' : 'Submission Frequency'; ?>
                            </h2>
                        </div>
                        <div class="card-body p-2 h-180">
                            <canvas id="frequencyChart"></canvas>
                        </div>
                    </div>

                    <div class="dashboard-card hover-elevate">
                        <div class="card-header card-header-standard">
                            <h2 class="card-title-standard"><i class="bi bi-building text-gradient"></i>
                                <?php echo ($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') ? 'User Population by Office' : 'Office Activity Distribution'; ?>
                            </h2>
                        </div>
                        <div class="card-body office-distribution-body">
                            <div class="doughnut-wrapper">
                                <canvas id="officeChart"></canvas>
                            </div>
                            <div class="office-legend">
                                <div class="legend-item">
                                    <div class="legend-label-box">
                                        <span class="legend-color-dot bg-vibrant-orange shadow-vibrant-orange"></span>
                                        <span class="legend-text">OSDS</span>
                                    </div>
                                    <span class="legend-value" id="legendOSDS"><?php echo number_format(($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') ? $popOSDS : $osdsCount); ?></span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-label-box">
                                        <span class="legend-color-dot bg-warning"></span>
                                        <span class="legend-text">CID</span>
                                    </div>
                                    <span class="legend-value" id="legendCID"><?php echo number_format(($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') ? $popCID : $cidCount); ?></span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-label-box">
                                        <span class="legend-color-dot bg-vibrant-blue shadow-vibrant-blue"></span>
                                        <span class="legend-text">SGOD</span>
                                    </div>
                                    <span class="legend-value" id="legendSGOD"><?php echo number_format(($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') ? $popSGOD : $sgodCount); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-row-bottom">
                    <?php if ($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr'): ?>
                            <!-- Recently Active Personnel -->
                            <div class="dashboard-card hover-elevate">
                                <div class="card-header card-header-standard">
                                    <h2 class="card-title-standard"><i class="bi bi-people-fill text-gradient"></i> Recently Active Personnel</h2>
                                </div>
                                <div class="card-body p-0 max-h-350 overflow-y-auto">
                                    <div class="activity-feed">
                                        <?php if (empty($activePersonnel)): ?>
                                                <div class="feed-empty-state">No active users recorded.</div>
                                        <?php else: ?>
                                                <?php foreach ($activePersonnel as $person): ?>
                                                        <div class="feed-item">
                                                            <?php if (!empty($person['profile_picture'])): ?>
                                                                    <img src="<?php echo PUBLIC_ROOT . $person['profile_picture']; ?>" class="feed-avatar" alt="">
                                                            <?php else: ?>
                                                                    <div class="feed-avatar-placeholder"><?php echo strtoupper(substr($person['full_name'], 0, 1)); ?></div>
                                                            <?php endif; ?>
                                                            <div class="feed-info">
                                                                <span class="feed-user"><?php echo htmlspecialchars($person['full_name']); ?></span>
                                                                <span class="feed-activity text-muted"><?php echo htmlspecialchars($person['office_station']); ?></span>
                                                            </div>
                                                            <div class="feed-time">
                                                                <?php
                                                                $time = strtotime($person['last_seen']);
                                                                if (date('Y-m-d', $time) === date('Y-m-d'))
                                                                    echo 'Today, ' . date('h:i A', $time);
                                                                else
                                                                    echo date('M d, h:i A', $time);
                                                                ?>
                                                            </div>
                                                        </div>
                                                <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- System Audit Trail -->
                            <div class="dashboard-card hover-elevate">
                                <div class="card-header card-header-gradient">
                                    <span class="d-flex align-items-center">
                                        <h2 class="card-title-white mb-0"><i class="bi bi-shield-lock card-title-icon-white"></i> System Audit Trail</h2>
                                    </span>
                                    <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/activity-logs" class="view-all-btn">Detailed Logs</a>
                                </div>
                                <div class="card-body p-0 max-h-350 overflow-y-auto">

                                    <div class="audit-trail">
                                        <?php if (empty($auditTrail)): ?>
                                                <div class="feed-empty-state">No recent system events.</div>
                                        <?php else: ?>
                                                <?php foreach (array_slice($auditTrail, 0, 15) as $log):
                                                    $icon = 'bi-info-circle';
                                                    $lvl = 'lvl-info';
                                                    if (strpos($log['action'], 'Approved') !== false) {
                                                        $icon = 'bi-check-all';
                                                        $lvl = 'lvl-success';
                                                    } elseif (strpos($log['action'], 'Updated') !== false) {
                                                        $icon = 'bi-pencil-square';
                                                        $lvl = 'lvl-warn';
                                                    } elseif (strpos($log['action'], 'Logged') !== false) {
                                                        $icon = 'bi-person-badge';
                                                        $lvl = 'lvl-info';
                                                    } elseif (strpos($log['action'], 'User') !== false) {
                                                        $icon = 'bi-person-gear';
                                                        $lvl = 'lvl-warn';
                                                    }
                                                    ?>
                                                        <div class="audit-item">
                                                            <div class="audit-icon <?php echo $lvl; ?>">
                                                                <i class="bi <?php echo $icon; ?>"></i>
                                                            </div>
                                                            <div class="audit-content">
                                                                <span class="audit-title"><?php echo htmlspecialchars($log['user_name']); ?>: <?php echo htmlspecialchars($log['action']); ?></span>
                                                                <div class="audit-meta">
                                                                    <span><i class="bi bi-clock"></i> <?php echo date('h:i A', strtotime($log['created_at'])); ?></span>
                                                                    <span>•</span>
                                                                    <span><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($log['ip_address']); ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                    <?php else: ?>
                            <!-- Existing side-by-side feeds for Admin / Super Admin -->
                            <div class="dashboard-card hover-elevate">
                                <div class="card-header card-header-standard">
                                    <h2 class="card-title-standard"><i class="bi bi-megaphone text-gradient"></i> Recent Activity Submitted</h2>
                                </div>
                                <div class="card-body p-0 max-h-350 overflow-y-auto">
                                    <div class="activity-feed">
                                        <?php if (empty($activities)): ?>
                                                <div class="feed-empty-state">No recent activities.</div>
                                        <?php else: ?>
                                                <?php foreach (array_slice($activities, 0, 10) as $i => $act):
                                                    $feed_class = '';
                                                    $f_cat = $act['office_division'] ?? '';
                                                    if ($f_cat === 'OSDS')
                                                        $feed_class = 'osds';
                                                    elseif ($f_cat === 'CID')
                                                        $feed_class = 'cid';
                                                    elseif ($f_cat === 'SGOD')
                                                        $feed_class = 'sgod';

                                                    $isRelevantExpertise = !empty($act['competency']) && strpos($act['competency'], 'Relevant Expertise') !== false;
                                                    $expertStyle = $isRelevantExpertise ? 'border: 2px solid #c7d2fe; position: relative;' : '';
                                                    ?>
                                                        <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/view-activity?id=<?php echo $act['id']; ?>" 
                                                           class="feed-item <?php echo $feed_class; ?>"
                                                           style="<?php echo $expertStyle; ?>">
                                                            <?php if ($act['profile_picture']): ?>
                                                                    <img src="<?php echo PUBLIC_ROOT . $act['profile_picture']; ?>" class="feed-avatar">
                                                            <?php else: ?>
                                                                    <div class="feed-avatar-placeholder">
                                                                        <?php echo strtoupper(substr($act['full_name'], 0, 1)); ?>
                                                                    </div>
                                                            <?php endif; ?>
                                                            <div class="feed-info">
                                                                <span class="feed-user"><?php echo htmlspecialchars($act['full_name']); ?></span>
                                                                <span class="feed-activity text-truncate feed-activity-constraint">
                                                                    <?php echo htmlspecialchars($act['title']); ?>
                                                                </span>
                                                                <?php if ($isRelevantExpertise): ?>
                                                                    <span style="display: inline-flex; align-items: center; gap: 4px; background: #e0e7ff; color: #4338ca; padding: 1px 6px; border-radius: 4px; font-size: 0.55rem; font-weight: 800; text-transform: uppercase; margin-top: 2px;">
                                                                        <i class="bi bi-bookmark-star-fill"></i> RECORDED
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="feed-time">
                                                                <?php echo time_elapsed_string($act['activity_created_at'] ?? $act['created_at']); ?>
                                                            </div>
                                                        </a>
                                                <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="dashboard-card hover-elevate">
                                <div class="card-header card-header-gradient">
                                    <h2 class="card-title-white"><i class="bi bi-journal-text card-title-icon-white"></i> Recent Activity Logs</h2>
                                    <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/submissions" class="view-all-btn">View All</a>
                                </div>
                                <div class="card-body p-0 max-h-350 overflow-y-auto">
                                    <div class="table-responsive">
                                        <table class="data-table">
                                            <thead class="sticky-table-header">
                                                <tr>
                                                    <th>Submission Date</th>
                                                    <th>User / Personnel</th>
                                                    <th>Activity Description</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($activities)): ?>
                                                        <tr><td colspan="4" class="text-center py-4">No recent activities.</td></tr>
                                                <?php else: ?>
                                                        <?php foreach (array_slice($activities, 0, 15) as $act):
                                                            $s_class = 'status-pending';
                                                            $s_label = 'Pending';
                                                            if ($act['approved_sds']) {
                                                                $s_class = 'status-resolved';
                                                                $s_label = 'Approved';
                                                            } elseif ($act['recommending_asds']) {
                                                                $s_class = 'status-in_progress';
                                                                $s_label = 'Recommended';
                                                            } elseif ($act['reviewed_by_supervisor']) {
                                                                $s_class = 'status-accepted';
                                                                $s_label = 'Reviewed';
                                                            }
                                                            ?>
                                                                <?php
                                                                $isRelevantExpertise = !empty($act['competency']) && strpos($act['competency'], 'Relevant Expertise') !== false;
                                                                $rowStyle = $isRelevantExpertise ? 'border: 2px solid #c7d2fe; border-radius: 8px;' : '';
                                                                ?>
                                                                <tr style="<?php echo $rowStyle; ?>">
                                                                    <td><?php echo date('M d, Y', strtotime($act['activity_created_at'] ?? $act['created_at'])); ?></td>
                                                                    <td><strong><?php echo htmlspecialchars($act['full_name']); ?></strong><br><small><?php echo htmlspecialchars($act['office_station']); ?></small></td>
                                                                    <td>
                                                                        <span class="text-truncate d-block activity-desc-constraint"><?php echo htmlspecialchars($act['title']); ?></span>
                                                                        <?php if ($isRelevantExpertise): ?>
                                                                            <span style="display: inline-flex; align-items: center; gap: 4px; background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 4px; font-size: 0.6rem; font-weight: 800; text-transform: uppercase; margin-top: 4px;">
                                                                                <i class="bi bi-bookmark-star-fill"></i> RECORDED ENTRY
                                                                            </span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td><span class="status-badge <?php echo $s_class; ?>"><?php echo $s_label; ?></span></td>
                                                                </tr>
                                                        <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                    <?php endif; ?>
                </div>
            </main>

            <footer class="admin-footer">
                <p>&copy; <?php echo date('Y'); ?> SDO L&D Passbook System. <span class="text-muted">Developed by
                        Algen
                        D. Loveres and Cedrick V. Bacaresas</span></p>
            </footer>
        </div>
    </div>
    <script>
        // Pass PHP data to JavaScript
        window.dashboardData = {
            freqLabels: <?php echo json_encode(($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') ? array_keys($registrationGrowth) : $freqLabels); ?>,
            freqValues: <?php echo json_encode(($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') ? array_values($registrationGrowth) : $freqValues); ?>,
            osdsCount: <?php echo ($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') ? $popOSDS : $osdsCount; ?>,
            cidCount: <?php echo ($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') ? $popCID : $cidCount; ?>,
            sgodCount: <?php echo ($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') ? $popSGOD : $sgodCount; ?>,
            isHR: <?php echo ($_SESSION['role'] === 'head_hr' || $_SESSION['role'] === 'hr') ? 'true' : 'false'; ?>
        };
    </script>
    <script src="<?php echo PUBLIC_ROOT; ?>js/admin/dashboard.js?v=<?php echo time(); ?>"></script>
</body>

</html>
