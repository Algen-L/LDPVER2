<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Submissions Progress - LDP</title>
    <?php include BASE_PATH . 'includes/head.php'; ?>
    <style>
        .submission-card {
            margin-bottom: 24px;
            position: relative;
        }

        .submission-card .card-body {
            padding: 16px 20px;
        }

        .prog-track-wrapper {
            margin-top: 16px;
            position: relative;
            padding: 0 10px;
        }

        .prog-track-line {
            position: absolute;
            top: 14px;
            left: 20px;
            right: 20px;
            height: 4px;
            background: var(--bg-tertiary);
            z-index: 1;
            border-radius: 2px;
        }

        .prog-track-fill {
            position: absolute;
            top: 14px;
            left: 20px;
            height: 4px;
            background: var(--success);
            z-index: 2;
            border-radius: 2px;
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .prog-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            z-index: 3;
        }

        .prog-step {
            text-align: center;
            flex: 1;
        }

        .prog-icon {
            width: 32px;
            height: 32px;
            background: white;
            border: 2.5px solid var(--border-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-size: 0.85rem;
            color: var(--text-muted);
            transition: all var(--transition-base);
        }

        .prog-step.active .prog-icon {
            border-color: var(--success);
            color: var(--success);
            box-shadow: 0 0 0 6px var(--success-bg);
        }

        .prog-label {
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
        }

        .prog-date {
            font-size: 0.6rem;
            color: var(--text-muted);
            display: block;
            margin-top: 1px;
        }

        .filter-bar-custom {
            background: var(--card-bg);
            padding: 12px 16px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
            align-items: center;
            box-shadow: var(--shadow-sm);
        }

        .submissions-list-scroll {
            max-height: 850px;
            overflow-y: auto;
            padding-right: 12px;
            margin-right: -12px;
        }
    </style>
</head>

<body>
    <div class="app-layout">
        <?php include BASE_PATH . 'includes/sidebar.php'; ?>

        <div class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <div class="breadcrumb">
                        <h1 class="page-title">My Activity History</h1>
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
                <form method="GET" action="<?php echo PUBLIC_ROOT; ?>index.php/user/submissions-progress"
                    class="filter-bar-custom">
                    <div style="position: relative; flex: 1; min-width: 250px;">
                        <i class="bi bi-search"
                            style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                        <input type="text" name="search" class="form-control" placeholder="Search activities..."
                            value="<?php echo htmlspecialchars($filters['search']); ?>"
                            style="padding-left: 42px; height: 38px;">
                    </div>
                    <div style="width: 160px;">
                        <select name="status" class="form-control" style="height: 38px;">
                            <option value="">All Statuses</option>
                            <option value="Pending" <?php echo $filters['status'] == 'Pending' ? 'selected' : ''; ?>>
                                Pending</option>
                            <option value="Reviewed" <?php echo $filters['status'] == 'Reviewed' ? 'selected' : ''; ?>>
                                Reviewed</option>
                            <option value="Recommending" <?php echo $filters['status'] == 'Recommending' ? 'selected' : ''; ?>>Recommending</option>
                            <option value="Approved" <?php echo $filters['status'] == 'Approved' ? 'selected' : ''; ?>>
                                Approved</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="height: 38px;"><i class="bi bi-funnel"></i>
                        Apply</button>
                    <?php if ($filters['search'] || $filters['status']): ?>
                        <a href="<?php echo PUBLIC_ROOT; ?>index.php/user/submissions-progress" class="btn btn-secondary"
                            style="height: 38px; display: flex; align-items: center;">Reset</a>
                    <?php endif; ?>
                </form>

                <div class="submissions-list-scroll">
                    <?php include BASE_PATH . 'includes/functions/activity-functions.php'; ?>
                    <?php if (count($activities) > 0): ?>
                        <?php foreach ($activities as $act):
                            $prog = getProgressInfo($act);
                            $active_count = 0;
                            foreach ($prog['stages'] as $s)
                                if ($s['completed'])
                                    $active_count++;
                            $line_pct = ($active_count - 1) / (count($prog['stages']) - 1) * 100;
                            if ($line_pct < 0)
                                $line_pct = 0;
                            ?>
                            <div class="dashboard-card submission-card">
                                <div class="card-body">
                                    <div style="display: flex; justify-content: space-between;">
                                        <div>
                                            <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--primary);">
                                                <?php echo htmlspecialchars($act['title']); ?>
                                            </h3>
                                            <?php
                                            $isRelevantExpertise = strpos($act['competency'], 'Relevant Expertise') !== false;
                                            ?>
                                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">
                                                <span><i class="bi bi-geo-alt"></i>
                                                    <?php echo htmlspecialchars($act['venue']); ?>
                                                </span>
                                                <span style="margin-left: 12px;"><i class="bi bi-calendar-check"></i>
                                                    <?php echo date('M d, Y', strtotime(explode(', ', $act['date_attended'])[0])); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <?php if ($isRelevantExpertise): ?>
                                                <span class="activity-status-badge"
                                                    style="padding: 4px 12px; font-size: 0.75rem; background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe;">
                                                    <i class="bi bi-bookmark-star-fill"></i> Recorded Entry
                                                </span>
                                            <?php else: ?>
                                                <span class="activity-status-badge status-pending"
                                                    style="padding: 4px 12px; font-size: 0.75rem;">
                                                    <?php echo $act['status'] ?? 'Pending'; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if (!$isRelevantExpertise): ?>
                                        <div class="prog-track-wrapper">
                                            <div class="prog-track-line"></div>
                                            <div class="prog-track-fill" style="width: <?php echo $line_pct; ?>%;"></div>
                                            <div class="prog-steps">
                                                <?php foreach ($prog['stages'] as $stage): ?>
                                                    <div class="prog-step <?php echo $stage['completed'] ? 'active' : ''; ?>">
                                                        <div class="prog-icon"><i class="bi <?php echo $stage['icon']; ?>"></i></div>
                                                        <span class="prog-label">
                                                            <?php echo $stage['label']; ?>
                                                        </span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div
                                            style="margin-top: 16px; padding: 12px; background: #f8fafc; border-radius: 8px; font-size: 0.85rem; color: #64748b; display: flex; align-items: center; gap: 8px;">
                                            <i class="bi bi-info-circle-fill" style="color: #4338ca;"></i>
                                            <span>This activity is recorded via Relevant Expertise bypass and does not require
                                                approval.</span>
                                        </div>
                                    <?php endif; ?>

                                    <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                                        <a href="<?php echo PUBLIC_ROOT; ?>index.php/user/view-activity?id=<?php echo $act['id']; ?>"
                                            class="btn btn-secondary btn-sm">View Details</a>
                                        <?php if (!$act['reviewed_by_supervisor']): ?>
                                            <a href="<?php echo PUBLIC_ROOT; ?>index.php/user/edit-activity?id=<?php echo $act['id']; ?>"
                                                class="btn btn-primary btn-sm">Edit Record</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="dashboard-card" style="padding: 40px; text-align: center;">No activities found.</div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</body>

</html>