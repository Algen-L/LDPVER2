<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Activity Details - LDP</title>
    <?php include BASE_PATH . 'includes/head.php'; ?>
    <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/user/view_activity.css?v=<?php echo time(); ?>">
    <style>
        .view-prog-fill {
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
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
                        <a href="<?php echo PUBLIC_ROOT; ?>index.php/user/submissions-progress"
                            class="breadcrumb-item">History</a>
                        <span class="breadcrumb-separator"><i class="bi bi-chevron-right"></i></span>
                        <span class="breadcrumb-item active">View Details</span>
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
                <!-- Branded Header for Print -->
                <div class="print-only-header">
                    <img src="<?php echo PUBLIC_ROOT; ?>assets/LogoLDP.png" alt="LDP Logo" class="print-logo">
                    <div class="print-title-group">
                        <h1>Learning & Development Passbook</h1>
                        <p>Schools Division Office</p>
                    </div>
                </div>

                <div class="view-layout-container">

                    <!-- Progress Timeline -->
                    <?php
                    $role = $_SESSION['role'] ?? 'user';
                    $next_stage = '';
                    $can_interact = false;

                    if (!$activity['reviewed_by_supervisor']) {
                        $next_stage = 'Supervisor Review';
                        $can_interact = in_array($role, ['admin', 'super_admin', 'head_hr', 'immediate_head']);
                    } elseif (!$activity['recommending_asds']) {
                        $next_stage = 'SDO Recommendation';
                        $can_interact = in_array($role, ['admin', 'super_admin']);
                    } elseif (!$activity['approved_sds']) {
                        $next_stage = 'Final Approval';
                        $can_interact = ($role === 'immediate_head');
                    }
                    ?>
                    <div class="view-prog-track <?php echo ($can_interact && $next_stage) ? 'can-interact' : ''; ?>"
                        <?php if ($can_interact && $next_stage): ?>onclick="openApprovalModal()" <?php endif; ?>>
                        <div class="view-prog-steps">
                            <div class="view-prog-line"></div>
                            <?php
                            $stages = [
                                ['label' => 'Submitted', 'field' => 'created_at', 'active' => true],
                                ['label' => 'Reviewed', 'field' => 'reviewed_at', 'active' => (bool) $activity['reviewed_by_supervisor']],
                                ['label' => 'Recommended', 'field' => 'recommended_at', 'active' => (bool) $activity['recommending_asds']],
                                ['label' => 'Approved', 'field' => 'approved_at', 'active' => (bool) $activity['approved_sds']]
                            ];
                            $active_count = 0;
                            foreach ($stages as $s)
                                if ($s['active'])
                                    $active_count++;
                            $fill_pct = ($active_count - 1) / (count($stages) - 1) * 100;
                            ?>
                            <div class="view-prog-fill" style="width: <?php echo $fill_pct; ?>%;"></div>

                            <?php foreach ($stages as $stage): ?>
                                <div class="view-prog-step <?php echo $stage['active'] ? 'active' : ''; ?>">
                                    <div class="view-prog-icon">
                                        <i class="bi bi-check2"></i>
                                    </div>
                                    <div class="view-prog-text">
                                        <span class="view-prog-label"><?php echo $stage['label']; ?></span>
                                        <span class="view-prog-date">
                                            <?php echo $activity[$stage['field']] ? date('M d, Y', strtotime($activity[$stage['field']])) : 'Pending'; ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="submitter-hero">
                        <?php if (!empty($activity['profile_picture'])): ?>
                            <img src="<?php echo PUBLIC_ROOT . htmlspecialchars($activity['profile_picture']); ?>"
                                class="submitter-avatar">
                        <?php else: ?>
                            <div class="submitter-avatar"
                                style="background: var(--bg-tertiary); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; color: var(--text-muted);">
                                <?php echo strtoupper(substr($activity['full_name'] ?? 'U', 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        <div class="submitter-info">
                            <p>Activity Submitted By</p>
                            <h2><?php echo htmlspecialchars($activity['full_name'] ?? 'Unknown User'); ?></h2>
                            <div
                                style="display: flex; gap: 16px; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary);">
                                <span><i class="bi bi-building"></i>
                                    <?php echo htmlspecialchars($activity['office_station'] ?? 'N/A'); ?></span>
                                <span><i class="bi bi-briefcase"></i>
                                    <?php echo htmlspecialchars($activity['user_position'] ?? 'Employee'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Main Activity Details Card -->
                    <div class="dashboard-card" style="margin-bottom: 40px;">
                        <div class="card-body" style="padding: 40px;">
                            <?php
                            $printStatus = 'PENDING';
                            if ($activity['approved_sds'])
                                $printStatus = 'APPROVED';
                            elseif ($activity['recommending_asds'])
                                $printStatus = 'RECOMMENDED';
                            elseif ($activity['reviewed_by_supervisor'])
                                $printStatus = 'REVIEWED';
                            ?>
                            <div class="print-status-header">
                                STATUS: <?php echo $printStatus; ?>
                            </div>

                            <div class="data-section-title"><i class="bi bi-book"></i> Activity Details</div>

                            <h2
                                style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin-bottom: 24px;">
                                <?php echo htmlspecialchars($activity['title']); ?>
                            </h2>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 40px;">
                                <div class="form-group">
                                    <label class="form-label">Date(s) of Attendance</label>
                                    <div class="form-control"
                                        style="background: var(--bg-secondary); font-weight: 600; height: auto; min-height: 48px;">
                                        <?php
                                        $dates = explode(', ', $activity['date_attended']);
                                        $formattedDates = array_map(function ($d) {
                                            return @strtotime($d) ? date('M d, Y', strtotime($d)) : $d;
                                        }, $dates);
                                        echo implode(' | ', $formattedDates);
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Venue</label>
                                    <div class="form-control"
                                        style="background: var(--bg-secondary); font-weight: 600;">
                                        <?php echo htmlspecialchars($activity['venue'] ?: 'Not Specified'); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Competencies Addressed</label>
                                    <div class="form-control"
                                        style="background: var(--bg-secondary); font-weight: 600;">
                                        <?php echo htmlspecialchars($activity['competency']); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Conducted By</label>
                                    <div class="form-control"
                                        style="background: var(--bg-secondary); font-weight: 600;">
                                        <?php echo htmlspecialchars($activity['conducted_by']); ?>
                                    </div>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 40px;">
                                <div>
                                    <label class="form-label">Modalities</label>
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                        <?php
                                        $mods = explode(', ', $activity['modality']);
                                        foreach ($mods as $m):
                                            if (!$m)
                                                continue; ?>
                                            <span class="activity-status-badge status-recommending"
                                                style="padding: 6px 16px; border-radius: 30px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; display: inline-flex; align-items: center; gap: 4px; letter-spacing: 0.02em; background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa;"><?php echo $m; ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">Type of L&D</label>
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                        <?php
                                        $types = explode(', ', $activity['type_ld']);
                                        foreach ($types as $t):
                                            if (!$t)
                                                continue; ?>
                                            <span class="activity-status-badge status-reviewed"
                                                style="padding: 6px 16px; border-radius: 30px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; display: inline-flex; align-items: center; gap: 4px; letter-spacing: 0.02em; background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;"><?php echo $t; ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="data-section-title"><i class="bi bi-rocket-takeoff"></i> Workplace Application
                                Plan</div>



                            <?php if (!empty($activity['workplace_image_path'])): ?>
                                <div class="form-group" style="margin-bottom: 40px;">
                                    <label class="form-label">EVIDENCE / ATTACHMENTS</label>
                                    <div style="display: flex; flex-wrap: wrap; gap: 16px;">

                                        <?php
                                        $paths = [];
                                        $trimmed = trim($activity['workplace_image_path'] ?? '');
                                        if (strpos($trimmed, '[') === 0)
                                            $paths = json_decode($trimmed, true) ?: [];
                                        elseif (!empty($trimmed))
                                            $paths = [$trimmed];

                                        foreach ($paths as $path):
                                            if (empty($path))
                                                continue;
                                            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                            $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']); ?>
                                            <div class="image-attachment">
                                                <?php if ($isImg): ?>
                                                    <a href="<?php echo PUBLIC_ROOT . htmlspecialchars($path); ?>" target="_blank">
                                                        <img src="<?php echo PUBLIC_ROOT . htmlspecialchars($path); ?>"
                                                            style="width: 140px; height: 140px; object-fit: cover; border-radius: var(--radius-sm);">
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?php echo PUBLIC_ROOT . htmlspecialchars($path); ?>" target="_blank"
                                                        style="width: 140px; height: 140px; display: flex; align-items: center; justify-content: center; background: var(--bg-tertiary); border-radius: var(--radius-sm); text-decoration: none; color: var(--primary);">
                                                        <i class="bi bi-file-earmark-pdf" style="font-size: 3rem;"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="data-section-title"><i class="bi bi-lightbulb"></i> Application of Learning
                            </div>

                            <div class="form-group" style="margin-bottom: 32px;">
                                <label class="form-label">SUPPORTING DOCUMENT</label>
                                <?php if (!empty($activity['application_file_path'])): ?>
                                    <div class="image-attachment" style="display: inline-block;">
                                        <a href="<?php echo PUBLIC_ROOT . htmlspecialchars($activity['application_file_path']); ?>"
                                            target="_blank"
                                            style="display: flex; align-items: center; gap: 12px; padding: 12px 20px; background: #e0f2fe; border: 1px solid #7dd3fc; border-radius: 10px; text-decoration: none; color: #0284c7; transition: all 0.2s;">
                                            <i class="bi bi-file-earmark-text-fill" style="font-size: 1.5rem;"></i>
                                            <div style="display: flex; flex-direction: column;">
                                                <span style="font-weight: 700; font-size: 0.9rem;">View Supporting
                                                    Document</span>
                                                <span style="font-size: 0.75rem; opacity: 0.8;">Click to open file</span>
                                            </div>
                                            <i class="bi bi-box-arrow-up-right" style="margin-left: 8px;"></i>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div
                                        style="color: var(--text-muted); font-style: italic; padding: 15px; background: var(--bg-secondary); border-radius: var(--radius-sm);">
                                        No application document provided.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="print-page-break">
                                <div class="data-section-title"><i class="bi bi-journal-text"></i> Personal Reflection
                                </div>

                                <div class="form-group" style="margin-bottom: 32px;">
                                    <label class="form-label">PERSONAL REFLECTION</label>
                                    <div
                                        style="line-height: 1.7; color: var(--text-secondary); background: var(--bg-secondary); padding: 24px; border-radius: var(--radius-lg);">
                                        <?php echo nl2br(htmlspecialchars($activity['reflection'])); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="data-section-title"><i class="bi bi-award"></i> Certificate of Participation
                            </div>

                            <div class="form-group" style="margin-bottom: 32px;">
                                <?php if ($activity['certificate_path']): ?>
                                    <div class="image-attachment" style="display: inline-block;">
                                        <a href="<?php echo PUBLIC_ROOT . htmlspecialchars($activity['certificate_path']); ?>"
                                            target="_blank"
                                            style="width: 160px; height: 160px; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: var(--radius-md); text-decoration: none; color: #16a34a; transition: transform 0.2s;">
                                            <i class="bi bi-patch-check-fill" style="font-size: 3rem;"></i>
                                            <span style="font-size: 0.8rem; font-weight: 700; margin-top: 12px;">View
                                                Certificate</span>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div
                                        style="padding: 24px; background: var(--bg-secondary); border-radius: var(--radius-md); color: var(--text-muted); font-style: italic;">
                                        No certificate attached for this activity.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="data-section-title"><i class="bi bi-pen"></i> Signatures & Authorization</div>

                            <div class="signatures-grid"
                                style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 32px;">
                                <div class="signature-box" style="text-align: center;">
                                    <?php if ($activity['recommending_asds'] && !empty($activity['organizer_signature_path'])): ?>
                                        <p
                                            style="font-size: 0.75rem; font-weight: 800; color: var(--text-primary); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.3px;">
                                            Good As Recommended by this
                                            <?php echo date('M d, Y', strtotime($activity['recommended_at'])); ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="signature-line"
                                        style="height: 120px; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid var(--text-primary); margin-bottom: 12px;">
                                        <?php if (!empty($activity['organizer_signature_path'])): ?>
                                            <img src="<?php echo PUBLIC_ROOT . htmlspecialchars($activity['organizer_signature_path']); ?>"
                                                class="signature-img" style="max-height: 100px; filter: contrast(1.2);">
                                        <?php else: ?>
                                            <span style="color: var(--text-muted); font-style: italic;">No signature
                                                provided</span>
                                        <?php endif; ?>
                                    </div>
                                    <p style="font-size: 0.75rem; color: var(--text-muted);">Human Resource Training
                                        Officer</p>
                                </div>
                                <div class="signature-box" style="text-align: center;">
                                    <?php if ($activity['approved_sds'] && !empty($activity['signature_path'])): ?>
                                        <p
                                            style="font-size: 0.75rem; font-weight: 800; color: var(--text-primary); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.3px;">
                                            Good As Approved by this
                                            <?php echo date('M d, Y', strtotime($activity['approved_at'])); ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="signature-line"
                                        style="height: 120px; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid var(--text-primary); margin-bottom: 12px;">
                                        <?php if (!empty($activity['signature_path'])): ?>
                                            <img src="<?php echo PUBLIC_ROOT . htmlspecialchars($activity['signature_path']); ?>"
                                                class="signature-img" style="max-height: 100px; filter: contrast(1.2);">
                                        <?php else: ?>
                                            <span style="color: var(--text-muted); font-style: italic;">No signature
                                                provided</span>
                                        <?php endif; ?>
                                    </div>
                                    <p style="font-weight: 800; text-transform: uppercase; font-size: 0.9rem;">
                                        <?php echo htmlspecialchars($activity['approved_by'] ?? 'Head of Office'); ?>
                                    </p>
                                    <p style="font-size: 0.75rem; color: var(--text-muted);">IMMEDIATE HEAD</p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Print Button at Bottom -->
                    <div style="text-align: center; margin-top: 40px; margin-bottom: 60px;" class="btn-print-hide">
                        <button onclick="window.print()" class="btn btn-primary btn-lg"
                            style="padding: 12px 32px; font-weight: 800; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 4px 6px rgba(15, 76, 117, 0.2);">
                            <i class="bi bi-printer-fill"></i> PRINT ACTIVITY RECORD
                        </button>
                    </div>
                </div>
            </main>

            <footer class="user-footer btn-print-hide">
                <p>&copy; <?php echo date('Y'); ?> SDO L&D Passbook System. <span class="text-muted">Developed by Algen
                        D. Loveres and Cedrick V. Bacaresas</span></p>
            </footer>
        </div>
    </div>

    <!-- Approval Modal (Re-implementation if needed by supervisor) -->
    <div id="approvalModal" class="approval-modal">
        <div class="approval-modal-content">
            <div class="modal-header">
                <h5 style="margin:0; font-weight:800; color:var(--primary);">
                    <i class="bi bi-shield-check"></i> <span id="modalStageTitle">Approval Action</span>
                </h5>
                <button type="button" class="btn-close" onclick="closeApprovalModal()"
                    style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted);">&times;</button>
            </div>
            <div class="modal-body" id="modalStageContent">
                <!-- Dynamic Content Loaded by JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeApprovalModal()">Cancel</button>
                <button type="button" id="modalSubmitBtn" class="btn btn-primary btn-sm">Submit
                    Action</button>
            </div>
        </div>
    </div>

    <script>
        // Signature Pad Logic
        function initSignaturePad(canvasId) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return null;
            const ctx = canvas.getContext('2d');
            let drawing = false;
            const dpr = window.devicePixelRatio || 1;
            const rect = canvas.getBoundingClientRect();
            canvas.width = rect.width * dpr;
            canvas.height = rect.height * dpr;
            ctx.scale(dpr, dpr);
            canvas.style.width = rect.width + 'px';
            canvas.style.height = rect.height + 'px';

            function getPos(e) {
                const r = canvas.getBoundingClientRect();
                return {
                    x: (e.clientX || (e.touches && e.touches[0].clientX)) - r.left,
                    y: (e.clientY || (e.touches && e.touches[0].clientY)) - r.top
                };
            }
            function start(e) { drawing = true; ctx.beginPath(); ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.strokeStyle = '#000'; const pos = getPos(e); ctx.moveTo(pos.x, pos.y); if (e.type === 'touchstart') e.preventDefault(); }
            function move(e) { if (!drawing) return; const pos = getPos(e); ctx.lineTo(pos.x, pos.y); ctx.stroke(); if (e.type === 'touchmove') e.preventDefault(); }
            function stop() { drawing = false; }
            canvas.addEventListener('mousedown', start);
            canvas.addEventListener('mousemove', move);
            window.addEventListener('mouseup', stop);
            canvas.addEventListener('touchstart', start);
            canvas.addEventListener('touchmove', move);
            canvas.addEventListener('touchend', stop);
            return {
                clear: () => ctx.clearRect(0, 0, canvas.width, canvas.height),
                isEmpty: () => { const pixels = ctx.getImageData(0, 0, canvas.width, canvas.height).data; return !Array.from(pixels).some(p => p !== 0); },
                getData: () => canvas.toDataURL()
            };
        }

        const modal = document.getElementById('approvalModal');
        const modalTitle = document.getElementById('modalStageTitle');
        const modalContent = document.getElementById('modalStageContent');
        const modalSubmitBtn = document.getElementById('modalSubmitBtn');

        window.openApprovalModal = function () {
            const nextStage = "<?php echo $next_stage; ?>";
            modal.style.display = 'flex';
            // Implementation of approval forms... (can be added if user role allows)
        }
        window.closeApprovalModal = function () { modal.style.display = 'none'; }
    </script>
</body>

</html>