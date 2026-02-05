<?php
// Extracted variables from $data (handled by Controller::view)
// $activity, $message, $messageType, $user, $notifRepo, $pdo
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Activity Record - Admin</title>
    <?php require BASE_PATH . 'includes/admin_head.php'; ?>
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/admin/edit_activity.css?v=<?php echo time(); ?>">

</head>

<body>
    <div class="app-layout">
        <?php require BASE_PATH . 'includes/sidebar.php'; ?>

        <div class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <div class="breadcrumb">
                        <h1 class="page-title">Update Activity</h1>
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
                <div class="dashboard-card edit-activity-container">
                    <div class="card-body edit-activity-body">
                        <?php if ($message): ?>
                            <div
                                class="alert alert-<?php echo ($messageType === 'success') ? 'success' : 'danger'; ?> mb-4">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <div class="prog-preview">
                            <div class="view-prog-track">
                                <div class="view-prog-steps">
                                    <div class="view-prog-line"></div>
                                    <?php
                                    $stages = [
                                        ['label' => 'Submitted', 'active' => true],
                                        ['label' => 'Reviewed', 'active' => (bool) $activity['reviewed_by_supervisor']],
                                        ['label' => 'Recommended', 'active' => (bool) $activity['recommending_asds']],
                                        ['label' => 'Approved', 'active' => (bool) $activity['approved_sds']]
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
                                            <div class="view-prog-icon"><i
                                                    class="bi <?php echo $stage['active'] ? 'bi-check2' : 'bi-circle'; ?>"></i>
                                            </div>
                                            <span class="view-prog-label"><?php echo $stage['label']; ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-section">
                                <div class="form-section-header">
                                    <i class="bi bi-info-circle"></i>
                                    <h3>Basic Information</h3>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Title of L&D Activity <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" required
                                        value="<?php echo htmlspecialchars($activity['title']); ?>">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group mb-3">
                                        <label class="form-label">Date(s) Attended <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="date_attended" id="date_picker" class="form-control"
                                            value="<?php echo htmlspecialchars($activity['date_attended']); ?>"
                                            required>
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label class="form-label">Venue</label>
                                        <input type="text" name="venue" class="form-control"
                                            value="<?php echo htmlspecialchars($activity['venue'] ?: ''); ?>"
                                            placeholder="e.g. SDO Conference Hall">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group mb-3">
                                        <label class="form-label">Addressed Competency/ies <span
                                                class="text-danger">*</span></label>
                                        <select id="competency-select" name="competency[]" class="form-control" required
                                            multiple>
                                            <?php
                                            $user_ildns = $ildnRepo->getILDNList($activity['user_id']);
                                            $current_competencies = explode(', ', $activity['competency']);

                                            // Ensure current competencies that aren't in ILDNs are still options
                                            $ildn_texts = array_column($user_ildns, 'need_text');
                                            foreach ($current_competencies as $comp):
                                                if (trim($comp) && !in_array(trim($comp), $ildn_texts)): ?>
                                                    <option value="<?php echo htmlspecialchars(trim($comp)); ?>" selected>
                                                        <?php echo htmlspecialchars(trim($comp)); ?>
                                                    </option>
                                                <?php endif; endforeach; ?>

                                            <?php foreach ($user_ildns as $ildn): ?>
                                                <option value="<?php echo htmlspecialchars($ildn['need_text']); ?>" <?php echo in_array($ildn['need_text'], $current_competencies) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($ildn['need_text']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label class="form-label">Conducted By <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="conducted_by" class="form-control" required
                                            value="<?php echo htmlspecialchars($activity['conducted_by']); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-section-header"><i class="bi bi-diagram-3"></i>
                                            <h3>Modality</h3>
                                        </div>
                                        <div class="checkbox-grid">
                                            <?php $modalities = ["Formal Training", "Job-Embedded Learning", "Relationship Discussion Learning", "Learning Action Cell"];
                                            $current_mods = explode(', ', $activity['modality']);
                                            foreach ($modalities as $m): ?>
                                                <label class="checkbox-item">
                                                    <input type="checkbox" name="modality[]" value="<?php echo $m; ?>" <?php echo in_array($m, $current_mods) ? 'checked' : ''; ?>>
                                                    <span><?php echo $m; ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-section-header"><i class="bi bi-tags"></i>
                                            <h3>Type of L&D</h3>
                                        </div>
                                        <div class="checkbox-grid">
                                            <?php $types = ["Supervisory", "Managerial", "Technical", "Others"];
                                            $current_types = explode(', ', $activity['type_ld']);
                                            foreach ($types as $t): ?>
                                                <label class="checkbox-item">
                                                    <input type="checkbox" name="type_ld[]" value="<?php echo $t; ?>" <?php echo in_array($t, $current_types) ? 'checked' : ''; ?>>
                                                    <span><?php echo $t; ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                        <input type="text" name="type_ld_others" class="form-control mt-2"
                                            placeholder="Specify if others..."
                                            value="<?php echo htmlspecialchars($activity['type_ld_others'] ?: ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-section-header"><i class="bi bi-rocket-takeoff"></i>
                                    <h3>Workplace Application</h3>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Update Evidence / Attachments</label>
                                    <input type="file" name="workplace_image[]" class="form-control" multiple>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Reflection <span class="text-danger">*</span></label>
                                    <textarea name="reflection" class="form-control reflection-textarea"
                                        required><?php echo htmlspecialchars($activity['reflection']); ?></textarea>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5"><i class="bi bi-save"></i>
                                    Update Record</button>
                                <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/view-activity?id=<?php echo $activity['id']; ?>"
                                    class="btn btn-secondary btn-lg ms-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#date_picker", {
                mode: "multiple",
                dateFormat: "Y-m-d",
                defaultDate: <?php echo json_encode(explode(', ', $activity['date_attended'])); ?>,
                conjunction: ", ",
                altInput: true,
                altFormat: "M j, Y"
            });
            new TomSelect("#competency-select", { create: true, maxItems: 5, placeholder: "Select or type competency..." });
        });
    </script>
</body>

</html>