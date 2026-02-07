<?php
// Extracted variables from $data (handled by Controller::view)
// $activity, $message, $messageType, $user, $notifRepo, $pdo, $ildnRepo
use App\Models\ReferenceRepository;

$refRepo = new ReferenceRepository($pdo);
$ld_types = $refRepo->getAllLDTypes();
$modalities = $refRepo->getAllModalities();
$classifications = $refRepo->getAllClassifications();
$user_ildns = $ildnRepo->getILDNList($activity['user_id']);

$message = $message ?? null;
$messageType = $messageType ?? null;

$selected_competencies = explode(', ', $activity['competency'] ?? '');
$selected_classifications = explode(', ', $activity['classification'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Activity Record - Admin</title>
    <!-- Use PUBLIC_ROOT for includes -->
    <?php require BASE_PATH . 'includes/admin_head.php'; ?>
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/user/common_branded_header.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/admin/edit_activity.css?v=<?php echo time(); ?>">


</head>

<body>
    <div class="app-layout">
        <?php require BASE_PATH . 'includes/sidebar.php'; ?>

        <div class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <div class="breadcrumb">
                        <h1 class="page-title">Edit Record</h1>
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
                <div class="dashboard-card"
                    style="max-width: 900px; margin: 0 auto; overflow: hidden; border-radius: var(--radius-xl);">

                    <!-- Activity Branded Header -->
                    <div class="activity-branded-header">
                        <div class="header-logo-container">
                            <img src="<?php echo PUBLIC_ROOT; ?>assets/LogoLDP.png" alt="LDP Logo" class="branded-logo">
                        </div>
                        <div class="header-content">
                            <span class="system-badge">Admin Review</span>
                            <h1 class="header-main-title"><?php echo htmlspecialchars($activity['title']); ?></h1>
                            <p class="header-subtitle">Schools Division Office - Activity Validation</p>
                        </div>
                    </div>

                    <div class="view-prog-track">
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

                    <div class="card-body" style="padding: 32px 40px;">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo ($messageType === 'success') ? 'success' : 'danger'; ?> mb-4"
                                style="border-radius: 12px; font-weight: 600;">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form id="activity-form" method="POST" enctype="multipart/form-data">

                            <!-- Section 1: Basic Information -->
                            <div class="form-section">
                                <div class="data-section-title">
                                    <i class="bi bi-info-circle"></i> BASIC INFORMATION
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label">TITLE OF L&D ACTIVITY <span
                                            style="color: var(--danger);">*</span></label>
                                    <input type="text" name="title" class="form-control" required
                                        value="<?php echo htmlspecialchars($activity['title']); ?>"
                                        placeholder="Enter the full title of the training or activity">
                                </div>

                                <div class="details-grid">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="bi bi-calendar3"></i> DATE(S) ATTENDED
                                            <span style="color: var(--danger);" id="req-date">*</span>
                                        </label>
                                        <input type="text" name="date_attended" id="date_picker" class="form-control"
                                            value="<?php echo htmlspecialchars($activity['date_attended']); ?>" required
                                            placeholder="Select dates">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">
                                            VENUE <span style="color: var(--danger);" id="req-venue">*</span>
                                        </label>
                                        <input type="text" name="venue" id="venue" class="form-control" required
                                            value="<?php echo htmlspecialchars($activity['venue']); ?>"
                                            placeholder="e.g. SDO Conference Hall">
                                    </div>
                                </div>

                                <div class="details-grid mt-4">
                                    <div class="form-group">
                                        <label class="form-label">ADDRESSED COMPETENCY/IES <span
                                                style="color: var(--danger);">*</span></label>
                                        <select id="competency_select" name="competency[]" class="form-control" multiple
                                            placeholder="Select or type learning needs...">
                                            <option value="Relevant Expertise" <?php echo in_array('Relevant Expertise', $selected_competencies) ? 'selected' : ''; ?>>Relevant Expertise
                                            </option>
                                            <?php foreach ($user_ildns as $ildn): ?>
                                                <option value="<?php echo htmlspecialchars($ildn['need_text']); ?>" <?php echo in_array($ildn['need_text'], $selected_competencies) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($ildn['need_text']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">CLASSIFICATION <span style="color: var(--danger);"
                                                id="req-classification">*</span></label>
                                        <select id="classification_select" name="classification[]" class="form-control"
                                            multiple placeholder="Select classification...">
                                            <?php foreach ($classifications as $classItem): ?>
                                                <option value="<?php echo htmlspecialchars($classItem['name']); ?>" <?php echo in_array($classItem['name'], $selected_classifications) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($classItem['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Modalities & Type -->
                            <div class="form-section">
                                <div class="details-grid">
                                    <div>
                                        <div class="data-section-title">
                                            <i class="bi bi-diagram-3"></i> MODALITY
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">SELECT METHOD <span style="color: var(--danger);"
                                                    id="req-modality">*</span></label>
                                            <select id="modality_select" name="modality" class="form-control">
                                                <option value="" disabled>Select modality...</option>
                                                <?php foreach ($modalities as $mod): ?>
                                                    <option value="<?php echo htmlspecialchars($mod['name']); ?>" <?php echo ($activity['modality'] === $mod['name']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($mod['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="data-section-title">
                                            <i class="bi bi-tags"></i> TYPE OF L&D
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">L&D CATEGORY <span style="color: var(--danger);"
                                                    id="req-type">*</span></label>
                                            <select id="type_ld_select" name="type_ld" class="form-control">
                                                <option value="" disabled>Select type...</option>
                                                <?php foreach ($ld_types as $type): ?>
                                                    <option value="<?php echo htmlspecialchars($type['name']); ?>" <?php echo ($activity['type_ld'] === $type['name']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($type['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div id="type-others-input-container"
                                                style="display: <?php echo ($activity['type_ld'] === 'Others') ? 'block' : 'none'; ?>; margin-top: 15px;">
                                                <input type="text" name="type_ld_others" class="form-control"
                                                    placeholder="Please specify type..."
                                                    value="<?php echo htmlspecialchars($activity['type_ld_others'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Activity Evidence & Certification -->
                            <div class="form-section">
                                <div class="data-section-title">
                                    <i class="bi bi-collection-play"></i> ACTIVITY EVIDENCE & CERTIFICATION
                                </div>

                                <div class="evidence-grid">
                                    <!-- Workplace Application Plan -->
                                    <div class="evidence-item">
                                        <div class="evidence-sub-title">
                                            <i class="bi bi-rocket-takeoff"></i> Workplace Application Plan
                                        </div>
                                        <div class="form-group">
                                            <label class="premium-label">EVIDENCE / ATTACHMENTS <span
                                                    style="color: var(--danger);" id="req-workplace">*</span></label>

                                            <?php if ($activity['workplace_image_path']): ?>
                                                <div class="current-files-preview"
                                                    style="display: flex; gap: 8px; margin-bottom: 12px; flex-wrap: wrap;">
                                                    <?php foreach (explode(', ', $activity['workplace_image_path']) as $img): ?>
                                                        <a href="<?php echo PUBLIC_ROOT . $img; ?>" target="_blank"
                                                            class="thumbnail-card"
                                                            style="width: 50px; height: 50px; border-radius: 8px; overflow: hidden; border: 1.5px solid #e2e8f0; display: block;">
                                                            <img src="<?php echo PUBLIC_ROOT . $img; ?>"
                                                                style="width: 100%; height: 100%; object-fit: cover;">
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>

                                            <div class="file-drop-zone" id="drop-zone"
                                                onclick="document.getElementById('workplace_image').click()">
                                                <i class="bi bi-cloud-arrow-up"></i>
                                                <p>Click to upload</p>
                                                <input type="file" name="workplace_image[]" id="workplace_image"
                                                    multiple hidden>
                                                <div id="file-list" class="compact-file-list"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Application of Learning -->
                                    <div class="evidence-item">
                                        <div class="evidence-sub-title">
                                            <i class="bi bi-lightbulb"></i> Application of Learning
                                        </div>
                                        <div class="form-group">
                                            <label class="premium-label">SUPPORTING DOCUMENT</label>

                                            <?php if ($activity['application_file_path']): ?>
                                                <a href="<?php echo PUBLIC_ROOT . $activity['application_file_path']; ?>"
                                                    target="_blank" class="file-badge-inline compact"
                                                    style="text-decoration: none;">
                                                    <i class="bi bi-file-earmark-text"></i>
                                                    <span>Already Uploaded</span>
                                                </a>
                                            <?php endif; ?>

                                            <div class="file-drop-zone" id="app-drop-zone"
                                                onclick="document.getElementById('application_file').click()">
                                                <i class="bi bi-cloud-arrow-up"></i>
                                                <p>Click to replace</p>
                                                <input type="file" name="application_file" id="application_file" hidden>
                                                <div id="app-file-list" class="compact-file-list"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Certificate -->
                                    <div class="evidence-item">
                                        <div class="evidence-sub-title">
                                            <i class="bi bi-award"></i> Certificate of Participation
                                        </div>
                                        <div class="form-group">
                                            <label class="premium-label">CERTIFICATE FILE <span
                                                    style="color: var(--danger);" id="req-cert">*</span></label>

                                            <?php if ($activity['certificate_path']): ?>
                                                <a href="<?php echo PUBLIC_ROOT . $activity['certificate_path']; ?>"
                                                    target="_blank" class="file-badge-inline compact success"
                                                    style="text-decoration: none;">
                                                    <i class="bi bi-patch-check-fill"></i>
                                                    <span>Verified Certificate</span>
                                                </a>
                                            <?php endif; ?>

                                            <div class="file-drop-zone" id="cert-drop-zone"
                                                onclick="document.getElementById('certificate_image').click()">
                                                <i class="bi bi-cloud-arrow-up"></i>
                                                <p>Upload replacement</p>
                                                <input type="file" name="certificate_image" id="certificate_image"
                                                    hidden>
                                                <div id="cert-file-list" class="compact-file-list"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 6: Personal Reflection -->
                            <div class="form-section">
                                <div class="data-section-title">
                                    <i class="bi bi-journal-text"></i> PERSONAL REFLECTION
                                </div>
                                <div class="form-group">
                                    <label class="form-label">REFLECTION <span style="color: var(--danger);"
                                            id="req-reflection">*</span></label>
                                    <textarea name="reflection" class="form-control" required
                                        style="min-height: 150px; border-radius: 16px !important;"
                                        placeholder="Share your key takeaways..."><?php echo htmlspecialchars($activity['reflection']); ?></textarea>
                                </div>
                            </div>

                            <!-- Privacy Notice -->
                            <div class="privacy-notice-box">
                                <div style="display: flex; gap: 20px; align-items: flex-start;">
                                    <i class="bi bi-shield-lock-fill"
                                        style="font-size: 2rem; color: var(--primary-light);"></i>
                                    <div class="privacy-content">
                                        <h4
                                            style="font-size: 0.9rem; font-weight: 800; color: var(--premium-text); text-transform: uppercase; margin-bottom: 8px;">
                                            Privacy Notice</h4>
                                        <p
                                            style="font-size: 0.85rem; color: #64748b; line-height: 1.6; font-weight: 500;">
                                            As an administrator, you are updating a record that contains professional
                                            information. This modification must be verified against official
                                            documentation and will be logged in the system audit trail.</p>
                                        <label class="privacy-check-container"
                                            style="margin-top: 20px; display: flex; align-items: center; gap: 12px; cursor: pointer;">
                                            <input type="checkbox" id="privacy-agree" name="privacy_agree" required
                                                checked style="width: 22px; height: 22px;">
                                            <span
                                                style="font-size: 0.9rem; font-weight: 700; color: var(--premium-text);">I
                                                confirm this manual override is verified and authorized</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top: 40px; text-align: center; padding-bottom: 50px;">
                                <button type="submit" class="btn btn-primary btn-lg"
                                    style="width: 100%; max-width: 450px; padding: 18px; border-radius: 16px; margin-bottom: 15px; font-weight: 800; letter-spacing: 0.5px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                                    <i class="bi bi-check-circle-fill"></i> UPDATE ACTIVITY RECORD
                                </button>
                                <br>
                                <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/view-activity?id=<?php echo $activity['id']; ?>"
                                    class="btn btn-secondary btn-lg"
                                    style="width: 100%; max-width: 450px; padding: 18px; border-radius: 16px; background: #fff; border: 1.5px solid #e2e8f0; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; color: #64748b;">CANCEL</a>
                            </div>

                        </form>
                    </div>
                </div>
            </main>

            <footer class="user-footer">
                <p>&copy; <?php echo date('Y'); ?> SDO L&D Passbook System. (Admin Panel)</p>
            </footer>
        </div>
    </div>

    <!-- JS Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Initialize Flatpickr
            const datePicker = flatpickr("#date_picker", {
                mode: "multiple",
                dateFormat: "Y-m-d",
                conjunction: ", ",
                altInput: true,
                altFormat: "M j, Y",
                disableMobile: "true"
            });

            // 2. Initialize TomSelects
            const competencySelect = new TomSelect('#competency_select', {
                plugins: ['remove_button'],
                create: true,
                persist: false,
                placeholder: 'Select learning needs...',
                onChange: function () { checkRelevantExpertise(); }
            });

            const classificationSelect = new TomSelect('#classification_select', {
                plugins: ['remove_button'],
                create: false,
                persist: false,
                placeholder: 'Select classification...'
            });

            const modalitySelect = new TomSelect('#modality_select', {
                create: false,
                persist: false,
                placeholder: 'Select modality...',
                maxItems: 1
            });

            const typeSelect = new TomSelect('#type_ld_select', {
                create: false,
                persist: false,
                placeholder: 'Select type...',
                maxItems: 1,
                onChange: function (value) {
                    const container = document.getElementById('type-others-input-container');
                    if (container) container.style.display = (value === 'Others') ? 'block' : 'none';
                }
            });

            // 3. Logic for "Relevant Expertise" Bypass
            const checkRelevantExpertise = () => {
                const selected = competencySelect.getValue();
                const isRelevantExpertise = Array.isArray(selected) ? selected.includes('Relevant Expertise') : selected === 'Relevant Expertise';

                const optionalFields = [
                    { id: 'date_picker', el: document.getElementById('date_picker') },
                    { id: 'venue', el: document.getElementById('venue') },
                    { id: 'classification_select', el: document.getElementById('classification_select') },
                    { id: 'modality_select', el: document.getElementById('modality_select') },
                    { id: 'type_ld_select', el: document.getElementById('type_ld_select') },
                    { id: 'workplace_image', el: document.getElementById('workplace_image') },
                    { id: 'certificate_image', el: document.getElementById('certificate_image') },
                    { id: 'reflection', el: document.querySelector('textarea[name="reflection"]') }
                ];

                const indicators = ['req-date', 'req-venue', 'req-classification', 'req-modality', 'req-type', 'req-workplace', 'req-cert', 'req-reflection'];

                if (isRelevantExpertise) {
                    optionalFields.forEach(f => { if (f.el) f.el.removeAttribute('required'); });
                    indicators.forEach(id => { const el = document.getElementById(id); if (el) el.style.display = 'none'; });
                } else {
                    // Do not re-add 'required' to hidden elements or file inputs in edit mode
                    // to prevent silent browser validation blocks.
                    indicators.forEach(id => { const el = document.getElementById(id); if (el) el.style.display = 'inline'; });
                }
            };

            // 4. File List Previews
            const setupFilePreview = (inputId, listId) => {
                const input = document.getElementById(inputId);
                const list = document.getElementById(listId);
                if (!input || !list) return;

                input.addEventListener('change', function () {
                    list.innerHTML = '';
                    Array.from(this.files).forEach(file => {
                        const icon = file.type.startsWith('image/') ? 'bi-image' : 'bi-file-earmark-pdf';
                        const badge = document.createElement('div');
                        badge.style.background = 'white';
                        badge.style.padding = '10px 18px';
                        badge.style.borderRadius = '12px';
                        badge.style.border = '1.5px solid #e2e8f0';
                        badge.style.display = 'flex';
                        badge.style.alignItems = 'center';
                        badge.style.gap = '10px';
                        badge.style.fontSize = '0.9rem';
                        badge.style.fontWeight = '700';
                        badge.style.color = '#0f172a';
                        badge.innerHTML = `<i class="bi ${icon}" style="color: var(--primary-light); font-size: 1.1rem;"></i> <span>${file.name}</span>`;
                        list.appendChild(badge);
                    });
                });
            };

            setupFilePreview('workplace_image', 'file-list');
            setupFilePreview('application_file', 'app-file-list');
            setupFilePreview('certificate_image', 'cert-file-list');

            // 5. Initial Runs
            checkRelevantExpertise();
        });
    </script>
</body>

</html>