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

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                                    <div class="form-group">
                                        <label class="form-label">Addressed Competency/ies <span
                                                style="color: var(--danger);">*</span></label>
                                        <select id="competency_select" name="competency[]" class="form-control"
                                            placeholder="Select or type learning needs..." required multiple>
                                            <option value="Relevant Expertise">Relevant Expertise</option>
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
                                    <div class="form-group">
                                        <label class="form-label">Classification <span style="color: var(--danger);"
                                                id="req-classification">*</span></label>
                                        <select id="classification_select" name="classification[]" class="form-control"
                                            required multiple placeholder="Select classification...">
                                            <?php
                                            $selected_classifications = !empty($activity['classification']) ? explode(', ', $activity['classification']) : [];
                                            foreach ($classifications as $classItem): ?>
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
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                                    <div>
                                        <div class="form-section-header">
                                            <i class="bi bi-diagram-3"></i>
                                            <h3>Modality <span style="color: var(--danger);" id="req-modality">*</span>
                                            </h3>
                                        </div>
                                        <select id="modality_select" name="modality" class="form-control" required
                                            placeholder="Select modality...">
                                            <option value="" disabled>Select modality...</option>
                                            <?php foreach ($modalities as $mod): ?>
                                                <option value="<?php echo htmlspecialchars($mod['name']); ?>" <?php echo $activity['modality'] == $mod['name'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($mod['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <div class="form-section-header">
                                            <i class="bi bi-tags"></i>
                                            <h3>Type of L&D <span style="color: var(--danger);" id="req-type">*</span>
                                            </h3>
                                        </div>
                                        <select id="type_ld_select" name="type_ld" class="form-control" required
                                            placeholder="Select type of L&D...">
                                            <option value="" disabled>Select type of L&D...</option>
                                            <?php foreach ($ld_types as $type): ?>
                                                <option value="<?php echo htmlspecialchars($type['name']); ?>" <?php echo $activity['type_ld'] == $type['name'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($type['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <div id="type-others-input-container"
                                            style="display: <?php echo $activity['type_ld'] == 'Others' ? 'block' : 'none'; ?>; margin-top: 12px;">
                                            <input type="text" name="type_ld_others" class="form-control"
                                                placeholder="Please specify type..."
                                                value="<?php echo htmlspecialchars($activity['type_ld_others']); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Workplace Application Plan -->
                            <div class="form-section">
                                <div class="form-section-header">
                                    <i class="bi bi-rocket-takeoff"></i>
                                    <h3>Workplace Application Plan</h3>
                                </div>

                                <style>
                                    .premium-label {
                                        font-size: 0.75rem;
                                        font-weight: 800;
                                        color: var(--text-secondary);
                                        text-transform: uppercase;
                                        letter-spacing: 1px;
                                        margin-bottom: 12px;
                                        display: flex;
                                        align-items: center;
                                        gap: 4px;
                                    }

                                    .file-drop-zone {
                                        border: 2px dashed #cbd5e1;
                                        border-radius: 16px;
                                        padding: 40px 20px;
                                        text-align: center;
                                        background: #f8fafc;
                                        cursor: pointer;
                                        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                                        display: flex;
                                        flex-direction: column;
                                        align-items: center;
                                        gap: 12px;
                                        position: relative;
                                        overflow: hidden;
                                    }

                                    .file-drop-zone:hover,
                                    .file-drop-zone.drag-over {
                                        border-color: var(--primary);
                                        background: #eff6ff;
                                        transform: translateY(-2px);
                                        box-shadow: 0 10px 15px -3px rgba(15, 76, 117, 0.1);
                                    }

                                    .file-drop-zone i {
                                        font-size: 3rem;
                                        color: var(--primary);
                                        opacity: 0.8;
                                        transition: transform 0.3s ease;
                                    }

                                    .file-drop-zone:hover i,
                                    .file-drop-zone.drag-over i {
                                        transform: scale(1.1);
                                        opacity: 1;
                                    }

                                    .file-drop-zone p {
                                        font-size: 1rem;
                                        font-weight: 700;
                                        color: var(--text-primary);
                                        margin: 0;
                                    }

                                    .file-drop-zone .upload-hint {
                                        font-size: 0.8rem;
                                        color: #64748b;
                                        font-weight: 500;
                                    }

                                    #file-list .file-badge,
                                    #app-file-list .file-badge,
                                    #cert-file-list .file-badge {
                                        background: white;
                                        padding: 8px 16px;
                                        border-radius: 10px;
                                        border: 1px solid #e2e8f0;
                                        display: flex;
                                        align-items: center;
                                        gap: 10px;
                                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                                        font-size: 0.85rem;
                                        font-weight: 600;
                                        color: var(--text-primary);
                                        animation: slideIn 0.3s ease-out forwards;
                                    }

                                    @keyframes slideIn {
                                        from {
                                            opacity: 0;
                                            transform: translateY(10px);
                                        }

                                        to {
                                            opacity: 1;
                                            transform: translateY(0);
                                        }
                                    }

                                    .current-files-grid {
                                        display: grid;
                                        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                                        gap: 12px;
                                        margin-bottom: 16px;
                                    }

                                    .current-file-item {
                                        position: relative;
                                        border-radius: 12px;
                                        overflow: hidden;
                                        border: 2px solid #e2e8f0;
                                        background: white;
                                        transition: all 0.2s ease;
                                    }

                                    .current-file-item:hover {
                                        border-color: var(--primary);
                                        transform: translateY(-2px);
                                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                                    }

                                    .current-file-item img {
                                        width: 100%;
                                        height: 100px;
                                        object-fit: cover;
                                    }

                                    .current-file-label {
                                        font-size: 0.7rem;
                                        font-weight: 600;
                                        color: var(--text-secondary);
                                        text-transform: uppercase;
                                        letter-spacing: 0.5px;
                                        margin-bottom: 8px;
                                    }
                                </style>

                                <div class="form-group">
                                    <?php if ($activity['workplace_image_path']): ?>
                                        <label class="current-file-label">Current Evidence</label>
                                        <div class="current-files-grid">
                                            <?php foreach (explode(', ', $activity['workplace_image_path']) as $img): ?>
                                                <div class="current-file-item">
                                                    <img src="<?php echo PUBLIC_ROOT . $img; ?>" alt="Evidence">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <label class="premium-label">Evidence / Attachments <span
                                            style="color: var(--danger);" id="req-workplace">*</span></label>
                                    <div class="file-drop-zone" id="drop-zone"
                                        onclick="document.getElementById('workplace_image').click()"
                                        style="padding: 20px; min-height: auto;">
                                        <i class="bi bi-cloud-arrow-up" style="font-size: 2rem;"></i>
                                        <p style="font-size: 0.9rem;">Click to upload files (Images or Document)</p>
                                        <span class="upload-hint" style="font-size: 0.75rem;">Drag and drop your files
                                            here or click to browse</span>
                                        <input type="file" name="workplace_image[]" id="workplace_image" multiple
                                            hidden>
                                        <div id="file-list"
                                            style="display: flex; flex-wrap: wrap; gap: 8px; justify: center; margin-top: 10px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4: Application of Learning -->
                            <div class="form-section">
                                <div class="form-section-header">
                                    <i class="bi bi-lightbulb"></i>
                                    <h3>Application of Learning</h3>
                                </div>

                                <div class="form-group">
                                    <?php if (!empty($activity['application_file_path'])): ?>
                                        <label class="current-file-label">Current Document</label>
                                        <div style="margin-bottom: 16px;">
                                            <a href="<?php echo PUBLIC_ROOT . $activity['application_file_path']; ?>"
                                                target="_blank"
                                                style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: #f1f5f9; border-radius: 8px; text-decoration: none; color: var(--text-primary); font-size: 0.85rem; font-weight: 600;">
                                                <i class="bi bi-file-earmark-text"></i>
                                                View Current File
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <label class="premium-label">Supporting Document (Optional)</label>
                                    <div class="file-drop-zone" id="app-drop-zone"
                                        onclick="document.getElementById('application_file').click()"
                                        style="padding: 20px; min-height: auto;">
                                        <i class="bi bi-cloud-arrow-up" style="font-size: 2rem;"></i>
                                        <p style="font-size: 0.9rem;">Click to upload files (Images or Document)</p>
                                        <span class="upload-hint" style="font-size: 0.75rem;">Drag and drop your files
                                            here or click to browse</span>
                                        <input type="file" name="application_file" id="application_file"
                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" hidden>
                                        <div id="app-file-list"
                                            style="display: flex; flex-wrap: wrap; gap: 8px; justify: center; margin-top: 10px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 5: Certificate of Participation -->
                            <div class="form-section">
                                <div class="form-section-header">
                                    <i class="bi bi-award"></i>
                                    <h3>Certificate of Participation</h3>
                                </div>

                                <div class="form-group">
                                    <?php if (!empty($activity['certificate_image_path'])): ?>
                                        <label class="current-file-label">Current Certificate</label>
                                        <div style="margin-bottom: 16px;">
                                            <a href="<?php echo PUBLIC_ROOT . $activity['certificate_image_path']; ?>"
                                                target="_blank"
                                                style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: #f1f5f9; border-radius: 8px; text-decoration: none; color: var(--text-primary); font-size: 0.85rem; font-weight: 600;">
                                                <i class="bi bi-file-earmark-image"></i>
                                                View Current Certificate
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <label class="premium-label">Upload Certificate <span style="color: var(--danger);"
                                            id="req-cert">*</span></label>
                                    <div class="file-drop-zone" id="cert-drop-zone"
                                        onclick="document.getElementById('certificate_image').click()"
                                        style="padding: 20px; min-height: auto;">
                                        <i class="bi bi-cloud-arrow-up" style="font-size: 2rem;"></i>
                                        <p style="font-size: 0.9rem;">Click to upload certificate (Images or PDF)</p>
                                        <span class="upload-hint" style="font-size: 0.75rem;">Drag and drop file here or
                                            click to browse</span>
                                        <input type="file" name="certificate_image" id="certificate_image"
                                            accept=".pdf,.jpg,.jpeg,.png,.webp" hidden>
                                        <div id="cert-file-list"
                                            style="display: flex; flex-wrap: wrap; gap: 8px; justify: center; margin-top: 10px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 6: Personal Reflection -->
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
    <script src="<?php echo PUBLIC_ROOT; ?>js/active-forms.js?v=<?php echo time(); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Flatpickr
            const datePicker = flatpickr("#date_picker", {
                mode: "multiple",
                dateFormat: "Y-m-d",
                conjunction: ", ",
                altInput: true,
                altFormat: "M j, Y",
                disableMobile: "true"
            });

            // Initialize TomSelect for Competencies
            const competencySelect = new TomSelect('#competency_select', {
                plugins: ['remove_button'],
                create: true,
                persist: false,
                placeholder: 'Select or type learning needs...',
                maxOptions: 50
            });

            // Initialize TomSelect for Classification
            const classificationSelect = new TomSelect('#classification_select', {
                plugins: ['remove_button'],
                create: false,
                persist: false,
                placeholder: 'Select classification...'
            });

            // Initialize TomSelect for Modalities
            const modalitySelect = new TomSelect('#modality_select', {
                create: false,
                persist: false,
                placeholder: 'Select modality...',
                maxItems: 1
            });

            // Initialize TomSelect for Type of L&D
            const typeSelect = new TomSelect('#type_ld_select', {
                create: false,
                persist: false,
                placeholder: 'Select type of L&D...',
                maxItems: 1,
                onChange: function (value) {
                    toggleOthersInput();
                }
            });

            // Logic for "Others" specify input
            const othersContainer = document.getElementById('type-others-input-container');

            // Function to toggle 'others' input visibility
            const toggleOthersInput = () => {
                if (othersContainer && typeSelect) {
                    const selected = typeSelect.getValue();
                    const isOthersSelected = selected === 'Others';
                    othersContainer.style.display = isOthersSelected ? 'block' : 'none';
                }
            };

            // Initial check
            toggleOthersInput();

            // Simple File List Preview
            const setupFilePreview = (inputId, listId) => {
                const input = document.getElementById(inputId);
                const list = document.getElementById(listId);
                if (!input || !list) return;

                input.addEventListener('change', function () {
                    list.innerHTML = '';
                    Array.from(this.files).forEach(file => {
                        const badge = document.createElement('div');
                        badge.className = 'file-badge';
                        badge.innerHTML = `<i class="bi bi-file-earmark-check"></i> <span>${file.name}</span>`;
                        list.appendChild(badge);
                    });
                });
            };

            setupFilePreview('workplace_image', 'file-list');
            setupFilePreview('application_file', 'app-file-list');
            setupFilePreview('certificate_image', 'cert-file-list');
        });
    </script>
</body>

</html>