<?php
// Extracted variables from $data (handled by Controller::view)
// $all_users, $filters, $activities, $statuses, $user, $notifRepo
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submissions Management - Admin</title>
    <?php require BASE_PATH . 'includes/admin_head.php'; ?>
    <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/admin/submissions.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="app-layout">
        <?php require BASE_PATH . 'includes/sidebar.php'; ?>

        <div class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <div class="breadcrumb">
                        <h1 class="page-title">Submission Management</h1>
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
                <!-- Premium Filter Bar -->
                <div class="premium-filter-container">
                    <form method="GET" class="filter-form" id="mainFilterForm">
                        <div class="filter-grid">
                            <div class="search-wrapper">
                                <i class="bi bi-search"></i>
                                <input type="text" name="search"
                                    value="<?php echo htmlspecialchars($filters['search']); ?>"
                                    placeholder="Search entries, names, offices or categories (OSDS, CID, SGOD)..."
                                    class="search-control">
                            </div>

                            <!-- Status Custom Select -->
                            <div class="custom-select-wrapper" id="statusSelect">
                                <input type="hidden" name="status"
                                    value="<?php echo htmlspecialchars($filters['status_filter']); ?>">
                                <div class="custom-select-trigger">
                                    <span class="custom-select-text">
                                        <?php
                                        $sText = 'All Statuses';
                                        if (isset($statuses[$filters['status_filter']]))
                                            $sText = $statuses[$filters['status_filter']];
                                        echo htmlspecialchars($sText);
                                        ?>
                                    </span>
                                    <i class="bi bi-chevron-down"></i>
                                </div>
                                <div class="custom-select-options">
                                    <div class="custom-option <?php echo $filters['status_filter'] == '' ? 'selected' : ''; ?>"
                                        data-value="">All Statuses</div>
                                    <?php foreach ($statuses as $val => $label): ?>
                                        <div class="custom-option <?php echo $filters['status_filter'] == $val ? 'selected' : ''; ?>"
                                            data-value="<?php echo $val; ?>">
                                            <?php echo $label; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Date Range -->
                            <div class="date-range-pills">
                                <i class="bi bi-calendar-range"></i>
                                <input type="date" name="start_date" value="<?php echo $filters['start_date']; ?>"
                                    class="date-pill-input" title="From Date">
                                <span class="date-separator">TO</span>
                                <input type="date" name="end_date" value="<?php echo $filters['end_date']; ?>"
                                    class="date-pill-input" title="To Date">
                            </div>

                            <!-- Actions -->
                            <div class="filter-actions">
                                <button type="submit" class="apply-btn">
                                    <i class="bi bi-funnel-fill"></i> Apply
                                </button>
                                <?php if ($filters['user_id'] > 0 || $filters['status_filter'] || $filters['search'] || $filters['start_date'] || $filters['end_date']): ?>
                                    <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/submissions" class="reset-btn"
                                        title="Reset all filters">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Submissions Table Section -->
                <div class="dashboard-card hover-elevate mb-0">
                    <div class="card-header card-header-compact">
                        <h2 class="card-title-compact"><i class="bi bi-file-earmark-text text-gradient"></i> Activity
                            Submissions</h2>
                        <span class="result-count">Showing
                            <?php echo count($activities); ?>
                            total records
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="submissions-scroll-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Submission Date</th>
                                        <th>Personnel Info</th>
                                        <th>Activity Details</th>
                                        <th style="text-align: center;">Approvals</th>
                                        <th>Status</th>
                                        <th style="text-align: right;">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($activities)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="empty-state">
                                                    <i class="bi bi-folder2-open text-muted empty-state-icon"></i>
                                                    <p class="mt-3 text-muted">No submissions found matching your filters.
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($activities as $act):
                                            $row_class = '';
                                            if (!empty($act['office_division'])) {
                                                $row_class = 'row-' . strtolower($act['office_division']);
                                            }

                                            // Highlight Logic for Immediate Head
                                            if ($_SESSION['role'] === 'immediate_head' && $act['reviewed_by_supervisor'] && $act['recommending_asds'] && !$act['approved_sds']) {
                                                $row_class .= ' highlight-pending-approval';
                                            }
                                            ?>
                                            <tr class="<?php echo $row_class; ?>">
                                                <td>
                                                    <span class="cell-primary">
                                                        <i class="bi bi-calendar-event text-muted me-2"></i>
                                                        <?php echo date('M d, Y', strtotime($act['created_at'])); ?>
                                                    </span>
                                                    <span class="cell-secondary">Logged
                                                        <?php echo date('M d, Y', strtotime($act['created_at'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="cell-primary">
                                                        <?php echo htmlspecialchars($act['full_name']); ?>
                                                    </div>
                                                    <div class="cell-secondary">
                                                        <?php echo htmlspecialchars($act['office_station']); ?>
                                                    </div>
                                                </td>
                                                <td style="max-width: 320px;">
                                                    <div class="cell-primary text-truncate"
                                                        title="<?php echo htmlspecialchars($act['title']); ?>">
                                                        <?php echo htmlspecialchars($act['title']); ?>
                                                    </div>
                                                    <div class="cell-secondary text-truncate">
                                                        <?php echo htmlspecialchars($act['competency']); ?>
                                                    </div>
                                                </td>
                                                <td style="text-align: center;">
                                                    <?php $isRelevantExpertise = strpos($act['competency'], 'Relevant Expertise') !== false; ?>
                                                    <?php if ($isRelevantExpertise): ?>
                                                        <span class="status-badge"
                                                            style="background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; font-size: 0.7rem;">
                                                            Recorded
                                                        </span>
                                                    <?php else: ?>
                                                        <div class="approval-indicators">
                                                            <span title="Supervisor Reviewed">
                                                                <i class="bi bi-check-circle-fill approval-icon <?php echo $act['reviewed_by_supervisor'] ? 'text-success' : 'text-muted'; ?>"
                                                                    style="opacity: <?php echo $act['reviewed_by_supervisor'] ? '1' : '0.4'; ?>;"></i>
                                                            </span>
                                                            <span title="ASDS Recommended">
                                                                <i class="bi bi-check-circle-fill approval-icon <?php echo $act['recommending_asds'] ? 'text-primary' : 'text-muted'; ?>"
                                                                    style="opacity: <?php echo $act['recommending_asds'] ? '1' : '0.4'; ?>;"></i>
                                                            </span>
                                                            <span title="SDS Approved">
                                                                <i class="bi bi-check-circle-fill approval-icon <?php echo $act['approved_sds'] ? 'text-success' : 'text-muted'; ?>"
                                                                    style="opacity: <?php echo $act['approved_sds'] ? '1' : '0.4'; ?>;"></i>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($isRelevantExpertise): ?>
                                                        <span class="status-badge"
                                                            style="background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe;">
                                                            Recorded
                                                        </span>
                                                    <?php else: ?>
                                                        <?php
                                                        $status_class = 'status-pending';
                                                        $label = 'Pending';
                                                        if ($act['approved_sds']) {
                                                            $status_class = 'status-resolved';
                                                            $label = 'Approved';
                                                        } elseif ($act['recommending_asds']) {
                                                            $status_class = 'status-in_progress';
                                                            $label = 'Recommended';
                                                        } elseif ($act['reviewed_by_supervisor']) {
                                                            $status_class = 'status-accepted';
                                                            $label = 'Reviewed';
                                                        }
                                                        ?>
                                                        <span class="status-badge <?php echo $status_class; ?>">
                                                            <?php echo $label; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="text-align: right;">
                                                    <div class="row-actions">
                                                        <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/view-activity?id=<?php echo $act['id']; ?>"
                                                            class="btn btn-secondary btn-icon" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/edit-activity?id=<?php echo $act['id']; ?>"
                                                            class="btn btn-outline btn-icon" title="Edit Entry">
                                                            <i class="bi bi-pencil text-primary"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="admin-footer">
                <p>&copy;
                    <?php echo date('Y'); ?> SDO L&D Passbook System. <span class="text-muted">Developed by Algen
                        D. Loveres and Cedrick V. Bacaresas</span>
                </p>
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Generic Custom Select Handler
            const setupCustomSelect = (containerId) => {
                const container = document.getElementById(containerId);
                const trigger = container.querySelector('.custom-select-trigger');
                const options = container.querySelector('.custom-select-options');
                const text = container.querySelector('.custom-select-text');
                const hiddenInput = container.querySelector('input[type="hidden"]');
                const optionItems = container.querySelectorAll('.custom-option');

                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    // Close other dropdowns first
                    document.querySelectorAll('.custom-select-options').forEach(opt => {
                        if (opt !== options) opt.classList.remove('show');
                    });
                    document.querySelectorAll('.custom-select-trigger').forEach(trig => {
                        if (trig !== trigger) trig.classList.remove('active');
                    });

                    options.classList.toggle('show');
                    trigger.classList.toggle('active');
                });

                optionItems.forEach(item => {
                    item.addEventListener('click', () => {
                        const val = item.getAttribute('data-value');
                        hiddenInput.value = val;
                        text.textContent = item.textContent.trim();

                        // Update UI
                        optionItems.forEach(i => i.classList.remove('selected'));
                        item.classList.add('selected');

                        options.classList.remove('show');
                        trigger.classList.remove('active');
                    });
                });
            };


            setupCustomSelect('statusSelect');

            // Global Click to close dropdowns
            document.addEventListener('click', () => {
                document.querySelectorAll('.custom-select-options').forEach(opt => opt.classList.remove('show'));
                document.querySelectorAll('.custom-select-trigger').forEach(trig => trig.classList.remove('active'));
            });
        });
    </script>
</body>

</html>