<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record New Activity - LDP</title>
    <!-- Use PUBLIC_ROOT for includes -->
    <?php include BASE_PATH . 'includes/head.php'; ?>
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
    <!-- Tom Select CSS -->
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
                        <h1 class="page-title">Record Activity</h1>
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
                            <span class="system-badge">Activity Entry</span>
                            <h1 class="header-main-title">Learning & Development Attended</h1>
                            <p class="header-subtitle">Schools Division Office - Official Record Form</p>
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
                                        placeholder="Enter the full title of the training or activity">
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                                    <div class="form-group">
                                        <label class="form-label">Date(s) Attended <span style="color: var(--danger);"
                                                id="req-date">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"
                                                style="background: var(--bg-secondary); border-right: none;">
                                                <i class="bi bi-calendar3"></i>
                                            </span>
                                            <input type="text" name="date_attended" id="date_picker"
                                                class="form-control" placeholder="Click to select dates" required
                                                style="border-left: none;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Venue <span style="color: var(--danger);"
                                                id="req-venue">*</span></label>
                                        <input type="text" name="venue" id="venue" class="form-control" required
                                            placeholder="e.g. SDO Conference Hall">
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                                    <div class="form-group">
                                        <label class="form-label">Addressed Competency/ies <span
                                                style="color: var(--danger);">*</span></label>
                                        <select id="competency_select" name="competency[]" class="form-control"
                                            placeholder="Select or type learning needs..." required multiple>
                                            <option value="Relevant Expertise">Relevant Expertise</option>
                                            <?php foreach ($user_ildns as $ildn): ?>
                                                <option value="<?php echo htmlspecialchars($ildn['need_text']); ?>">
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
                                            <?php foreach ($classifications as $classItem): ?>
                                                <option value="<?php echo htmlspecialchars($classItem['name']); ?>">
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
                                            <option value="" disabled selected>Select modality...</option>
                                            <?php foreach ($modalities as $mod): ?>
                                                <option value="<?php echo htmlspecialchars($mod['name']); ?>">
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
                                            <option value="" disabled selected>Select type of L&D...</option>
                                            <?php foreach ($ld_types as $type): ?>
                                                <option value="<?php echo htmlspecialchars($type['name']); ?>">
                                                    <?php echo htmlspecialchars($type['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <div id="type-others-input-container" style="display: none; margin-top: 12px;">
                                            <input type="text" name="type_ld_others" class="form-control"
                                                placeholder="Please specify type...">
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

                                    .privacy-notice-box {
                                        background: #f1f5f9;
                                        border-radius: 12px;
                                        padding: 24px;
                                        border: 1px solid #e2e8f0;
                                        margin-top: 40px;
                                        display: flex;
                                        gap: 20px;
                                        align-items: flex-start;
                                        text-align: left;
                                        transition: all 0.3s ease;
                                    }

                                    .privacy-notice-box:has(input:checked) {
                                        background: #f0fdf4;
                                        border-color: #bbf7d0;
                                    }

                                    .privacy-check-container {
                                        margin-top: 12px;
                                        display: flex;
                                        align-items: center;
                                        gap: 12px;
                                        padding-top: 15px;
                                        border-top: 1px solid rgba(0, 0, 0, 0.05);
                                        cursor: pointer;
                                    }

                                    .privacy-check-container input {
                                        width: 20px;
                                        height: 20px;
                                        cursor: pointer;
                                    }

                                    .privacy-check-text {
                                        font-size: 0.85rem;
                                        font-weight: 700;
                                        color: var(--text-primary);
                                    }

                                    .privacy-notice-box i {
                                        font-size: 1.5rem;
                                        color: var(--primary);
                                        margin-top: -2px;
                                    }

                                    .privacy-content h4 {
                                        font-size: 0.85rem;
                                        font-weight: 800;
                                        color: var(--text-primary);
                                        margin-bottom: 6px;
                                        text-transform: uppercase;
                                        letter-spacing: 0.5px;
                                    }

                                    .privacy-content p {
                                        font-size: 0.82rem;
                                        color: #64748b;
                                        line-height: 1.6;
                                        margin: 0;
                                        font-weight: 500;
                                    }
                                </style>

                                <div class="form-group">
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
                                            accept=".pdf,.jpg,.jpeg,.png,.webp" hidden required>
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
                                    <label class="form-label">Reflection <span style="color: var(--danger);"
                                            id="req-reflection">*</span></label>
                                    <textarea name="reflection" class="form-control" required style="min-height: 120px;"
                                        placeholder="Share your key takeaways and how this will improve your performance..."></textarea>
                                </div>
                            </div>

                            <!-- Privacy Notice -->
                            <div class="privacy-notice-box">
                                <i class="bi bi-shield-lock-fill"></i>
                                <div class="privacy-content">
                                    <h4>Privacy Notice</h4>
                                    <p>We collect personal and professional information (Name, Activity Details, and
                                        Evidence) when you submit this record. This data will be utilized solely for
                                        documentation and processing of your L&D progress within SDO DepEd.</p>
                                    <label class="privacy-check-container">
                                        <input type="checkbox" id="privacy-agree" name="privacy_agree" required>
                                        <span class="privacy-check-text">I have read and agree to the Privacy
                                            Notice</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div style="margin-top: 32px; text-align: center; padding-bottom: 40px;">
                                <button type="submit" class="btn btn-primary btn-lg"
                                    style="width: 100%; max-width: 400px;">
                                    <i class="bi bi-check-circle-fill"></i> SUBMIT ACTIVITY RECORD
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </main>

            <footer class="user-footer">
                <p>&copy;
                    <?php echo date('Y'); ?> SDO L&D Passbook System.
                </p>
            </footer>
        </div>
    </div>

    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <!-- Flatpickr JS -->
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
                maxOptions: 50,
                onChange: function (value) {
                    checkRelevantExpertise();
                }
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
                    saveDraft(); // Save on change
                }
            });

            // Logic for "Relevant Expertise" Bypass
            const checkRelevantExpertise = () => {
                const selected = competencySelect.getValue();
                const isRelevantExpertise = Array.isArray(selected)
                    ? selected.includes('Relevant Expertise')
                    : selected === 'Relevant Expertise';

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

                const reqIndicators = {
                    'req-date': document.getElementById('req-date'),
                    'req-venue': document.getElementById('req-venue'),
                    'req-classification': document.getElementById('req-classification'),
                    'req-modality': document.getElementById('req-modality'),
                    'req-type': document.getElementById('req-type'),
                    'req-workplace': document.getElementById('req-workplace'),
                    'req-cert': document.getElementById('req-cert'),
                    'req-reflection': document.getElementById('req-reflection')
                };

                if (isRelevantExpertise) {
                    optionalFields.forEach(field => {
                        if (field.el) field.el.removeAttribute('required');
                    });

                    // Hide Asterisks
                    Object.values(reqIndicators).forEach(el => { if (el) el.style.display = 'none'; });

                } else {
                    optionalFields.forEach(field => {
                        if (field.el) field.el.setAttribute('required', 'required');
                    });

                    // Show Asterisks
                    Object.values(reqIndicators).forEach(el => { if (el) el.style.display = 'inline'; });
                }

                saveDraft();
            };

            // Logic for "Others" specify input
            const othersContainer = document.getElementById('type-others-input-container');

            // Function to toggle 'others' input visibility
            const toggleOthersInput = () => {
                if (othersContainer && typeSelect) {
                    const selected = typeSelect.getValue(); // Returns array for multiple
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


            // --- Form Persistence Logic ---
            const form = document.getElementById('activity-form');
            const STORAGE_KEY = 'ldp_activity_draft_v3'; // Bump version for logic change

            /**
             * Save form data to localStorage
             */
            const saveDraft = () => {
                const formData = new FormData(form);
                const draft = {};

                // Convert FormData to object
                for (const [key, value] of formData.entries()) {
                    if (value instanceof File) continue;

                    if (draft[key]) {
                        if (!Array.isArray(draft[key])) {
                            draft[key] = [draft[key]];
                        }
                        draft[key].push(value);
                    } else {
                        draft[key] = value;
                    }
                }

                // We need to explicitly check multi-selects if they are empty, as FormData won't include them
                // But TomSelect updates the underlying select, so if it has value, it's in FormData.
                // If it's empty, it's missing. That's fine for saving.

                localStorage.setItem(STORAGE_KEY, JSON.stringify(draft));
            };

            /**
             * Restore form data from localStorage
             */
            const restoreDraft = () => {
                const savedData = localStorage.getItem(STORAGE_KEY);
                if (!savedData) return;

                try {
                    const draft = JSON.parse(savedData);

                    // Restore Standard Inputs (Text, inputs)
                    Object.keys(draft).forEach(name => {
                        // Skip complex fields handled below
                        if (['modality', 'type_ld', 'competency[]', 'date_attended', 'classification[]'].includes(name)) return;

                        const inputs = form.querySelectorAll(`[name="${name}"]`);
                        if (inputs.length > 0) {
                            if (inputs[0].type !== 'file') {
                                inputs[0].value = draft[name];
                            }
                        }
                    });

                    // Restore Special Components

                    // 1. Flatpickr (Date)
                    if (draft['date_attended']) {
                        datePicker.setDate(draft['date_attended'], true);
                    }

                    // 2. TomSelect (Competency)
                    if (draft['competency[]']) {
                        const comps = Array.isArray(draft['competency[]']) ? draft['competency[]'] : [draft['competency[]']];
                        comps.forEach(val => {
                            if (!competencySelect.options[val]) {
                                competencySelect.addOption({ value: val, text: val });
                            }
                        });
                        competencySelect.setValue(comps);
                    }

                    // 3. TomSelect (Classification) - Multi
                    if (draft['classification[]']) {
                        const classes = Array.isArray(draft['classification[]']) ? draft['classification[]'] : [draft['classification[]']];
                        classificationSelect.setValue(classes);
                    }

                    // 4. TomSelect (Modality) - Single
                    if (draft['modality']) {
                        const mod = Array.isArray(draft['modality']) ? draft['modality'][0] : draft['modality'];
                        modalitySelect.setValue(mod);
                    }

                    // 5. TomSelect (Type of L&D) - Single
                    if (draft['type_ld']) {
                        const type = Array.isArray(draft['type_ld']) ? draft['type_ld'][0] : draft['type_ld'];
                        typeSelect.setValue(type);
                    }

                    // 6. Trigger UI updates query
                    toggleOthersInput();
                    checkRelevantExpertise();

                } catch (e) {
                    console.error("Error restoring draft:", e);
                }
            };

            // Event Listeners for Saving
            form.addEventListener('input', saveDraft);
            form.addEventListener('change', saveDraft); // Bubbles from original selects too

            // Clear draft on submit
            form.addEventListener('submit', function () {
                localStorage.removeItem(STORAGE_KEY);
            });

            // Initial Restore
            restoreDraft();

        });
    </script>
</body>

</html>