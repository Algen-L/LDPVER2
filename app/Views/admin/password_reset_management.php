<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Management - LDP</title>
    <?php require BASE_PATH . 'includes/admin_head.php'; ?>
    <style>
        :root {
            --card-bg: #ffffff;
            --stat-icon-bg: #f8fafc;
            --primary: #0ea5e9;
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --sidebar-bg: #ffffff;
            --main-bg: #f1f5f9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .icon-blue {
            background: #e0f2fe;
            color: #0284c7;
        }

        .icon-red {
            background: #fee2e2;
            color: #dc2626;
        }

        .icon-green {
            background: #dcfce7;
            color: #16a34a;
        }

        .icon-orange {
            background: #ffedd5;
            color: #ea580c;
        }

        .icon-purple {
            background: #f3e8ff;
            color: #9333ea;
        }

        .icon-cyan {
            background: #ecfeff;
            color: #0891b2;
        }

        .stat-info .stat-value {
            display: block;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }

        .stat-info .stat-label {
            font-size: 0.875rem;
            color: var(--secondary);
        }

        .management-card {
            background: var(--card-bg);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .search-box {
            position: relative;
            max-width: 400px;
            width: 100%;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary);
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .search-box input:focus {
            border-color: var(--primary);
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 0.75rem 0.5rem;
            color: var(--secondary);
            font-weight: 600;
            font-size: 0.75rem;
            background: #f8fafc;
        }

        td {
            padding: 0.75rem 0.5rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 0.825rem;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--secondary);
            font-size: 0.75rem;
        }

        .user-info .name {
            display: block;
            font-weight: 600;
            color: #1e293b;
        }

        .user-info .email {
            font-size: 0.75rem;
            color: var(--secondary);
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-active {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-blocked {
            background: #fee2e2;
            color: #dc2626;
        }

        .progress-mini {
            height: 5px;
            background: #f1f5f9;
            border-radius: 3px;
            width: 80px;
            margin-top: 4px;
        }

        .progress-bar {
            height: 100%;
            border-radius: 3px;
            background: var(--primary);
        }

        .action-btns {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .btn-mini {
            padding: 3px 6px;
            font-size: 0.65rem;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 3px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-mini:hover {
            background: #f8fafc;
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-mini i {
            font-size: 0.8rem;
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
                        <h1 class="page-title"><i class="bi bi-key-fill"></i> Password Reset Rate Limits</h1>
                    </div>
                </div>
            </header>

            <div class="content-wrapper" style="padding: 1.5rem;">
                <div class="stats-grid" id="statsGrid">
                    <!-- Stats will be loaded here via JS -->
                    <div class="stat-card">
                        <div class="stat-icon icon-blue"><i class="bi bi-people"></i></div>
                        <div class="stat-info"><span class="stat-value">...</span><span class="stat-label">Users with
                                Requests</span></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-red"><i class="bi bi-slash-circle"></i></div>
                        <div class="stat-info"><span class="stat-value">...</span><span class="stat-label">Blocked
                                Users</span></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-green"><i class="bi bi-check-circle"></i></div>
                        <div class="stat-info"><span class="stat-value">...</span><span class="stat-label">Active
                                Users</span></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-purple"><i class="bi bi-envelope"></i></div>
                        <div class="stat-info"><span class="stat-value">...</span><span class="stat-label">Total OTP
                                Requests</span></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-cyan"><i class="bi bi-eye"></i></div>
                        <div class="stat-info"><span class="stat-value">...</span><span class="stat-label">Page
                                Accesses</span></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-orange"><i class="bi bi-arrow-repeat"></i></div>
                        <div class="stat-info"><span class="stat-value">...</span><span class="stat-label">Total
                                Resends</span></div>
                    </div>
                </div>

                <div class="management-card">
                    <div class="table-header">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" id="userSearch" placeholder="Search by name or email..."
                                onkeyup="filterTable()">
                        </div>
                        <div class="header-actions">
                            <span style="color: var(--secondary); font-size: 0.875rem;" id="recordCount">Showing ...
                                records</span>
                        </div>
                    </div>

                    <div class="table-container">
                        <table id="securityTable">
                            <thead>
                                <tr>
                                    <th>USER</th>
                                    <th>ROLE</th>
                                    <th>PAGE VISITS</th>
                                    <th>OTP REQUESTS (1h)</th>
                                    <th>OTP INPUT</th>
                                    <th>RESENDS (1h)</th>
                                    <th>STATUS</th>
                                    <th>LAST ACTIVITY</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody id="securityTableBody">
                                <!-- Data will be loaded via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', loadSecurityStats);

        function loadSecurityStats() {
            fetch('<?php echo $route_prefix; ?>admin/get-security-stats')
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        showToast(data.error, 'error');
                        return;
                    }
                    updateDashboard(data);
                })
                .catch(e => showToast("Failed to load security stats", 'error'));
        }

        function updateDashboard(data) {
            // Update Stats
            const stats = data.stats;
            const statElements = document.querySelectorAll('.stat-value');
            statElements[0].innerText = stats.usersWithRequests;
            statElements[1].innerText = stats.blockedUsers;
            statElements[2].innerText = stats.activeUsers;
            statElements[3].innerText = stats.totalOtpRequests;
            statElements[4].innerText = stats.pageAccesses;
            statElements[5].innerText = stats.totalResends;

            // Update Table
            const tbody = document.getElementById('securityTableBody');
            tbody.innerHTML = '';

            data.users.forEach(u => {
                const row = document.createElement('tr');
                const lastActivity = u.last_activity !== 'N/A' ? formatDateTime(u.last_activity) : 'N/A';

                // Limits (hardcoded based on logic)
                const reqLimit = 3;
                const inputLimit = 5;
                const resendLimit = 10; // Assuming a soft limit for display

                row.innerHTML = `
                    <td>
                        <div class="user-cell">
                            ${u.profile_picture ? `<img src="../public/uploads/profile_pics/${u.profile_picture}" class="user-avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">` : `<div class="user-avatar">${u.full_name.charAt(0)}</div>`}
                            <div class="user-info">
                                <span class="name">${u.full_name}</span>
                                <span class="email">${u.email}</span>
                            </div>
                        </div>
                    </td>
                    <td><span style="font-size: 0.75rem; color: #64748b;">${u.role.toUpperCase()}</span></td>
                    <td><span style="color: #6366f1; font-weight: 600;">${u.page_visits}</span></td>
                    <td>
                        <div style="font-weight: 600;">${u.otp_requests}/${reqLimit}</div>
                        <div class="progress-mini"><div class="progress-bar" style="width: ${Math.min(100, (u.otp_requests / reqLimit) * 100)}%; background: ${u.otp_requests >= reqLimit ? 'var(--danger)' : 'var(--primary)'}"></div></div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">${u.otp_input_attempts || 0}/${inputLimit}</div>
                        <div class="progress-mini"><div class="progress-bar" style="width: ${Math.min(100, (u.otp_input_attempts / inputLimit) * 100)}%; background: ${u.otp_input_attempts >= inputLimit ? 'var(--danger)' : 'var(--warning)'}"></div></div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">${u.resends}/--</div>
                        <div class="progress-mini"><div class="progress-bar" style="width: ${Math.min(100, (u.resends / resendLimit) * 100)}%; background: var(--primary)"></div></div>
                    </td>
                    <td>
                        <span class="badge ${u.is_blocked ? 'badge-blocked' : 'badge-active'}">
                            <i class="bi ${u.is_blocked ? 'bi-lock-fill' : 'bi-shield-check'}"></i> ${u.is_blocked ? 'Blocked' : 'Active'}
                        </span>
                    </td>
                    <td style="font-size: 0.75rem; color: #64748b;">${lastActivity}</td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-mini" onclick="resetLimit('${u.email}', 'otp_limit')"><i class="bi bi-arrow-counterclockwise"></i> OTP Limit</button>
                            <button class="btn-mini" onclick="resetLimit('${u.email}', 'input_tries')"><i class="bi bi-pencil-square"></i> Input Tries</button>
                            <button class="btn-mini" onclick="resetLimit('${u.email}', 'resend_limit')"><i class="bi bi-send"></i> Resend Limit</button>
                            <button class="btn-mini" onclick="resetLimit('${u.email}', 'page_visits')"><i class="bi bi-eye"></i> Page Visits</button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });

            document.getElementById('recordCount').innerText = `Showing ${data.users.length} records`;
        }

        function resetLimit(email, type) {
            if (!confirm(`Are you sure you want to reset ${type.replace('_', ' ')} for ${email}?`)) return;

            const formData = new FormData();
            formData.append('email', email);
            formData.append('type', type);

            fetch('<?php echo $route_prefix; ?>admin/reset-security-limit', {
                method: 'POST',
                body: formData
            })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        showToast(data.message, 'success');
                        loadSecurityStats(); // Reload
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(e => showToast("Error resetting limit", 'error'));
        }

        function filterTable() {
            const input = document.getElementById("userSearch");
            const filter = input.value.toLowerCase();
            const table = document.getElementById("securityTable");
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                const tdName = tr[i].getElementsByClassName("name")[0];
                const tdEmail = tr[i].getElementsByClassName("email")[0];
                if (tdName || tdEmail) {
                    const text = (tdName.innerText + " " + tdEmail.innerText).toLowerCase();
                    tr[i].style.display = text.indexOf(filter) > -1 ? "" : "none";
                }
            }
        }

        function formatDateTime(dateTimeStr) {
            const date = new Date(dateTimeStr);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' ' +
                date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }); }
    </script>
</body>

</html>