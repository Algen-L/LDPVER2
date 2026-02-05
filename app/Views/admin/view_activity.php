<?php
// Extracted variables from $data (handled by Controller::view)
// $activity, $user, $notifRepo, $pdo
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Activity Details - Admin</title>
    <?php require BASE_PATH . 'includes/admin_head.php'; ?>
    <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/admin/view_activity.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="app-layout">
        <?php require BASE_PATH . 'includes/sidebar.php'; ?>

        <div class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <div class="breadcrumb">
                        <h1 class="page-title">Activity Details</h1>
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
                <div class="print-only-header">
                    <img src="<?php echo PUBLIC_ROOT; ?>assets/LogoLDP.png" alt="LDP Logo" class="print-logo">
                    <div>
                        <h1 class="print-title">
                            Learning & Development Passbook</h1>
                        <p class="print-subtitle">Schools Division
                            Office</p>
                    </div>
                </div>

                <div class="view-layout-container">
                    <?php
                    $role = $_SESSION['role'];
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

                    <div id="approvalModal" class="approval-modal">
                        <div class="approval-modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title-text"><i class="bi bi-shield-check"></i> <span
                                        id="modalStageTitle">Approval
                                        Action</span></h5>
                                <button type="button" onclick="closeApprovalModal()"
                                    class="close-btn-style">&times;</button>
                            </div>
                            <div class="modal-body" id="modalStageContent"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm"
                                    onclick="closeApprovalModal()">Cancel</button>
                                <button type="button" id="modalSubmitBtn" class="btn btn-primary btn-sm">Submit
                                    Action</button>
                            </div>
                        </div>
                    </div>

                    <div class="submitter-hero">
                        <?php if (!empty($activity['profile_picture'])): ?>
                            <img src="<?php echo PUBLIC_ROOT . $activity['profile_picture']; ?>" class="submitter-avatar">
                        <?php else: ?>
                            <div class="submitter-avatar-placeholder">
                                <?php echo strtoupper(substr($activity['full_name'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        <div class="submitter-info">
                            <p>Activity Submitted By</p>
                            <h2>
                                <?php echo htmlspecialchars($activity['full_name']); ?>
                            </h2>
                            <div class="hero-meta">
                                <span><i class="bi bi-building"></i>
                                    <?php echo htmlspecialchars($activity['office_station']); ?>
                                </span>
                                <span><i class="bi bi-briefcase"></i>
                                    <?php echo htmlspecialchars($activity['user_position'] ?: 'Employee'); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card mb-40">
                        <div class="card-body p-40">
                            <?php
                            $statusClass = 'status-badge-pending';
                            $printStatus = 'PENDING';
                            if ($activity['approved_sds']) {
                                $printStatus = 'APPROVED';
                                $statusClass = 'status-badge-approved';
                            } elseif ($activity['recommending_asds']) {
                                $printStatus = 'RECOMMENDED';
                                $statusClass = 'status-badge-recommended';
                            } elseif ($activity['reviewed_by_supervisor']) {
                                $printStatus = 'REVIEWED';
                                $statusClass = 'status-badge-reviewed';
                            }
                            ?>
                            <div class="ui-status-badge <?php echo $statusClass; ?>">
                                STATUS: <?php echo $printStatus; ?>
                            </div>

                            <div class="data-section-title"><i class="bi bi-book"></i> Activity Details</div>
                            <h2 class="activity-title-view">
                                <?php echo htmlspecialchars($activity['title']); ?>
                            </h2>

                            <div class="details-grid">
                                <div class="form-group">
                                    <label class="form-label">Date(s) of Attendance</label>
                                    <div class="form-control form-control-static-long">
                                        <?php
                                        $dates = explode(', ', $activity['date_attended']);
                                        $formattedDates = array_map(function ($d) {
                                            return date('M d, Y', strtotime($d));
                                        }, $dates);
                                        echo implode(' | ', $formattedDates);
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Venue</label>
                                    <div class="form-control form-control-static">
                                        <?php echo htmlspecialchars($activity['venue'] ?: 'Not Specified'); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Competencies Addressed</label>
                                    <div class="form-control form-control-static">
                                        <?php echo htmlspecialchars($activity['competency']); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Conducted By</label>
                                    <div class="form-control form-control-static">
                                        <?php echo htmlspecialchars($activity['conducted_by']); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="details-grid">
                                <div>
                                    <label class="form-label">Modalities</label>
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                        <?php
                                        $mods = explode(', ', $activity['modality']);
                                        foreach ($mods as $m):
                                            if (!$m)
                                                continue; ?>
                                            <span class="activity-status-badge status-recommending"><?php echo $m; ?></span>
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
                                            <span class="activity-status-badge status-reviewed"><?php echo $t; ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="data-section-title"><i class="bi bi-rocket-takeoff"></i> Workplace Application
                                Plan</div>
                            <?php if (!empty($activity['workplace_image_path'])): ?>
                                <div class="form-group mb-40">
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
                                                    <a href="<?php echo PUBLIC_ROOT . $path; ?>" target="_blank">
                                                        <img src="<?php echo PUBLIC_ROOT . $path; ?>"
                                                            style="width: 140px; height: 140px; object-fit: cover; border-radius: var(--radius-sm);">
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?php echo PUBLIC_ROOT . $path; ?>" target="_blank"
                                                        class="pdf-attachment">
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
                            <div class="form-group">
                                <label class="form-label">SUPPORTING DOCUMENT</label>
                                <div class="data-section-content">
                                    <?php if ($activity['application_file_path']): ?>
                                        <a href="<?php echo PUBLIC_ROOT . $activity['application_file_path']; ?>"
                                            target="_blank" class="doc-link">
                                            <i class="bi bi-file-earmark-text-fill doc-icon-size"></i>
                                            <div class="flex-col">
                                                <span class="doc-label">View Supporting Document</span>
                                                <span class="doc-hint">Click to open file</span>
                                            </div>
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #64748b; font-style: italic;">No application document
                                            provided.</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="print-page-break">
                                <div class="data-section-title"><i class="bi bi-journal-text"></i> Personal Reflection
                                </div>
                                <div class="form-group">
                                    <label class="form-label">PERSONAL REFLECTION</label>
                                    <div class="data-section-content">
                                        <?php echo nl2br(htmlspecialchars($activity['reflection'] ?? '')); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="data-section-title"><i class="bi bi-award"></i> Certificate of Participation
                            </div>
                            <div class="cert-card-view">
                                <?php if ($activity['certificate_path']): ?>
                                    <a href="<?php echo PUBLIC_ROOT . $activity['certificate_path']; ?>" target="_blank"
                                        class="cert-preview-box">
                                        <div class="cert-check-icon">
                                            <i class="bi bi-check2"></i>
                                        </div>
                                        <span class="cert-label-view" style="font-weight: 800; color: #16a34a;">View
                                            Certificate</span>
                                    </a>
                                <?php else: ?>
                                    <div class="no-cert-box">
                                        No certificate attached.</div>
                                <?php endif; ?>
                            </div>

                            <div class="data-section-title"><i class="bi bi-pen"></i> Signatures & Authorization</div>
                            <div class="signatures-grid-view">
                                <div class="sig-column">
                                    <div class="sig-label-box">
                                        <?php if ($activity['recommending_asds']): ?>
                                            <p class="sig-status-text">
                                                GOOD AS RECOMMENDED BY THIS
                                                <?php echo $activity['recommended_at'] ? date('M d, Y', strtotime($activity['recommended_at'])) : date('M d, Y'); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="sig-container-view">
                                        <?php if (!empty($activity['organizer_signature_path'])): ?>
                                            <img src="<?php echo PUBLIC_ROOT . $activity['organizer_signature_path']; ?>"
                                                class="sig-img-view">
                                        <?php endif; ?>
                                    </div>
                                    <div class="sig-thick-line"></div>
                                    <div class="text-center">
                                        <p class="head-name">
                                            <?php echo htmlspecialchars($activity['conducted_by'] ?: ($hr_name ?? 'HR OFFICER')); ?>
                                        </p>
                                        <p class="head-role">Human Resource Training Officer</p>
                                    </div>
                                </div>
                                <div class="sig-column">
                                    <div class="sig-label-box">
                                        <?php if ($activity['approved_sds']): ?>
                                            <p class="sig-status-text">
                                                GOOD AS APPROVED BY THIS
                                                <?php echo $activity['approved_at'] ? date('M d, Y', strtotime($activity['approved_at'])) : date('M d, Y'); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="sig-container-view">
                                        <?php if (!empty($activity['signature_path'])): ?>
                                            <img src="<?php echo PUBLIC_ROOT . $activity['signature_path']; ?>"
                                                class="sig-img-view">
                                        <?php endif; ?>
                                    </div>
                                    <div class="sig-thick-line"></div>
                                    <div class="text-center">
                                        <p class="head-name">
                                            <?php echo htmlspecialchars($activity['approved_by'] ?: ($sds_name ?? 'SDS')); ?>
                                        </p>
                                        <p class="head-role">Office of the Schools Division Superintendent</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="footer-actions btn-print-hide">
                        <button onclick="window.print()" class="btn btn-primary btn-lg btn-print-action">
                            <i class="bi bi-printer-fill"></i> PRINT ACTIVITY RECORD</button>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
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
            canvas.addEventListener('mousedown', start); canvas.addEventListener('mousemove', move); window.addEventListener('mouseup', stop);
            canvas.addEventListener('touchstart', start); canvas.addEventListener('touchmove', move); canvas.addEventListener('touchend', stop);

            return {
                clear: () => ctx.clearRect(0, 0, canvas.width, canvas.height),
                isEmpty: () => {
                    const pixels = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
                    return !Array.from(pixels).some(p => p !== 0);
                },
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
            if (nextStage === 'Supervisor Review') {
                modalTitle.innerText = 'Supervisor Review';
                modalContent.innerHTML = `<p>Are you sure you want to verify this activity's documentation and details?</p><form id="modal-review-form" method="POST"><input type="hidden" name="action_approval" value="1"><input type="hidden" name="stage" value="supervisor"></form>`;
                modalSubmitBtn.onclick = () => document.getElementById('modal-review-form').submit();
            } else if (nextStage === 'SDO Recommendation') {
                modalTitle.innerText = 'SDO Recommendation';
                modalContent.innerHTML = `
                    <form id="modal-recommend-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action_approval" value="1">
                        <input type="hidden" name="stage" value="asds">
                        <input type="hidden" name="organizer_signature_data" id="organizer_signature_data_modal">
                        <input type="hidden" name="conducted_by" value="<?php echo htmlspecialchars($user['full_name']); ?>">
                        <div class="mb-3">
                            <label class="form-label">Upload Signature (Optional)</label>
                            <input type="file" name="organizer_sig_file" id="organizer_sig_file" class="form-control" accept="image/*">
                        </div>
                        <label class="form-label">Draw Signature</label>
                        <div class="signature-pad-container">
                            <canvas id="org-sig-canvas-modal" class="sig-canvas"></canvas>
                            <button type="button" class="btn-clear" id="clear-org-modal">Clear Pad</button>
                        </div>
                    </form>`;
                setTimeout(() => {
                    const pad = initSignaturePad('org-sig-canvas-modal');
                    document.getElementById('clear-org-modal').onclick = () => pad.clear();
                    modalSubmitBtn.onclick = () => {
                        if (!pad.isEmpty()) document.getElementById('organizer_signature_data_modal').value = pad.getData();
                        document.getElementById('modal-recommend-form').submit();
                    };
                }, 100);
            } else if (nextStage === 'Final Approval') {
                modalTitle.innerText = 'Final Approval';
                modalContent.innerHTML = `
                    <form id="modal-approval-form" method="POST">
                        <input type="hidden" name="action_approval" value="1">
                        <input type="hidden" name="stage" value="sds">
                        <input type="hidden" name="signature_data" id="signature_data_modal">
                        <div class="mb-3">
                            <label class="form-label">Immediate Head Name</label>
                            <input type="text" name="approved_by" class="form-control" required placeholder="Full Name">
                        </div>
                        <label class="form-label">Your Signature</label>
                        <div class="signature-pad-container">
                            <canvas id="sig-canvas-modal" class="sig-canvas"></canvas>
                            <button type="button" class="btn-clear" id="clear-sig-modal">Clear Pad</button>
                        </div>
                    </form>`;
                setTimeout(() => {
                    const pad = initSignaturePad('sig-canvas-modal');
                    document.getElementById('clear-sig-modal').onclick = () => pad.clear();
                    modalSubmitBtn.onclick = () => {
                        const name = modalContent.querySelector('input[name="approved_by"]').value.trim();
                        if (!name) return alert('Name is required');
                        if (pad.isEmpty()) return alert('Signature is required');
                        document.getElementById('signature_data_modal').value = pad.getData();
                        document.getElementById('modal-approval-form').submit();
                    };
                }, 100);
            }
        }
        window.closeApprovalModal = function () { modal.style.display = 'none'; }
    </script>
</body>

</html>