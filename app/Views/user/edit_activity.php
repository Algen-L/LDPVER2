<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Activity Record - LDP</title>
    <?php include BASE_PATH . 'includes/head.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/user/common_branded_header.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="app-layout">
        <?php include BASE_PATH . 'includes/sidebar.php'; ?>

        <div class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <div class="breadcrumb">
                        <h1 class="page-title">Edit Activity</h1>
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
                <div class="dashboard-card"
                    style="max-width: 900px; margin: 0 auto; overflow: hidden; border-radius: var(--radius-xl);">
                    <!-- Activity Branded Header -->
                    <div class="activity-branded-header">
                        <div class="header-logo-container">
                            <img src="<?php echo PUBLIC_ROOT; ?>assets/LogoLDP.png" alt="LDP Logo" class="branded-logo">
                        </div>
                        <div class="header-content">
                            <span class="system-badge">Modify Record</span>
                            <h1 class="header-main-title">Learning & Development Record</h1>
                            <p class="header-subtitle">Schools Division Office - Update Activity</p>
                        </div>
                    </div>

                    <div class="card-body" style="padding: 40px;">
                        <form id="activity-form" method="POST" enctype="multipart/form-data">

                            <!-- Section 1: Basic Information -->
                            <div class="form-section">
                                <div class="form-section-header">
                                    <i class="bi bi-info-circle"></i>
                                    <h3>Basic Information</h3>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Title of L&D Activity <span
                                            style="color: var(--danger);">*</span></label>
                                    <input type="text" name="title" class="form-control" required
                                        value="<?php echo htmlspecialchars($activity['title']); ?>">
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                                    <div class="form-group">
                                        <label class="form-label">Date(s) Attended <span
                                                style="color: var(--danger);">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                            <input type="text" name="date_attended" id="date_picker"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($activity['date_attended']); ?>"
                                                required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Venue <span
                                                style="color: var(--danger);">*</span></label>
                                        <input type="text" name="venue" class="form-control" required
                                            value="<?php echo htmlspecialchars($activity['venue']); ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Addressed Competency/ies <span
                                            style="color: var(--danger);">*</span></label>
                                    <select id="competency_select" name="competency[]" class="form-control" required
                                        multiple>
                                        <?php
                                        $selected_competencies = explode(', ', $activity['competency']);
                                        foreach ($user_ildns as $ildn):
                                            ?>
                                            <option value="<?php echo htmlspecialchars($ildn['need_text']); ?>" <?php echo in_array($ildn['need_text'], $selected_competencies) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($ildn['need_text']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Section 2: Modalities -->
                            <?php $modalities = explode(', ', $activity['modality']); ?>
                            <div class="form-section">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                                    <div>
                                        <div class="form-section-header">
                                            <i class="bi bi-diagram-3"></i>
                                            <h3>Modality <span style="color: var(--danger);">*</span></h3>
                                        </div>
                                        <div class="checkbox-grid">
                                            <?php foreach (['Formal Training', 'Job-Embedded Learning', 'Relationship Discussion Learning', 'Learning Action Cell'] as $m): ?>
                                                <label class="checkbox-item">
                                                    <input type="checkbox" name="modality[]" value="<?php echo $m; ?>" <?php echo in_array($m, $modalities) ? 'checked' : ''; ?>>
                                                    <span>
                                                        <?php echo $m; ?>
                                                    </span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php $types = explode(', ', $activity['type_ld']); ?>
                                    <div>
                                        <div class="form-section-header">
                                            <i class="bi bi-tags"></i>
                                            <h3>Type of L&D <span style="color: var(--danger);">*</span></h3>
                                        </div>
                                        <div class="checkbox-grid">
                                            <?php foreach (['Supervisory', 'Managerial', 'Technical'] as $t): ?>
                                                <label class="checkbox-item">
                                                    <input type="checkbox" name="type_ld[]" value="<?php echo $t; ?>" <?php echo in_array($t, $types) ? 'checked' : ''; ?>>
                                                    <span>
                                                        <?php echo $t; ?>
                                                    </span>
                                                </label>
                                            <?php endforeach; ?>
                                            <label class="checkbox-item">
                                                <input type="checkbox" name="type_ld[]" value="Others"
                                                    id="type-others-checkbox" <?php echo in_array('Others', $types) ? 'checked' : ''; ?>>
                                                <span>Others (Specify)</span>
                                            </label>
                                        </div>
                                        <div id="type-others-input-container"
                                            style="display: <?php echo in_array('Others', $types) ? 'block' : 'none'; ?>; margin-top: 12px;">
                                            <input type="text" name="type_ld_others" class="form-control"
                                                value="<?php echo htmlspecialchars($activity['type_ld_others']); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Evidence -->
                            <div class="form-section">
                                <div class="form-section-header">
                                    <i class="bi bi-rocket-takeoff"></i>
                                    <h3>Evidence & Attachments</h3>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Current Evidence</label>
                                    <div style="display: flex; gap: 10px; margin-bottom: 12px; flex-wrap: wrap;">
                                        <?php if ($activity['workplace_image_path']): ?>
                                            <?php foreach (explode(', ', $activity['workplace_image_path']) as $img): ?>
                                                <div
                                                    style="position: relative; width: 80px; height: 80px; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
                                                    <img src="<?php echo PUBLIC_ROOT . $img; ?>"
                                                        style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span style="color: var(--text-muted); font-size: 0.85rem;">No evidence uploaded
                                                yet.</span>
                                        <?php endif; ?>
                                    </div>
                                    <label class="form-label">Upload New Evidence (Optional)</label>
                                    <div class="file-drop-zone" id="drop-zone"
                                        onclick="document.getElementById('workplace_image').click()"
                                        style="padding: 20px;">
                                        <i class="bi bi-cloud-arrow-up"></i>
                                        <p>Click to replace files</p>
                                        <input type="file" name="workplace_image[]" id="workplace_image" multiple
                                            hidden>
                                        <div id="file-list" style="margin-top: 10px;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4: Reflection -->
                            <div class="form-section">
                                <div class="form-section-header">
                                    <i class="bi bi-journal-text"></i>
                                    <h3>Personal Reflection</h3>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Reflection <span
                                            style="color: var(--danger);">*</span></label>
                                    <textarea name="reflection" class="form-control" required
                                        style="min-height: 120px;"><?php echo htmlspecialchars($activity['reflection']); ?></textarea>
                                </div>
                            </div>

                            <div style="margin-top: 32px; text-align: center;">
                                <button type="submit" class="btn btn-primary btn-lg"
                                    style="width: 100%; max-width: 400px;">
                                    <i class="bi bi-check-circle-fill"></i> UPDATE RECORD
                                </button>
                                <a href="<?php echo PUBLIC_ROOT; ?>index.php/user/view-activity?id=<?php echo $activity['id']; ?>"
                                    class="btn btn-secondary btn-lg"
                                    style="width: 100%; max-width: 400px; margin-top: 12px;">CANCEL</a>
                            </div>

                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- JS Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#date_picker", {
                mode: "multiple",
                dateFormat: "Y-m-d",
                conjunction: ", ",
                altInput: true,
                altFormat: "M j, Y"
            });

            new TomSelect('#competency_select', {
                plugins: ['remove_button'],
                create: true
            });

            const othersCheckbox = document.getElementById('type-others-checkbox');
            const othersContainer = document.getElementById('type-others-input-container');
            if (othersCheckbox) {
                othersCheckbox.addEventListener('change', function () {
                    othersContainer.style.display = this.checked ? 'block' : 'none';
                });
            }

            const input = document.getElementById('workplace_image');
            const list = document.getElementById('file-list');
            input.addEventListener('change', function () {
                list.innerHTML = '';
                Array.from(this.files).forEach(file => {
                    list.innerHTML += `<div class="file-badge">${file.name}</div>`;
                });
            });
        });
    </script>
</body>

</html>