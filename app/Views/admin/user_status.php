<?php
// Extracted variables from $data (handled by Controller::view)
// $users, $stats, $ildn_stats, $user, $notifRepo
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Status Monitor - Admin</title>
    <?php require BASE_PATH . 'includes/admin_head.php'; ?>
    <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/admin/user_status.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="app-layout">
        <?php require BASE_PATH . 'includes/sidebar.php'; ?>

        <div class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <div class="breadcrumb">
                        <h1 class="page-title">User Network Status</h1>
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
                <!-- Premium Search Bar -->
                <div class="search-container-premium">
                    <span class="search-label-premium">Live Personnel Search</span>
                    <div class="search-input-wrapper">
                        <input type="text" id="userSearch" class="search-input-premium"
                            placeholder="Type name, office, category (OSDS, CID, SGOD) or position...">
                        <i class="bi bi-search search-icon-premium"></i>
                    </div>
                </div>


                <div class="user-grid">
                    <?php foreach ($users as $u): ?>
                        <?php
                        $u_stats = isset($stats[$u['id']]) ? $stats[$u['id']] : ['total' => 0, 'approved' => 0, 'pending' => 0];
                        $approved_pct = $u_stats['total'] > 0 ? round(($u_stats['approved'] / $u_stats['total']) * 100) : 0;
                        $pending_pct = $u_stats['total'] > 0 ? round(($u_stats['pending'] / $u_stats['total']) * 100) : 0;
                        ?>
                        <div class="user-card hover-elevate interactive-card"
                            onclick="showUserDetails(<?php echo $u['id']; ?>)"
                            data-name="<?php echo strtolower($u['full_name']); ?>"
                            data-office="<?php echo strtolower($u['office_station']); ?>"
                            data-position="<?php echo strtolower($u['position']); ?>"
                            data-office-category="<?php echo htmlspecialchars($u['office_division'] ?? 'OTHER'); ?>">

                            <div class="card-header">
                                <?php if ($u['profile_picture']): ?>
                                    <img src="<?php echo PUBLIC_ROOT . $u['profile_picture']; ?>" alt="" class="avatar">
                                <?php else: ?>
                                    <div class="avatar-placeholder">
                                        <?php echo strtoupper(substr($u['full_name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="user-details">
                                    <span class="name">
                                        <?php echo htmlspecialchars($u['full_name']); ?>
                                    </span>
                                    <span class="position text-truncate position-text">
                                        <?php echo htmlspecialchars($u['position'] ?: 'Educational Personnel'); ?>
                                    </span>
                                </div>
                                <div class="last-seen last-seen-box">
                                    <?php if ($_SESSION['role'] !== 'head_hr' && $u['latest_activity_id']): ?>
                                        <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/view-activity?id=<?php echo $u['latest_activity_id']; ?>"
                                            class="activity-link" onclick="event.stopPropagation();">
                                        <?php endif; ?>
                                        <div class="last-activity-card"
                                            style="<?php echo ($_SESSION['role'] === 'head_hr') ? 'cursor: default;' : ''; ?>">
                                            <?php if ($u['latest_activity_title']): ?>
                                                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                                                    <i class="bi bi-activity activity-icon"></i>
                                                    <span class="entry-label">Latest
                                                        Entry</span>
                                                </div>
                                                <div class="entry-title text-truncate"
                                                    title="<?php echo htmlspecialchars($u['latest_activity_title']); ?>">
                                                    <?php echo htmlspecialchars($u['latest_activity_title']); ?>
                                                </div>
                                                <div style="display: flex; align-items: center; gap: 6px;">
                                                    <span class="status-dot"></span>
                                                    <span class="time-ago">
                                                        <?php echo time_elapsed_string($u['latest_submission']); ?>
                                                    </span>
                                                </div>
                                            <?php else: ?>
                                                <div style="display: flex; align-items: center; gap: 8px; padding: 4px 0;">
                                                    <i class="bi bi-pause-circle pause-icon"></i>
                                                    <div style="display: flex; flex-direction: column;">
                                                        <span class="entry-label">Status</span>
                                                        <span class="time-ago">No
                                                            recent activity</span>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($_SESSION['role'] !== 'head_hr' && $u['latest_activity_id']): ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="stats-row">
                                <div class="stat-item">
                                    <span class="stat-val">
                                        <?php echo $u_stats['total']; ?>
                                    </span>
                                    <span class="stat-label">Total</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-val text-success">
                                        <?php echo $u_stats['approved']; ?>
                                    </span>
                                    <span class="stat-label">Approved</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-val text-warning">
                                        <?php echo $u_stats['pending']; ?>
                                    </span>
                                    <span class="stat-label">Pending</span>
                                </div>
                            </div>

                            <div class="progress-section">
                                <div class="progress-label">
                                    <span>Approval Completion</span>
                                    <span class="pct-val">
                                        <?php echo $approved_pct; ?>%
                                    </span>
                                </div>
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill green" style="width: <?php echo $approved_pct; ?>%"></div>
                                    <div class="progress-bar-fill orange" style="width: <?php echo $pending_pct; ?>%"></div>
                                </div>
                            </div>

                            <?php
                            // Get ILDN stats for this user
                            $user_ildn = isset($ildn_stats[$u['id']]) ? $ildn_stats[$u['id']] : ['total_ildns' => 0, 'unaddressed_ildns' => 0];
                            ?>

                            <?php if ($user_ildn['total_ildns'] > 0): ?>
                                <div class="learning-needs-card">
                                    <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                                        <i class="bi bi-lightbulb bulb-icon"></i>
                                        <span class="entry-label">Learning
                                            Needs</span>
                                    </div>
                                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                        <span class="need-badge-blue">
                                            <?php echo $user_ildn['total_ildns']; ?> Total
                                        </span>
                                        <?php if ($user_ildn['unaddressed_ildns'] > 0): ?>
                                            <span class="need-badge-orange">
                                                <i class="bi bi-exclamation-circle" style="font-size: 0.65rem;"></i>
                                                <?php echo $user_ildn['unaddressed_ildns']; ?> Unaddressed
                                            </span>
                                        <?php else: ?>
                                            <span class="need-badge-green">
                                                <i class="bi bi-check-circle" style="font-size: 0.65rem;"></i> All Addressed
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="meta-info">
                                <span title="Primary Office"><i class="bi bi-building me-1"></i>
                                    <?php echo htmlspecialchars($u['office_station']); ?>
                                </span>
                                <span title="Registration Date"><i class="bi bi-person-check me-1"></i>
                                    <?php echo date('M Y', strtotime($u['joined_at'])); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
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

    <!-- Personnel Detail Modal -->
    <div id="userDetailsModal" class="details-modal-overlay">
        <div class="details-modal">
            <header class="modal-header-premium">
                <div class="header-content">
                    <div id="modalAvatar" class="header-avatar"></div>
                    <div class="header-text">
                        <h2 id="modalName">---</h2>
                        <p id="modalPosition">---</p>
                    </div>
                </div>
                <button class="modal-close-btn" onclick="closeModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </header>

            <div class="modal-scroll-area">
                <div class="detail-section-title">
                    <span><i class="bi bi-graph-up-arrow"></i> Performance Metrics</span>
                </div>
                <div class="detail-stats-grid">
                    <div class="detail-stat-card">
                        <span id="statTotal" class="detail-stat-val">0</span>
                        <span class="detail-stat-label">Submissions</span>
                    </div>
                    <div class="detail-stat-card">
                        <span id="statApproved" class="detail-stat-val text-success">0</span>
                        <span class="detail-stat-label">Approved</span>
                    </div>
                    <div class="detail-stat-card">
                        <span id="statPending" class="detail-stat-val text-warning">0</span>
                        <span class="detail-stat-label">Pending</span>
                    </div>
                    <div class="detail-stat-card">
                        <span id="statRate" class="detail-stat-val pct-val">0%</span>
                        <span class="detail-stat-label">Success Rate</span>
                    </div>
                </div>

                <div class="detail-section-title">
                    <span><i class="bi bi-bar-chart"></i> Submission Frequency</span>
                    <div class="timeline-selector">
                        <button class="timeline-btn active" onclick="fetchTimeline('week')" id="btn-week">Week</button>
                        <button class="timeline-btn" onclick="fetchTimeline('month')" id="btn-month">Month</button>
                        <button class="timeline-btn" onclick="fetchTimeline('year')" id="btn-year">Year</button>
                    </div>
                </div>
                <div class="activity-frequency-hub">
                    <div id="frequencyChart" class="frequency-chart">
                        <svg id="chartSvg" class="chart-svg" viewBox="0 0 1000 120" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="areaGradient" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="var(--primary)" stop-opacity="0.3" />
                                    <stop offset="100%" stop-color="var(--primary)" stop-opacity="0" />
                                </linearGradient>
                            </defs>
                            <path id="chartArea" class="chart-path-area" d="" />
                            <path id="chartLine" class="chart-path-line" d="" />
                            <g id="chartDots"></g>
                        </svg>
                        <div id="chartLabels" class="chart-labels-container"></div>
                        <div id="chartNoData" class="no-data-msg"
                            style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100%;">
                        </div>
                    </div>
                </div>

                <div class="detail-section-title">
                    <span><i class="bi bi-file-earmark-text"></i> Activity Submissions</span>
                </div>
                <div id="modalActivityList" class="submission-grid-modal">
                    <!-- Submissions injected by JS -->
                </div>

                <div class="detail-section-title">
                    <span><i class="bi bi-patch-check"></i> Certification History</span>
                </div>
                <div id="modalCertList" class="cert-list">
                    <!-- Certs injected by JS -->
                </div>

                <div class="detail-section-title">
                    <span><i class="bi bi-lightbulb"></i> Individual Learning Needs</span>
                </div>
                <div id="modalIldnList" class="ildn-list-modal">
                    <!-- ILDNs injected by JS -->
                </div>

                <div class="detail-section-title">
                    <span><i class="bi bi-clock-history"></i> Recent Engagement Logs</span>
                </div>
                <div id="modalLogTimeline" class="log-timeline">
                    <!-- Logs injected by JS -->
                </div>
            </div>
        </div>
    </div>

    <script>
        const userRole = '<?php echo $_SESSION['role']; ?>';
        let currentUserId = null;
        let currentTimeframe = 'week';

        function showUserDetails(userId) {
            currentUserId = userId;
            currentTimeframe = 'week'; // Reset to default
            const modal = document.getElementById('userDetailsModal');

            // Show loading state or clear previous
            document.getElementById('modalName').textContent = 'Loading...';
            document.getElementById('modalPosition').textContent = 'Please wait...';
            document.getElementById('modalCertList').innerHTML = '';
            document.getElementById('modalActivityList').innerHTML = '';
            document.getElementById('modalIldnList').innerHTML = '';
            document.getElementById('modalLogTimeline').innerHTML = '';

            // Reset Chart UI
            document.getElementById('chartSvg').style.display = 'none';
            document.getElementById('chartLabels').innerHTML = '';
            document.getElementById('chartDots').innerHTML = '';
            const noData = document.getElementById('chartNoData');
            noData.style.display = 'block';
            noData.textContent = 'Loading chart...';

            // Reset buttons
            document.querySelectorAll('.timeline-btn').forEach(btn => btn.classList.remove('active'));
            const weekBtn = document.getElementById('btn-week');
            if (weekBtn) weekBtn.classList.add('active');

            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('active'), 10);

            fetchData();
        }

        function fetchTimeline(timeline) {
            currentTimeframe = timeline;

            // Update UI
            document.querySelectorAll('.timeline-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(`btn-${timeline}`).classList.add('active');

            // Show loading on chart
            document.getElementById('chartSvg').style.display = 'none';
            document.getElementById('chartLabels').innerHTML = '';
            const noData = document.getElementById('chartNoData');
            noData.style.display = 'block';
            noData.textContent = 'Loading chart...';

            fetchData(true); // partial fetch
        }

        function fetchData(partial = false) {
            if (!currentUserId) return;

            fetch(`<?php echo PUBLIC_ROOT; ?>index.php/admin/user-details?user_id=${currentUserId}&timeline=${currentTimeframe}`)
                .then(async response => {
                    const text = await response.text();
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Server response:', text);
                        throw new Error('Invalid server response: ' + text.substring(0, 100));
                    }
                })
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }

                    if (!partial) {
                        // Populate User Info
                        document.getElementById('modalName').textContent = data.user.full_name;
                        document.getElementById('modalPosition').textContent = data.user.position || 'Educational Personnel';

                        const avatarDiv = document.getElementById('modalAvatar');
                        if (data.user.profile_picture) {
                            avatarDiv.innerHTML = `<img src="<?php echo PUBLIC_ROOT; ?>${data.user.profile_picture}" style="width:100%; height:100%; border-radius:inherit; object-fit:cover;">`;
                        } else {
                            avatarDiv.textContent = data.user.full_name.charAt(0).toUpperCase();
                            avatarDiv.innerHTML = data.user.full_name.charAt(0).toUpperCase();
                        }

                        // Populate Stats
                        document.getElementById('statTotal').textContent = data.stats.total;
                        document.getElementById('statApproved').textContent = data.stats.approved;
                        document.getElementById('statPending').textContent = data.stats.pending;
                        document.getElementById('statRate').textContent = data.stats.completion_rate + '%';

                        // Populate Certificates
                        const certList = document.getElementById('modalCertList');
                        if (data.certificates.length > 0) {
                            data.certificates.forEach(c => {
                                const certContent = `
                                    <div class="cert-icon"><i class="bi bi-file-earmark-pdf"></i></div>
                                    <div class="cert-info-mini">
                                        <h4>${c.title}</h4>
                                        <p>${new Date(c.date_attended).toLocaleDateString()}</p>
                                    </div>
                                `;

                                if (userRole === 'head_hr') {
                                    certList.innerHTML += `<div class="cert-card-mini" style="cursor: default;">${certContent}</div>`;
                                } else {
                                    certList.innerHTML += `
                                        <a href="<?php echo PUBLIC_ROOT; ?>${c.certificate_path}" target="_blank" class="cert-card-mini">
                                            ${certContent}
                                        </a>
                                    `;
                                }
                            });
                        } else {
                            certList.innerHTML = '<div class="no-data-msg" style="grid-column: 1/-1;">No certificates uploaded yet.</div>';
                        }

                        // Populate Submissions
                        const submissionList = document.getElementById('modalActivityList');
                        if (data.submissions && data.submissions.length > 0) {
                            data.submissions.forEach(s => {
                                let statusTag = '';
                                if (s.approved_sds == 1) statusTag = '<span class="submission-status-tag status-tag-approved">Approved</span>';
                                else if (s.recommending_asds == 1) statusTag = '<span class="submission-status-tag status-tag-recommending">Recommending</span>';
                                else if (s.reviewed_by_supervisor == 1) statusTag = '<span class="submission-status-tag status-tag-reviewed">Reviewed</span>';
                                else statusTag = '<span class="submission-status-tag status-tag-pending">Pending</span>';

                                const isRelevantExpertise = s.competency && s.competency.includes('Relevant Expertise');
                                const cardStyle = isRelevantExpertise ? 'border: 1px solid #c7d2fe; position: relative;' : '';
                                const expertBadge = isRelevantExpertise ? `
                                    <span style="display: inline-flex; align-items: center; gap: 4px; background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 4px; font-size: 0.6rem; font-weight: 800; text-transform: uppercase; margin-top: 4px;">
                                        <i class="bi bi-bookmark-star-fill"></i> RECORDED ENTRY
                                    </span>
                                ` : '';

                                const submissionContent = `
                                    <div class="submission-icon"><i class="bi bi-file-earmark-text"></i></div>
                                    <div class="submission-info-mini">
                                        <h4>${s.title}</h4>
                                        <p>${s.type_ld}</p>
                                        <div style="display: flex; flex-wrap: wrap; gap: 4px; align-items: center;">
                                            ${statusTag}
                                            ${expertBadge}
                                        </div>
                                    </div>
                                `;

                                if (userRole === 'head_hr') {
                                    submissionList.innerHTML += `<div class="submission-card-mini" style="cursor: default; ${cardStyle}">${submissionContent}</div>`;
                                } else {
                                    submissionList.innerHTML += `
                                        <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/view-activity?id=${s.id}" class="submission-card-mini" style="${cardStyle}">
                                            ${submissionContent}
                                        </a>
                                    `;
                                }
                            });
                        } else {
                            submissionList.innerHTML = '<div class="no-data-msg" style="grid-column: 1/-1;">No submissions found.</div>';
                        }

                        // Populate ILDNs
                        const ildnList = document.getElementById('modalIldnList');
                        if (data.ildns && data.ildns.length > 0) {
                            data.ildns.forEach(i => {
                                const isAddressed = parseInt(i.usage_count) > 0;
                                const statusClass = isAddressed ? 'ildn-badge-addressed' : 'ildn-badge-unaddressed';
                                const statusText = isAddressed ? 'Addressed' : 'Unaddressed';
                                const iconClass = isAddressed ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill';

                                ildnList.innerHTML += `
                                    <div class="ildn-card-mini">
                                        <div class="ildn-icon-mini"><i class="bi bi-lightbulb"></i></div>
                                        <div class="ildn-info-mini">
                                            <h4>${i.need_text}</h4>
                                            ${i.description ? `<p>${i.description}</p>` : ''}
                                            <div style="margin-top: 6px;">
                                                <span class="ildn-status-badge ${statusClass}">
                                                    <i class="bi ${iconClass}"></i> ${statusText}
                                                </span>
                                                <span style="font-size: 0.6rem; color: #94a3b8; margin-left: 8px;">
                                                    Used in ${i.usage_count} activities
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            ildnList.innerHTML = '<div class="no-data-msg">No learning needs listed yet.</div>';
                        }

                        // Populate Logs
                        const timeline = document.getElementById('modalLogTimeline');
                        if (data.logs.length > 0) {
                            data.logs.forEach(l => {
                                timeline.innerHTML += `
                                    <div class="log-entry">
                                        <div class="log-dot"></div>
                                        <div class="log-content">
                                            <h5>${l.action}</h5>
                                            <span>${new Date(l.created_at).toLocaleString()}</span>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            timeline.innerHTML = '<div class="no-data-msg">No logs available.</div>';
                        }
                    }

                    // Always Populate Chart
                    const chartSvg = document.getElementById('chartSvg');
                    const chartLabels = document.getElementById('chartLabels');
                    const chartLine = document.getElementById('chartLine');
                    const chartArea = document.getElementById('chartArea');
                    const chartDots = document.getElementById('chartDots');
                    const chartNoData = document.getElementById('chartNoData');

                    chartLabels.innerHTML = '';
                    chartDots.innerHTML = '';
                    chartLine.setAttribute('d', '');
                    chartArea.setAttribute('d', '');
                    chartNoData.style.display = 'none';
                    chartSvg.style.display = 'block';

                    if (data.activity_data && data.activity_data.length > 0) {
                        const items = data.activity_data;
                        const maxCount = Math.max(...items.map(m => m.count), 1);
                        const points = [];
                        const width = 1000;
                        const height = 120;

                        items.forEach((m, i) => {
                            const x = items.length > 1 ? (i / (items.length - 1)) * width : width / 2;
                            const y = height - (m.count / maxCount) * (height - 20) - 10;
                            points.push({ x, y, label: m.label, count: m.count });

                            chartLabels.innerHTML += `<span class="frequency-label">${m.label}</span>`;
                            chartDots.innerHTML += `<circle cx="${x}" cy="${y}" class="chart-dot" title="${m.count} submissions"><title>${m.label}: ${m.count} submissions</title></circle>`;
                        });

                        if (points.length > 1) {
                            // Flowing Line Calculation (Cubic Bezier)
                            let d = `M ${points[0].x},${points[0].y}`;
                            for (let i = 0; i < points.length - 1; i++) {
                                const p0 = points[i];
                                const p1 = points[i + 1];
                                const cp1x = p0.x + (p1.x - p0.x) / 2;
                                d += ` C ${cp1x},${p0.y} ${cp1x},${p1.y} ${p1.x},${p1.y}`;
                            }
                            chartLine.setAttribute('d', d);

                            // Area path
                            const areaD = d + ` L ${points[points.length - 1].x},${height} L ${points[0].x},${height} Z`;
                            chartArea.setAttribute('d', areaD);
                        } else if (points.length === 1) {
                            const p = points[0];
                            chartLine.setAttribute('d', `M 0,${p.y} L ${width},${p.y}`);
                            chartArea.setAttribute('d', `M 0,${p.y} L ${width},${p.y} L ${width},${height} L 0,${height} Z`);
                        }
                    } else {
                        chartSvg.style.display = 'none';
                        chartNoData.textContent = `No activity recorded for this ${currentTimeframe}ly view.`;
                        chartNoData.style.display = 'block';
                    }
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    if (typeof showToast === 'function') {
                        showToast("Access Restricted", err.message, "warning");
                    }
                    closeModal();
                });
        }

        function closeModal() {
            const modal = document.getElementById('userDetailsModal');
            modal.classList.remove('active');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        window.onclick = function (event) {
            const modal = document.getElementById('userDetailsModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Real-time Search Logic
            const searchInput = document.getElementById('userSearch');
            const userCards = document.querySelectorAll('.user-card');

            // Search Input Logic
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const searchTerm = this.value.toLowerCase();

                    userCards.forEach(card => {
                        const name = card.dataset.name;
                        const office = card.dataset.office;
                        const position = card.dataset.position;
                        const officeCategory = card.dataset.officeCategory.toLowerCase();

                        const matchesSearch = !searchTerm ||
                            name.includes(searchTerm) ||
                            office.includes(searchTerm) ||
                            position.includes(searchTerm) ||
                            officeCategory.includes(searchTerm);

                        if (matchesSearch) {
                            card.style.display = 'block';
                            card.style.opacity = '1';
                        } else {
                            card.style.display = 'none';
                            card.style.opacity = '0';
                        }
                    });
                });
            }
        });
    </script>
</body>

</html>