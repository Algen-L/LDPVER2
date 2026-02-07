<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile & Certificates - LDP Passbook</title>
    <?php require BASE_PATH . 'includes/head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo PUBLIC_ROOT; ?>css/pages/profile.css?v=<?php echo time(); ?>">
    <style>
        .ildn-list-scroll::-webkit-scrollbar-thumb:hover,
        .submissions-list-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .submissions-list-scroll {
            max-height: 520px;
            overflow-y: auto;
            padding-right: 12px;
            margin-right: -12px;
        }

        .submissions-list-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .submissions-list-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .submissions-list-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        @media (max-width: 992px) {
            .profile-main-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }

        .stat-card {
            background: #ffffff;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 16px;
            border: 1px solid #eef2f6;
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
            border-color: var(--primary-light);
        }

        /* Custom Modal Styles */
        .custom-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            animation: fadeIn 0.2s ease;
        }

        .custom-modal {
            background: white;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            text-align: center;
            transform: translateY(20px);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .custom-modal.show {
            transform: translateY(0);
        }

        .modal-icon-container {
            width: 60px;
            height: 60px;
            background: #fee2e2;
            color: #dc2626;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.75rem;
            margin: 0 auto 20px;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .modal-text {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
        }

        .modal-btn {
            flex: 1;
            padding: 12px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            border: none;
            transition: all 0.2s ease;
        }

        .modal-btn-cancel {
            background: #f1f5f9;
            color: #64748b;
        }

        .modal-btn-cancel:hover {
            background: #e2e8f0;
        }

        .modal-btn-delete {
            background: #dc2626;
            color: white;
        }

        .modal-btn-delete:hover {
            background: #b91c1c;
            box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.2);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            opacity: 0.9;
        }

        .stat-value {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
            display: block;
            line-height: 1.1;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #94a3b8;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .section-header {
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 16px 16px 0 0;
            border: 1px solid #eef2f6;
            border-bottom: none;
            margin-bottom: 0;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .section-title i {
            color: #F57C00;
        }

        .scrollable-cert-container {
            background: #f8fafc;
            border: 1px solid #eef2f6;
            border-radius: 0 0 16px 16px;
            padding: 20px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .certificate-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        .activity-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #eef2f6;
            padding: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            box-shadow: 0 4px 12px -2px rgba(15, 76, 117, 0.08);
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }

        .activity-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -5px rgba(15, 76, 117, 0.15);
            border-color: #F57C00;
        }

        .activity-type {
            font-size: 0.55rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #ffffff;
            background: var(--primary);
            padding: 2px 10px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 8px;
            letter-spacing: 0.8px;
            width: fit-content;
        }

        .activity-title {
            font-weight: 800;
            color: var(--primary);
            font-size: 0.85rem;
            margin-bottom: 8px;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.6em;
        }

        .activity-meta {
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 0.72rem;
            color: #64748b;
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #f1f5f9;
        }

        .activity-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        .activity-meta i {
            color: #F57C00;
            font-size: 0.85rem;
        }

        .cert-upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            background: #f8fafc;
            color: #64748b;
        }

        .cert-upload-zone:hover {
            background: #fff7ed;
            border-color: #F57C00;
            color: #F57C00;
        }

        .cert-upload-zone i {
            font-size: 1.4rem;
            display: block;
            margin-bottom: 4px;
        }

        .has-cert {
            background: #fff7ed;
            border: 1px solid #ffedd5;
            padding: 10px 14px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(245, 124, 0, 0.05);
        }

        .account-settings-card {
            display: none;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .toggle-settings-btn {
            background: white;
            color: #1e293b;
            border: 1px solid #e2e8f0;
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .toggle-settings-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .toggle-settings-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            color: #1e293b;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.2s;
            outline: none;
        }

        .form-control[readonly] {
            background: #f8fafc;
            border-color: #e2e8f0;
            color: #334155;
            cursor: default;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(15, 76, 117, 0.1);
            background: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 20px;
            border: 1px solid var(--card-border);
            grid-column: 1 / -1;
        }

        /* Certificate Filter Styles */
        .cert-filter-bar {
            display: flex;
            gap: 12px;
        }

        .cert-search-wrapper {
            position: relative;
            width: 240px;
        }

        .cert-search-wrapper i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .cert-filter-input {
            width: 100%;
            padding: 8px 12px 8px 36px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #334155;
            background: #f8fafc;
            transition: all 0.2s;
            outline: none;
            height: 38px;
        }

        .cert-filter-input:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(15, 76, 117, 0.1);
        }

        .cert-select-wrapper {
            position: relative;
            width: 160px;
        }

        .cert-select-wrapper i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 0.75rem;
            pointer-events: none;
        }

        .cert-filter-select {
            width: 100%;
            padding: 8px 32px 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #334155;
            background: #f8fafc;
            transition: all 0.2s;
            outline: none;
            height: 38px;
            appearance: none;
            cursor: pointer;
            font-weight: 600;
        }

        .cert-filter-select:focus {
            border-color: var(--primary);
            background: white;
        }

        @media (max-width: 600px) {
            .section-header {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .cert-filter-bar {
                width: 100%;
                flex-direction: column;
            }

            .cert-search-wrapper,
            .cert-select-wrapper {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="app-layout">
        <?php require BASE_PATH . 'includes/sidebar.php'; ?>

        <div class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <div class="breadcrumb">
                        <h1 class="page-title">My Profile</h1>
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
                    <button id="toggleSettings" class="toggle-settings-btn">
                        <i class="bi bi-person-gear"></i> Account Information
                    </button>
                </div>
            </header>

            <main class="content-wrapper">
                <div class="profile-container">

                    <!-- Hero Section -->
                    <div class="profile-hero">
                        <div class="hero-main">
                            <?php if (!empty($user['profile_picture'])): ?>
                                <img src="<?php echo PUBLIC_ROOT . htmlspecialchars($user['profile_picture']); ?>"
                                    class="hero-avatar">
                            <?php else: ?>
                                <div class="hero-avatar">
                                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            <div class="hero-info">
                                <h2>
                                    <?php echo htmlspecialchars($user['full_name']); ?>
                                </h2>
                                <p>
                                    <i class="bi bi-person-badge"></i>
                                    <?php echo htmlspecialchars($user['position'] ?: 'Employee'); ?>
                                    <span
                                        style="opacity: 0.5; margin: 0 4px; color: rgba(255,255,255,0.5) !important;">•</span>
                                    <i class="bi bi-building"></i>
                                    <?php echo htmlspecialchars($user['office_station']); ?>
                                </p>
                            </div>
                        </div>

                        <div class="hero-actions" style="display: flex; align-items: center; gap: 16px;">
                            <?php if (empty($user['rating_period'])): ?>
                                <div class="rating-period-alert" id="ratingPeriodAlert">
                                    <div class="alert-content">
                                        <div class="alert-icon-box">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                        </div>
                                        <div class="alert-text">
                                            <strong>Rating Period Missing</strong>
                                            <p>Please set your current Rating Period.</p>
                                        </div>
                                    </div>
                                    <button type="button" class="alert-action-btn"
                                        onclick="document.getElementById('toggleSettings').click(); document.getElementById('accountSettings').scrollIntoView({behavior: 'smooth'});">
                                        FIX NOW
                                    </button>
                                </div>
                            <?php endif; ?>

                            <!-- Message Log Icon -->
                            <a href="javascript:void(0)" onclick="openMessageModal()" class="messages-log-btn"
                                title="View Message Log">
                                <i class="bi bi-bell-fill msg-icon-img"
                                    style="font-size: 1.2rem; filter: brightness(0) invert(1);"></i>
                                <?php
                                $unreadCount = 0;
                                if (!empty($notifications)) {
                                    foreach ($notifications as $n) {
                                        if (!$n['is_read'])
                                            $unreadCount++;
                                    }
                                }
                                if ($unreadCount > 0):
                                    ?>
                                    <span class="msg-badge-dot"></span>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>

                    <!-- Account Information (Hidden by default) -->
                    <div id="accountSettings" class="account-settings-card">
                        <div class="dashboard-card" style="margin-bottom: 24px; border: 1px solid #e2e8f0;">
                            <div class="card-header"
                                style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 15px 25px;">
                                <h2 style="font-size: 1.1rem; margin: 0;"><i class="bi bi-shield-lock"></i> Account
                                    Settings</h2>
                            </div>
                            <div class="card-body" style="padding: 30px;">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="update_profile" value="1">
                                    <div class="form-group mb-4">
                                        <label class="form-label "
                                            style="display: block; margin-bottom: 15px; font-weight: 700; color: #475569; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Personal
                                            Avatar</label>
                                        <div
                                            style="display: flex; align-items: center; gap: 25px; background: #f8fafc; padding: 20px; border-radius: 20px; border: 1.5px solid #eef2f6;">
                                            <div id="avatarPreviewContainer"
                                                style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; border: 4px solid white; box-shadow: 0 10px 20px rgba(0,0,0,0.08); flex-shrink: 0; background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
                                                <?php if (!empty($user['profile_picture'])): ?>
                                                    <img src="<?php echo PUBLIC_ROOT . htmlspecialchars($user['profile_picture']); ?>"
                                                        id="currentAvatar"
                                                        style="width: 100%; height: 100%; object-fit: cover;">
                                                <?php else: ?>
                                                    <div
                                                        style="font-size: 2.5rem; font-weight: 800; color: var(--primary); opacity: 0.3;">
                                                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div style="flex: 1;">
                                                <div style="margin-bottom: 12px;">
                                                    <button type="button"
                                                        onclick="document.getElementById('profile_pic_input').click()"
                                                        class="btn btn-outline-primary"
                                                        style="height: 42px; padding: 0 20px; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                                                        <i class="bi bi-camera"></i> Update Photo
                                                    </button>
                                                    <input type="file" name="profile_picture" id="profile_pic_input"
                                                        style="display: none;" accept="image/*">
                                                </div>
                                                <p
                                                    style="margin: 0; color: #94a3b8; font-size: 0.8rem; font-weight: 500;">
                                                    Square images work best. Max size 2MB (JPG, PNG).
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" name="full_name" class="form-control"
                                                value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Employee Number</label>
                                            <input type="text" class="form-control"
                                                value="<?php echo htmlspecialchars($user['employee_number']); ?>"
                                                readonly>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Position / Designation</label>
                                            <input type="text" name="position" class="form-control"
                                                value="<?php echo htmlspecialchars($user['position']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Office / Station</label>
                                            <input type="text" name="office_station" class="form-control"
                                                value="<?php echo htmlspecialchars($user['office_station']); ?>"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Rating Period</label>
                                            <input type="text" name="rating_period" class="form-control"
                                                value="<?php echo htmlspecialchars($user['rating_period']); ?>"
                                                placeholder="e.g. Jan-Jun 2024" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Area of Specialization</label>
                                            <input type="text" name="area_of_specialization" class="form-control"
                                                value="<?php echo htmlspecialchars($user['area_of_specialization']); ?>"
                                                placeholder="e.g. English, ICT, Administration">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Age</label>
                                            <input type="number" name="age" class="form-control"
                                                value="<?php echo htmlspecialchars($user['age']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Sex</label>
                                            <select name="sex" class="form-control">
                                                <option value="Male" <?php echo $user['sex'] === 'Male' ? 'selected' : ''; ?>>
                                                    Male</option>
                                                <option value="Female" <?php echo $user['sex'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                                            </select>
                                        </div>
                                        <div class="form-group" style="grid-column: 1 / -1;">
                                            <label class="form-label">Update Password (Leave blank to keep
                                                current)</label>
                                            <input type="password" name="password" class="form-control"
                                                placeholder="Enter new password">
                                        </div>
                                    </div>

                                    <div
                                        style="margin-top: 30px; padding-top: 25px; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end;">
                                        <button type="submit" class="btn btn-primary"
                                            style="padding: 12px 30px; border-radius: 12px; font-weight: 700;">
                                            <i class="bi bi-check-circle"></i> Save All Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <!-- Top Section: ILDN + Stats -->
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;">
                        <!-- Left: ILDN Management -->
                        <div class="dashboard-card" style="box-shadow: 0 4px 12px rgba(15, 76, 117, 0.05);">
                            <div class="card-header"
                                style="background: white; border-bottom: 2px solid #f1f5f9; padding: 18px 25px; display: flex; justify-content: space-between; align-items: center;">
                                <h2 style="font-size: 1rem; color: var(--primary); font-weight: 800; margin: 0;"><i
                                        class="bi bi-lightbulb-fill" style="color: #F57C00;"></i> Individual Learning
                                    and Development Needs
                                </h2>
                            </div>
                            <div class="card-body" style="padding: 20px;">
                                <!-- Add ILDN Form -->
                                <form method="POST" style="margin-bottom: 20px;">
                                    <input type="hidden" name="add_ildn" value="1">
                                    <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                                        <input type="text" name="need_text" class="form-control"
                                            placeholder="Enter a learning need..." required
                                            style="flex: 1; padding: 12px 16px; border-radius: 10px;">
                                        <button type="submit" class="btn btn-primary"
                                            style="padding: 12px 24px; border-radius: 10px; white-space: nowrap;">
                                            <i class="bi bi-plus-lg"></i> Add
                                        </button>
                                    </div>
                                    <textarea name="description" class="form-control"
                                        placeholder="What is this all about? (Optional)"
                                        style="min-height: 60px; padding: 12px 16px; border-radius: 10px; resize: vertical;"></textarea>
                                </form>

                                <!-- ILDN List -->
                                <div class="ildn-list-scroll"
                                    style="max-height: 250px; overflow-y: auto; padding-right: 5px;">
                                    <?php if (!empty($user_ildns)): ?>
                                        <?php foreach ($user_ildns as $need): ?>
                                            <?php
                                            $isAddressed = $need['usage_count'] > 0;
                                            $bgColor = $isAddressed ? '#ecfdf5' : '#eff6ff';
                                            $borderColor = $isAddressed ? '#10b981' : '#3b82f6';
                                            $badgeBg = $isAddressed ? '#10b981' : '#3b82f6';
                                            ?>
                                            <div class="ildn-item"
                                                style="background: <?php echo $bgColor; ?>; border: 1px solid <?php echo $borderColor; ?>20; border-radius: 8px; padding: 8px 12px; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                                                <div
                                                    style="width: 24px; height: 24px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                    <i class="bi <?php echo $isAddressed ? 'bi-check-circle-fill' : 'bi-info-circle'; ?>"
                                                        style="color: <?php echo $borderColor; ?>; font-size: 0.9rem;"></i>
                                                </div>
                                                <div style="flex: 1; min-width: 0;">
                                                    <h4
                                                        style="font-size: 0.8rem; font-weight: 700; color: #1e293b; margin: 0; line-height: 1.2;">
                                                        <?php echo htmlspecialchars($need['need_text']); ?>
                                                    </h4>
                                                    <?php if (!empty($need['description'])): ?>
                                                        <p
                                                            style="font-size: 0.7rem; color: #64748b; margin: 2px 0 0 0; line-height: 1.2;">
                                                            <?php echo htmlspecialchars($need['description']); ?>
                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                                <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                                                    <?php if ($need['usage_count'] > 0): ?>
                                                        <span
                                                            style="font-size: 0.65rem; font-weight: 700; color: #64748b; background: white; padding: 2px 6px; border-radius: 4px;">
                                                            <?php echo $need['usage_count']; ?>×
                                                        </span>
                                                    <?php endif; ?>
                                                    <a href="?delete_ildn=<?php echo $need['id']; ?>"
                                                        onclick="return confirm('Remove this development need?')"
                                                        style="color: #94a3b8; font-size: 1rem; line-height: 1;"><i
                                                            class="bi bi-x"></i></a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div style="text-align: center; padding: 40px 0;">
                                            <i class="bi bi-lightbulb" style="font-size: 2rem; color: #cbd5e1;"></i>
                                            <p style="font-size: 0.8rem; color: #64748b; margin-top: 10px;">Define your
                                                development goals here.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Stats -->
                        <div>
                            <div class="card-header"
                                style="background: white; padding: 18px 25px; border-radius: 16px 16px 0 0; border: 1px solid #eef2f6; border-bottom: none;">
                                <h2 style="font-size: 1rem; color: var(--primary); font-weight: 800; margin: 0;">
                                    <i class="bi bi-bar-chart-fill" style="color: #F57C00;"></i> Stats
                                </h2>
                            </div>
                            <div
                                style="background: #f8fafc; padding: 20px; border-radius: 0 0 16px 16px; border: 1px solid #eef2f6; border-top: none;">
                                <?php
                                $total = $stats['total'] ?? 0;
                                $withCerts = 0;
                                $pendingCerts = 0;
                                foreach ($activities as $act) {
                                    if (!empty($act['certificate_path'])) {
                                        $withCerts++;
                                    } else {
                                        $pendingCerts++;
                                    }
                                }
                                $unaddressedNeeds = 0;
                                foreach ($user_ildns as $need) {
                                    if ($need['usage_count'] == 0) {
                                        $unaddressedNeeds++;
                                    }
                                }
                                ?>
                                <!-- Stat Cards -->
                                <div style="display: flex; flex-direction: column; gap: 12px;">
                                    <!-- Total Activities -->
                                    <div
                                        style="background: white; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px;">
                                        <div
                                            style="width: 48px; height: 48px; background: #dbeafe; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="bi bi-journal-text"
                                                style="color: #3b82f6; font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div
                                                style="font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1;">
                                                <?php echo $total; ?>
                                            </div>
                                            <div
                                                style="font-size: 0.7rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px;">
                                                TOTAL ACTIVITIES</div>
                                        </div>
                                    </div>

                                    <!-- With Certificates -->
                                    <div
                                        style="background: white; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px;">
                                        <div
                                            style="width: 48px; height: 48px; background: #d1fae5; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="bi bi-patch-check-fill"
                                                style="color: #10b981; font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div
                                                style="font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1;">
                                                <?php echo $withCerts; ?>
                                            </div>
                                            <div
                                                style="font-size: 0.7rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px;">
                                                WITH CERTIFICATES</div>
                                        </div>
                                    </div>

                                    <!-- Unaddressed Needs -->
                                    <div
                                        style="background: white; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px;">
                                        <div
                                            style="width: 48px; height: 48px; background: #fee2e2; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="bi bi-exclamation-circle-fill"
                                                style="color: #ef4444; font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div
                                                style="font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1;">
                                                <?php echo $unaddressedNeeds; ?>
                                            </div>
                                            <div
                                                style="font-size: 0.7rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px;">
                                                UNADDRESSED NEEDS</div>
                                        </div>
                                    </div>

                                    <!-- Pending Certs -->
                                    <div
                                        style="background: white; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px;">
                                        <div
                                            style="width: 48px; height: 48px; background: #fef3c7; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="bi bi-clock-history"
                                                style="color: #f59e0b; font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <div
                                                style="font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1;">
                                                <?php echo $pendingCerts; ?>
                                            </div>
                                            <div
                                                style="font-size: 0.7rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px;">
                                                PENDING CERTS</div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>

                    <!-- Bottom Section: Activity Certificates -->
                    <div class="certificate-hub-section">
                        <div class="section-header"
                            style="display: flex; justify-content: space-between; align-items: center;">
                            <h2 class="section-title"><i class="bi bi-award-fill" style="color: #F57C00;"></i> Activity
                                Certificates</h2>

                            <!-- Dynamic Filter Controls -->
                            <div class="cert-filter-bar">
                                <div class="cert-search-wrapper">
                                    <i class="bi bi-search"></i>
                                    <input type="text" id="certSearch" class="cert-filter-input"
                                        placeholder="Search certificates...">
                                </div>
                                <div class="cert-select-wrapper">
                                    <select id="certSort" class="cert-filter-select">
                                        <option value="all">All Status</option>
                                        <option value="ready">With Certificate</option>
                                        <option value="upload">Needs Upload</option>
                                    </select>
                                    <i class="bi bi-chevron-down"></i>
                                </div>
                            </div>
                        </div>

                        <div class="scrollable-cert-container">
                            <div class="certificate-grid" id="certificateGrid">
                                <?php if (!empty($activities)): ?>
                                    <?php foreach ($activities as $act): ?>
                                        <?php
                                        $hasCert = !empty($act['certificate_path']);
                                        $isRelevantExpertise = strpos($act['competency'], 'Relevant Expertise') !== false;
                                        ?>
                                        <div class="activity-card"
                                            data-title="<?php echo strtolower(htmlspecialchars($act['title'])); ?>"
                                            data-date="<?php echo strtotime($act['created_at']); ?>"
                                            style="background: white; border: 1px solid <?php echo $isRelevantExpertise ? '#c7d2fe' : '#e2e8f0'; ?>; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; gap: 14px; transition: all 0.2s ease;">

                                            <!-- Activity Type Badge -->
                                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                                <span
                                                    style="background: var(--primary); color: white; padding: 5px 14px; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                                                    <?php echo htmlspecialchars($act['type'] ?? 'SUPERVISORY'); ?>
                                                </span>
                                                <?php if ($isRelevantExpertise): ?>
                                                    <span
                                                        style="background: #e0e7ff; color: #4338ca; padding: 5px 14px; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 4px;">
                                                        <i class="bi bi-bookmark-star-fill"></i> RECORDED ENTRY
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Title -->
                                            <h3
                                                style="font-size: 1.05rem; font-weight: 700; color: var(--primary); margin: 0; line-height: 1.3;">
                                                <?php echo htmlspecialchars($act['title']); ?>
                                            </h3>

                                            <!-- Meta Information (Date & Location) -->
                                            <div
                                                style="display: flex; flex-direction: column; gap: 8px; font-size: 0.8rem; color: #f97316;">
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <i class="bi bi-calendar3" style="font-size: 0.9rem;"></i>
                                                    <span><?php echo date('M d, Y', strtotime($act['date_attended'])); ?></span>
                                                </div>
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <i class="bi bi-geo-alt" style="font-size: 0.9rem;"></i>
                                                    <span><?php echo htmlspecialchars($act['venue'] ?? 'SDO...'); ?></span>
                                                </div>
                                            </div>

                                            <!-- Certificate Status -->
                                            <?php if ($hasCert): ?>
                                                <div
                                                    style="margin-top: 8px; padding: 14px; background: #fff7ed; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
                                                    <div class="has-cert" style="display: flex; align-items: center; gap: 10px;">
                                                        <div
                                                            style="width: 32px; height: 32px; background: #f97316; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                            <i class="bi bi-check-lg"
                                                                style="color: white; font-size: 1.1rem; font-weight: 800;"></i>
                                                        </div>
                                                        <span
                                                            style="font-size: 0.85rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.3px;">
                                                            CERTIFICATE READY
                                                        </span>
                                                    </div>
                                                    <a href="<?php echo PUBLIC_ROOT . htmlspecialchars($act['certificate_path']); ?>"
                                                        target="_blank"
                                                        style="padding: 8px 20px; background: var(--primary); color: white; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        View
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <div
                                                    style="margin-top: 8px; padding: 20px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                                    <i class="bi bi-hourglass-split" style="font-size: 2rem; color: #94a3b8;"></i>
                                                    <span
                                                        style="font-size: 0.8rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.3px;">
                                                        PENDING
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="bi bi-journal-x"
                                            style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i>
                                        <p style="color: #64748b; font-weight: 600;">No activities recorded yet.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>



    <!-- Message Log Modal -->
    <div id="messageLogModal" class="custom-modal-overlay">
        <div class="custom-modal" style="max-width: 500px; text-align: left;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3
                    style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-bell-fill" style="color: #F57C00;"></i> Message Log
                </h3>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <?php if (!empty($notifications)): ?>
                        <button onclick="openClearLogModal()"
                            style="font-size: 0.8rem; color: #ef4444; font-weight: 700; text-decoration: none; padding: 4px 10px; background: #fee2e2; border-radius: 6px; border: none; cursor: pointer;">
                            Clear Log
                        </button>
                    <?php endif; ?>
                    <button onclick="closeMessageModal()"
                        style="background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer; padding: 0;">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>


            <div style="max-height: 400px; overflow-y: auto; padding-right: 5px;" class="submissions-list-scroll">
                <?php if (!empty($notifications)): ?>
                    <?php foreach ($notifications as $notif): ?>
                        <div
                            style="background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 12px;">
                            <p
                                style="font-size: 0.9rem; color: #1e293b; margin: 0 0 8px 0; font-weight: 600; line-height: 1.5;">
                                <?php echo htmlspecialchars($notif['message']); ?>
                            </p>
                            <div
                                style="font-size: 0.75rem; color: #64748b; display: flex; align-items: center; justify-content: space-between;">
                                <span><i class="bi bi-clock"></i>
                                    <?php echo date('M d, Y h:i A', strtotime($notif['created_at'])); ?></span>
                                <?php if (!$notif['is_read']): ?>
                                    <span
                                        style="color: #ef4444; font-weight: 700; background: #fee2e2; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem;">NEW</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px 0; color: #94a3b8;">
                        <i class="bi bi-bell-slash"
                            style="font-size: 3rem; margin-bottom: 15px; display: block; opacity: 0.5;"></i>
                        <p style="font-size: 0.9rem; margin: 0; font-weight: 600;">No messages found.</p>
                        <p style="font-size: 0.8rem; margin-top: 5px;">We'll notify you when something important happens.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Clear Log Confirmation Modal -->
    <div id="clearLogModal" class="custom-modal-overlay" style="z-index: 10000;">
        <div class="custom-modal" style="max-width: 400px; text-align: center; padding: 30px;">
            <div class="modal-icon-container" style="background: #fee2e2; color: #ef4444; margin: 0 auto 20px;">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <h3 class="modal-title" style="margin-bottom: 10px;">Clear Message Log?</h3>
            <p class="modal-text" style="color: #64748b; font-size: 0.95rem; margin-bottom: 25px;">
                Are you sure you want to delete all messages? This action cannot be undone.
            </p>
            <div class="modal-actions" style="display: flex; gap: 12px; justify-content: center;">
                <button onclick="closeClearLogModal()" class="modal-btn modal-btn-cancel"
                    style="flex: 1; border: none; cursor: pointer;">Cancel</button>
                <a href="?clear_notifications=1" class="modal-btn modal-btn-delete"
                    style="flex: 1; text-decoration: none; display: flex; align-items: center; justify-content: center; border: none;">Yes,
                    Clear All</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Settings Toggle
            const toggleBtn = document.getElementById('toggleSettings');
            const settingsCard = document.getElementById('accountSettings');
            toggleBtn.addEventListener('click', function () {
                const isHidden = settingsCard.style.display === 'none' || settingsCard.style.display === '';
                settingsCard.style.display = isHidden ? 'block' : 'none';
                this.classList.toggle('active', isHidden);
            });

            // Certificate Search & Filter
            const searchInput = document.getElementById('certSearch');
            const sortSelect = document.getElementById('certSort');
            const grid = document.getElementById('certificateGrid');

            function filterCerts() {
                const term = searchInput.value.toLowerCase();
                const status = sortSelect.value;
                const cards = Array.from(grid.querySelectorAll('.activity-card'));

                cards.forEach(card => {
                    const title = card.getAttribute('data-title');
                    const hasCert = card.querySelector('.has-cert') !== null;

                    let matchesSearch = title.includes(term);
                    let matchesStatus = true;

                    if (status === 'ready') {
                        matchesStatus = hasCert;
                    } else if (status === 'upload') {
                        matchesStatus = !hasCert;
                    }

                    card.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', filterCerts);
            sortSelect.addEventListener('change', filterCerts);

            // Avatar Preview
            const imgInput = document.getElementById('profile_pic_input');
            const preview = document.getElementById('currentAvatar');
            if (imgInput && preview) {
                imgInput.onchange = evt => {
                    const [file] = imgInput.files;
                    if (file) preview.src = URL.createObjectURL(file);
                }
            }
        });

        // Message Log Modal Functions
        function openMessageModal() {
            const modal = document.getElementById('messageLogModal');
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.querySelector('.custom-modal').classList.add('show');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        function closeMessageModal() {
            const modal = document.getElementById('messageLogModal');
            modal.querySelector('.custom-modal').classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }, 300);
        }

        // Clear Log Modal Functions
        function openClearLogModal() {
            const modal = document.getElementById('clearLogModal');
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.querySelector('.custom-modal').classList.add('show');
            }, 10);
        }

        function closeClearLogModal() {
            const modal = document.getElementById('clearLogModal');
            modal.querySelector('.custom-modal').classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        // Close modal when clicking outside
        document.getElementById('clearLogModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeClearLogModal();
            }
        });

        // Close modal when clicking outside
        document.getElementById('messageLogModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeMessageModal();
            }
        });
    </script>
</body>

</html>