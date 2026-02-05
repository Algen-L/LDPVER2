<?php
// Extracted variables from $data (handled by Controller::view)
// $user_to_edit, $message, $messageType, $user, $notifRepo
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - <?php echo htmlspecialchars($user_to_edit['full_name']); ?></title>
    <?php require BASE_PATH . 'includes/admin_head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/admin/edit_user.css?v=<?php echo time(); ?>">

</head>

<body>
    <div class="app-layout">
        <?php require BASE_PATH . 'includes/sidebar.php'; ?>
        <div class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <h1 class="page-title">Edit Personnel</h1>
                </div>
                <div class="top-bar-right">
                    <a href="<?php echo PUBLIC_ROOT; ?>index.php/admin/manage-users" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
            </header>

            <main class="content-wrapper">
                <form method="POST" enctype="multipart/form-data" class="edit-container">
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo ($messageType === 'success') ? 'success' : 'danger'; ?> mb-4">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-grid-main">
                        <!-- Left Column: Avatar -->
                        <div class="avatar-preview-section">
                            <?php if ($user_to_edit['profile_picture']): ?>
                                <img src="<?php echo PUBLIC_ROOT . $user_to_edit['profile_picture']; ?>"
                                    class="preview-circle" id="imgPreview">
                            <?php else: ?>
                                <div class="preview-circle" id="imgPlaceholder">
                                    <?php echo strtoupper(substr($user_to_edit['full_name'], 0, 1)); ?>
                                </div>
                                <img src="" class="preview-circle" id="imgPreview" style="display: none;">
                            <?php endif; ?>

                            <label class="btn btn-secondary btn-sm w-100 photo-upload-btn">
                                <i class="bi bi-camera"></i> Change Photo
                                <input type="file" name="profile_picture" hidden onchange="previewImage(this)">
                            </label>
                            <p class="text-muted mt-3 photo-upload-hint">JPG, PNG or WEBP. Max 2MB.</p>
                        </div>

                        <!-- Right Column: Details -->
                        <div class="edit-card">
                            <div class="form-section-title"><i class="bi bi-person-badge"></i> Personal details</div>
                            <div class="form-grid-inner">
                                <div class="form-group full-width">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-control" required
                                        value="<?php echo htmlspecialchars($user_to_edit['full_name']); ?>"
                                        placeholder="Full Name">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Age</label>
                                    <input type="number" name="age" class="form-control"
                                        value="<?php echo $user_to_edit['age']; ?>" placeholder="Enter age">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Sex</label>
                                    <select name="sex" class="form-select">
                                        <option value="Male" <?php echo $user_to_edit['sex'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo $user_to_edit['sex'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-section-title"><i class="bi bi-briefcase"></i> Professional assignment
                            </div>
                            <div class="form-grid-inner">
                                <div class="form-group full-width">
                                    <label class="form-label">Office / Station</label>
                                    <select name="office_station" id="office_select" class="form-control" required>
                                        <option value="<?php echo htmlspecialchars($user_to_edit['office_station']); ?>"
                                            selected>
                                            <?php echo htmlspecialchars($user_to_edit['office_station']); ?>
                                        </option>
                                        <optgroup label="OSDS">
                                            <option value="ADMINISTRATIVE (PERSONEL)">ADMINISTRATIVE (PERSONEL)</option>
                                            <option value="ADMINISTRATIVE (PROPERTY AND SUPPLY)">ADMINISTRATIVE
                                                (PROPERTY AND SUPPLY)</option>
                                            <option value="ADMINISTRATIVE (RECORDS)">ADMINISTRATIVE (RECORDS)</option>
                                            <option value="ADMINISTRATIVE (CASH)">ADMINISTRATIVE (CASH)</option>
                                            <option value="ADMINISTRATIVE (GENERAL SERVICES)">ADMINISTRATIVE (GENERAL
                                                SERVICES)</option>
                                            <option value="FINANCE (ACCOUNTING)">FINANCE (ACCOUNTING)</option>
                                            <option value="FINANCE (BUDGET)">FINANCE (BUDGET)</option>
                                            <option value="LEGAL">LEGAL</option>
                                            <option value="ICT">ICT</option>
                                        </optgroup>
                                        <optgroup label="SGOD">
                                            <option value="SCHOOL GOVERNANCE AND OPERATION DIVISION">SCHOOL GOVERNANCE
                                                AND OPERATION DIVISION</option>
                                            <option value="SCHOOL MANAGEMENT MONITORING & EVALUATION">SCHOOL MANAGEMENT
                                                MONITORING & EVALUATION</option>
                                            <option value="HUMAN RESOURCES DEVELOPMENT">HUMAN RESOURCES DEVELOPMENT
                                            </option>
                                            <option value="DISASTER RISK REDUCTION AND MANAGEMENT">DISASTER RISK
                                                REDUCTION AND MANAGEMENT</option>
                                            <option value="EDUCATION FACILITIES">EDUCATION FACILITIES</option>
                                            <option value="SCHOOL HEALTH AND NUTRITION">SCHOOL HEALTH AND NUTRITION
                                            </option>
                                            <option value="SCHOOL HEALTH AND NUTRITION (DENTAL)">SCHOOL HEALTH AND
                                                NUTRITION (DENTAL)</option>
                                            <option value="SCHOOL HEALTH AND NUTRITION (MEDICAL)">SCHOOL HEALTH AND
                                                NUTRITION (MEDICAL)</option>
                                        </optgroup>
                                        <optgroup label="CID">
                                            <option value="CURRICULUM IMPLEMENTATION DIVISION">CURRICULUM IMPLEMENTATION
                                                DIVISION</option>
                                            <option
                                                value="CURRICULUM IMPLEMENTATION DIVISION (INSTRUCTIONAL MANAGEMENT)">
                                                CURRICULUM IMPLEMENTATION DIVISION (INSTRUCTIONAL MANAGEMENT)</option>
                                            <option
                                                value="CURRICULUM IMPLEMENTATION DIVISION (LEARNING RESOURCES MANAGEMENT)">
                                                CURRICULUM IMPLEMENTATION DIVISION (LEARNING RESOURCES MANAGEMENT)
                                            </option>
                                            <option
                                                value="CURRICULUM IMPLEMENTATION DIVISION (ALTERNATIVE LEARNING SYSTEM)">
                                                CURRICULUM IMPLEMENTATION DIVISION (ALTERNATIVE LEARNING SYSTEM)
                                            </option>
                                            <option
                                                value="CURRICULUM IMPLEMENTATION DIVISION (DISTRICT INSTRUCTIONAL SUPERVISION)">
                                                CURRICULUM IMPLEMENTATION DIVISION (DISTRICT INSTRUCTIONAL SUPERVISION)
                                            </option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="form-group full-width">
                                    <label class="form-label">Position / Designation</label>
                                    <input type="text" name="position" class="form-control"
                                        value="<?php echo htmlspecialchars($user_to_edit['position']); ?>"
                                        placeholder="Enter position">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Employee Number</label>
                                    <input type="text" name="employee_number" class="form-control"
                                        value="<?php echo htmlspecialchars($user_to_edit['employee_number'] ?? ''); ?>"
                                        placeholder="e.g. 1234567">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Rating Period</label>
                                    <input type="text" name="rating_period" class="form-control"
                                        value="<?php echo htmlspecialchars($user_to_edit['rating_period'] ?? ''); ?>"
                                        placeholder="e.g. 2025">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Area of Specialization</label>
                                    <input type="text" name="area_of_specialization" class="form-control"
                                        value="<?php echo htmlspecialchars($user_to_edit['area_of_specialization'] ?? ''); ?>"
                                        placeholder="e.g. Management">
                                </div>
                            </div>

                            <div class="form-section-title"><i class="bi bi-shield-lock"></i> Account access</div>
                            <div class="form-grid-inner">
                                <div class="form-group">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" required
                                        value="<?php echo htmlspecialchars($user_to_edit['username']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Reset Password (Blank to keep)</label>
                                    <input type="password" name="password" class="form-control" placeholder="••••••••">
                                </div>
                                <?php if ($_SESSION['role'] === 'super_admin' || $_SESSION['role'] === 'head_hr'): ?>
                                    <div class="form-group full-width">
                                        <label class="form-label">System Role</label>
                                        <select name="role" class="form-select">
                                            <option value="user" <?php echo $user_to_edit['role'] === 'user' ? 'selected' : ''; ?>>L&D Personnel</option>
                                            <option value="hr" <?php echo $user_to_edit['role'] === 'hr' ? 'selected' : ''; ?>>HR Personnel</option>
                                            <option value="immediate_head" <?php echo $user_to_edit['role'] === 'immediate_head' ? 'selected' : ''; ?>>Immediate
                                                Head</option>
                                            <option value="head_hr" <?php echo $user_to_edit['role'] === 'head_hr' ? 'selected' : ''; ?>>Head HR</option>
                                            <option value="admin" <?php echo $user_to_edit['role'] === 'admin' ? 'selected' : ''; ?>>System Admin</option>
                                            <option value="super_admin" <?php echo $user_to_edit['role'] === 'super_admin' ? 'selected' : ''; ?>>Super Admin</option>
                                        </select>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mt-2 text-center">
                                <button type="submit" class="btn-save">
                                    <i class="bi bi-check-circle"></i> Save All Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const officeSelect = document.getElementById('office_select');
            if (officeSelect) {
                new TomSelect('#office_select', {
                    create: true,
                    placeholder: 'Select or type office...'
                });
            }
        });

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById('imgPreview');
                    const placeholder = document.getElementById('imgPlaceholder');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if (placeholder) placeholder.style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>