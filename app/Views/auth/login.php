<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LDP Passbook - Login/Register</title>
    <!-- Adjusted path to head.php, assuming we are in public/ or it's just a view included -->
    <?php require __DIR__ . '/../../../includes/head.php'; ?>
    <link rel="stylesheet" href="css/pages/auth.css?v=<?php echo time(); ?>">
    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        /* Verification Code Styles */
        .verification-input-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }

        .code-digit {
            width: 45px;
            height: 50px;
            font-size: 24px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            transition: all 0.3s ease;
        }

        .code-digit:focus {
            border-color: #007bff;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
            outline: none;
            background: rgba(255, 255, 255, 0.1);
        }

        /* Toast Notification */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            background: rgba(30, 30, 30, 0.95);
            color: white;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border-left: 4px solid #EF4444;
            min-width: 300px;
            max-width: 400px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
        }

        .toast.toast-success {
            border-left-color: #10B981;
        }

        .toast.toast-error {
            border-left-color: #EF4444;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        .toast.removing {
            animation: slideOut 0.3s ease-in forwards;
        }

        /* Loading Spinner for Buttons */
        .btn-loading {
            position: relative;
            color: transparent !important;
            pointer-events: none;
            min-width: 100px;
        }

        .btn-loading::after {
            content: "";
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-top: -10px;
            margin-left: -10px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .status-loading {
            color: #60a5fa !important;
            /* Bright light blue */
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }
    </style>
</head>

<body class="auth-page">
    <div class="toast-container" id="toastContainer"></div>
    <div class="grid-background" id="gridBackground"></div>

    <div class="login-container <?php echo $isRegistration ? 'register-mode' : ''; ?>" id="authContainer">
        <div class="header">
            <div class="logo-container">
                <img src="assets/logo.png" alt="SDO Logo">
            </div>
            <h1 id="authTitle">
                <?php echo $isRegistration ? 'Create Account' : 'SDO L&D Passbook System'; ?>
            </h1>
            <p id="authSubtitle">
                <?php echo $isRegistration ? 'Fill in your details to get started' : 'San Pedro Division Office'; ?>
            </p>
        </div>

        <?php if ($message): ?>
            <script>
                // Show toast on page load for server messages
                document.addEventListener('DOMContentLoaded', function () {
                    const message = <?php echo json_encode($message); ?>;
                    const isSuccess = <?php echo json_encode(strpos($message, 'successful') !== false); ?>;
                    showToast(message, isSuccess ? 'success' : 'error', 5000);
                });
            </script>
        <?php endif; ?>

        <!-- Login Form -->
        <div id="loginSection" class="form-section <?php echo !$isRegistration ? 'active' : ''; ?>">
            <form id="loginForm" method="POST" action="">
                <div class="form-group">
                    <label>Gmail Address</label>
                    <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password"
                        required>
                </div>
                <button type="submit" class="btn">Sign In</button>
                <div style="text-align: right; margin-top: 10px;">
                    <span class="toggle-link" onclick="toggleForgotPassword(true)" style="font-size: 0.85rem;">Forgot
                        Password?</span>
                </div>
            </form>
            <div class="footer-text">
                Don't have an account? <span class="toggle-link" onclick="toggleAuth(true)">Register here</span>
            </div>
        </div>

        <!-- Forgot Password Section -->
        <div id="forgotPasswordSection" class="form-section">
            <div id="fpStep1">
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 20px;">
                    Enter your registered Gmail address and we'll send a reset token.
                </p>
                <div class="form-group">
                    <label>Gmail Address</label>
                    <input type="email" id="fp_email" class="form-control" placeholder="example@gmail.com">
                    <div style="margin-top: 10px; color: var(--text-secondary); font-size: 0.75rem; text-align: left;">
                        <i class="bi bi-info-circle"></i> Token expires in 5 minutes. (Maximum 3 requests per hour).
                    </div>
                </div>
                <button type="button" class="btn" id="fpRequestBtn" onclick="requestResetToken()">Send Reset
                    Token</button>
            </div>

            <div id="fpStep2" style="display: none;">
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 20px;">
                    Enter the 6-digit token sent to your email to verify your identity.
                </p>
                <div class="form-group">
                    <label>Verification Token</label>
                    <input type="text" id="fp_token" class="form-control" placeholder="6-digit token" maxlength="6">
                </div>
                <button type="button" class="btn" id="fpVerifyBtn" onclick="verifyResetToken()">Verify Token</button>
                <div style="margin-top: 15px; text-align: center;">
                    <span class="toggle-link" id="fpResendBtn" onclick="resendResetToken()" style="font-size: 0.85rem; color: var(--primary); cursor: pointer;">
                        <i class="bi bi-arrow-clockwise"></i> Resend Token
                    </span>
                </div>
            </div>

            <div id="fpStep3" style="display: none;">
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 20px;">
                    Token verified! Now set your new password.
                </p>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" id="fp_new_password" class="form-control" placeholder="••••••••">
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" id="fp_confirm_password" class="form-control" placeholder="••••••••">
                </div>
                <button type="button" class="btn" id="fpResetBtn" onclick="resetPassword()">Update Password</button>
            </div>

            <div class="footer-text">
                <span class="toggle-link" onclick="toggleForgotPassword(false)">Back to Login</span>
            </div>
        </div>

        <!-- Register Form -->
        <div id="registerSection" class="form-section <?php echo $isRegistration ? 'active' : ''; ?>">
            <form id="registerForm" method="POST" action="">
                <input type="hidden" name="register" value="1">

                <!-- STEP 1: Personal Details -->
                <div id="regStep1">
                    <div class="form-group">
                        <label>Full Name <span class="required-asterisk">*</span></label>
                        <input type="text" name="full_name" id="reg_full_name" class="form-control"
                            placeholder="John Doe" required>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Username <span class="required-asterisk">*</span></label>
                            <input type="text" name="reg_username" id="reg_username" class="form-control"
                                placeholder="j.doe" required>
                        </div>
                        <div class="form-group">
                            <label>Password <span class="required-asterisk">*</span></label>
                            <input type="password" name="reg_password" id="reg_password" class="form-control"
                                placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Office / Station <span class="required-asterisk">*</span></label>
                        <select name="office_station" id="office_select" class="form-control" required>
                            <option value="">Select your office...</option>
                            <?php if (!empty($offices_list)): ?>
                                <?php foreach ($offices_list as $category => $items): ?>
                                    <optgroup label="<?php echo htmlspecialchars($category); ?>">
                                        <?php foreach ($items as $office): ?>
                                            <option value="<?php echo htmlspecialchars($office['name']); ?>">
                                                <?php echo htmlspecialchars($office['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-grid grid-3">
                        <div class="form-group">
                            <label>Position <span class="required-asterisk">*</span></label>
                            <input type="text" name="position" id="position" class="form-control"
                                placeholder="Teacher I" required>
                        </div>
                        <div class="form-group">
                            <label>Specialization <span class="required-asterisk">*</span></label>
                            <input type="text" name="area_of_specialization" id="area_of_specialization"
                                class="form-control" placeholder="Science" required>
                        </div>
                        <div class="form-group">
                            <label>Age <span class="required-asterisk">*</span></label>
                            <input type="number" name="age" id="age" class="form-control" placeholder="25" required>
                        </div>
                    </div>

                    <div class="form-grid grid-3">
                        <div class="form-group">
                            <label>Sex <span class="required-asterisk">*</span></label>
                            <select name="sex" id="sex" class="form-control" required>
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="form-group span-2">
                            <label>Employee Number <span class="required-asterisk">*</span></label>
                            <input type="text" name="employee_number" id="employee_number" class="form-control"
                                placeholder="e.g. 1234567" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Gmail Address <span class="required-asterisk">*</span></label>
                        <input type="email" name="gmail" id="reg_email" class="form-control"
                            placeholder="example@gmail.com" required>
                        <small class="form-text text-muted">A valid Gmail address is required for account
                            notifications.</small>
                    </div>

                    <button type="button" class="btn" id="registerBtn" onclick="submitRegistration()">Next: Verify
                        Email</button>
                </div>

                <!-- STEP 2: Email Verification -->
                <div id="regStep2" style="display: none;">
                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 20px;">
                        We've sent a 6-digit verification code to <strong id="display_reg_email"></strong>. Please
                        enter it below to complete your registration.
                    </p>
                    <div class="form-group">
                        <label>Verification Code <span class="required-asterisk">*</span></label>
                        <div class="verification-input-container">
                            <input type="text" class="code-digit reg-digit" maxlength="1" pattern="\d*"
                                inputmode="numeric">
                            <input type="text" class="code-digit reg-digit" maxlength="1" pattern="\d*"
                                inputmode="numeric">
                            <input type="text" class="code-digit reg-digit" maxlength="1" pattern="\d*"
                                inputmode="numeric">
                            <input type="text" class="code-digit reg-digit" maxlength="1" pattern="\d*"
                                inputmode="numeric">
                            <input type="text" class="code-digit reg-digit" maxlength="1" pattern="\d*"
                                inputmode="numeric">
                            <input type="text" class="code-digit reg-digit" maxlength="1" pattern="\d*"
                                inputmode="numeric">
                        </div>
                        <input type="hidden" name="reg_verification_code" id="reg_verification_code">
                    </div>
                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="button" class="btn btn-secondary" onclick="backToStep1()"
                            style="flex: 1; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">Back</button>
                        <button type="button" class="btn" id="verifyRegBtn" onclick="verifyRegistrationCode()"
                            style="flex: 2;">Verify & Register</button>
                    </div>
                </div>
            </form>
            <div class="footer-text">
                Already have an account? <span class="toggle-link" onclick="toggleAuth(false)">Back to Login</span>
            </div>
        </div>

        <div class="footer-text auth-footer">
            Department of Education - San Pedro Division<br>
            <span class="dev-info">Developed by A.L and C.B</span>
        </div>
    </div>

    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        // Global functions called by HTML attributes
        function toggleAuth(isReg) {
            const container = document.getElementById('authContainer');
            const loginSec = document.getElementById('loginSection');
            const registerSec = document.getElementById('registerSection');
            const fpSec = document.getElementById('forgotPasswordSection');
            const title = document.getElementById('authTitle');
            const subtitle = document.getElementById('authSubtitle');

            if (isReg) {
                container.classList.add('register-mode');
                loginSec.classList.remove('active');
                registerSec.classList.add('active');
                fpSec.classList.remove('active');
                title.innerText = 'Create Account';
                subtitle.innerText = 'Fill in your details to get started';
                // Reset steps
                document.getElementById('regStep1').style.display = 'block';
                document.getElementById('regStep2').style.display = 'none';
            } else {
                container.classList.remove('register-mode');
                loginSec.classList.add('active');
                registerSec.classList.remove('active');
                fpSec.classList.remove('active');
                title.innerText = 'SDO L&D Passbook System';
                subtitle.innerText = 'San Pedro Division Office';
            }
        }

        function toggleForgotPassword(show) {
            const loginSec = document.getElementById('loginSection');
            const registerSec = document.getElementById('registerSection');
            const fpSec = document.getElementById('forgotPasswordSection');
            const title = document.getElementById('authTitle');
            const subtitle = document.getElementById('authSubtitle');

            if (show) {
                loginSec.classList.remove('active');
                registerSec.classList.remove('active');
                fpSec.classList.add('active');
                title.innerText = 'Reset Password';
                subtitle.innerText = 'Recover your account access';
                // Reset FP steps
                document.getElementById('fpStep1').style.display = 'block';
                document.getElementById('fpStep2').style.display = 'none';
            } else {
                fpSec.classList.remove('active');
                loginSec.classList.add('active');
                title.innerText = 'SDO L&D Passbook System';
                subtitle.innerText = 'San Pedro Division Office';
            }
        }

        function requestResetToken() {
            const emailInput = document.getElementById('fp_email');
            const email = emailInput.value.trim();
            const btn = document.getElementById('fpRequestBtn');
            const originalText = btn.innerHTML;

            if (!email) {
                showToast('Please enter your registered Gmail address.', 'warning');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';

            const formData = new FormData();
            formData.append('forgot_password', '1');
            formData.append('email', email);

            // Log the visit to the security dashboard
            const logData = new FormData();
            logData.append('log_reset_visit', '1');
            logData.append('email', email);
            fetch('', { method: 'POST', body: logData, headers: { 'X-Requested-With': 'XMLHttpRequest' } });

            fetch('', { // Changed from window.location.href
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showToast(data.message, 'success');
                        document.getElementById('fpStep1').style.display = 'none';
                        document.getElementById('fpStep2').style.display = 'block';

                        // Add email to Step 2 for resetting
                        let emailHidden = document.getElementById('reset_token_email');
                        if (!emailHidden) {
                            emailHidden = document.createElement('input');
                            emailHidden.type = 'hidden';
                            emailHidden.id = 'reset_token_email';
                            document.body.appendChild(emailHidden); // Global hidden or scoped
                        }
                        emailHidden.value = email;
                    } else {
                        showToast(data.message, 'error');
                    }
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                })
                .catch(error => {
                    showToast('An error occurred. Please try again.', 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        }

        function verifyResetToken() {
            const email = document.getElementById('reset_token_email').value;
            const token = document.getElementById('fp_token').value.trim();
            const btn = document.getElementById('fpVerifyBtn');
            const originalText = btn.innerHTML;

            if (!token) {
                showToast("Please enter the verification token.", 'warning');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Verifying...';

            const formData = new FormData();
            formData.append('verify_token', '1');
            formData.append('email', email);
            formData.append('token', token);

            fetch('', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        showToast(data.message, 'success');
                        document.getElementById('fpStep2').style.display = 'none';
                        document.getElementById('fp_confirm_password').parentElement.parentElement.parentElement.querySelector('#fpStep3').style.display = 'block';
                        // Correcting the selector above - actually just use IDs directly as they are unique
                        document.getElementById('fpStep3').style.display = 'block';
                    } else if (data.status === 'attempts_exceeded') {
                        showToast(data.message, 'error');
                        document.getElementById('fpStep2').style.display = 'none';
                        document.getElementById('fpStep1').style.display = 'block';
                        document.getElementById('fp_token').value = '';
                    } else {
                        showToast(data.message, 'error');
                    }
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                })
                .catch(e => {
                    showToast("An error occurred. Please try again.", 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        }

        function resendResetToken() {
            const email = document.getElementById('reset_token_email').value;
            const btn = document.getElementById('fpResendBtn');
            const originalText = btn.innerHTML;

            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.7';
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Resending...';

            const formData = new FormData();
            formData.append('forgot_password', '1');
            formData.append('email', email);

            fetch('', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(e => showToast("An error occurred. Please try again.", 'error'))
                .finally(() => {
                    setTimeout(() => {
                        btn.style.pointerEvents = 'auto';
                        btn.style.opacity = '1';
                        btn.innerHTML = originalText;
                    }, 2000);
                });
        }

        function startCooldown(btn, originalText, seconds = 300) {
            btn.disabled = true;
            const timer = setInterval(() => {
                seconds--;
                btn.innerText = `Wait ${seconds}s`;
                if (seconds <= 0) {
                    clearInterval(timer);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            }, 1000);
        }

        function resetPassword() {
            const email = document.getElementById('reset_token_email').value;
            const token = document.getElementById('fp_token').value.trim();
            const password = document.getElementById('fp_new_password').value.trim();
            const confirm = document.getElementById('fp_confirm_password').value.trim();
            const btn = document.getElementById('fpResetBtn');

            if (!password || !confirm) {
                showToast("Please fill in both password fields.", 'error');
                return;
            }

            if (password !== confirm) {
                showToast("Passwords do not match.", 'error');
                return;
            }

            btn.disabled = true;
            btn.innerText = "Updating...";

            const formData = new FormData();
            formData.append('reset_password', '1');
            formData.append('email', email);
            formData.append('token', token);
            formData.append('password', password);

            fetch(window.location.href, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        showToast(data.message, 'success');
                        setTimeout(() => toggleForgotPassword(false), 2000);
                    } else if (data.status === 'attempts_exceeded') {
                        showToast(data.message, 'error');
                        document.getElementById('fpStep3').style.display = 'none';
                        document.getElementById('fpStep1').style.display = 'block';
                    } else {
                        showToast(data.message, 'error');
                        btn.disabled = false;
                        btn.innerText = "Update Password";
                    }
                })
                .catch(e => {
                    showToast("An error occurred.", 'error');
                    btn.disabled = false;
                    btn.innerText = "Update Password";
                });
        }

        function submitRegistration() {
            // Validate ALL fields
            const requiredIds = [
                'reg_full_name',
                'reg_username',
                'reg_password',
                'office_select',
                'position',
                'area_of_specialization',
                'age',
                'sex',
                'employee_number',
                'reg_email'
            ];

            let isValid = true;
            requiredIds.forEach(id => {
                const el = document.getElementById(id);
                if (!el.value.trim()) {
                    isValid = false;
                    el.style.borderColor = 'red';
                } else {
                    el.style.borderColor = '';
                }
            });

            const email = document.getElementById('reg_email').value.trim();
            if (email && !email.endsWith('@gmail.com')) {
                isValid = false;
                document.getElementById('reg_email').style.borderColor = 'red';
                showToast('Please enter a valid Gmail address (must end with @gmail.com).', 'error', 4000);
            }

            if (!isValid) {
                if (email && !email.endsWith('@gmail.com')) return; // Already showed toast
                showToast("Please fill in all required fields.", 'error', 4000);
                return;
            }

            const registerBtn = document.getElementById('registerBtn');
            const originalText = registerBtn.innerText;
            registerBtn.disabled = true;
            registerBtn.classList.add('btn-loading');
            registerBtn.innerText = "Requesting code...";

            // Collect registration form data
            const regForm = document.getElementById('registerForm');
            const formData = new FormData(regForm);
            formData.append('request_registration_code', '1');

            // Submit registration request via AJAX
            fetch(window.location.href, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        showToast(data.message, 'success');
                        document.getElementById('display_reg_email').innerText = email;
                        document.getElementById('regStep1').style.display = 'none';
                        document.getElementById('regStep2').style.display = 'block';
                    } else {
                        showToast(data.message || "Request failed.", 'error', 4000);
                    }
                    registerBtn.disabled = false;
                    registerBtn.classList.remove('btn-loading');
                    registerBtn.innerText = originalText;
                })
                .catch(error => {
                    console.error('Registration Error:', error);
                    showToast("An error occurred. Please try again.", 'error', 4000);
                    registerBtn.disabled = false;
                    registerBtn.classList.remove('btn-loading');
                    registerBtn.innerText = originalText;
                });
        }

        function backToStep1() {
            document.getElementById('regStep1').style.display = 'block';
            document.getElementById('regStep2').style.display = 'none';
        }

        function verifyRegistrationCode() {
            const digits = document.querySelectorAll('.reg-digit');
            let code = '';
            digits.forEach(d => code += d.value);

            if (code.length !== 6) {
                showToast("Please enter the 6-digit verification code.", 'error');
                return;
            }

            const btn = document.getElementById('verifyRegBtn');
            const originalText = btn.innerText;
            btn.disabled = true;
            btn.classList.add('btn-loading');
            btn.innerText = "Verifying...";

            const formData = new FormData();
            formData.append('register', '1');
            formData.append('verify_registration_code', '1');
            formData.append('code', code);

            fetch(window.location.href, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        showToast(data.message, 'success', 6000);
                        setTimeout(() => {
                            window.location.href = window.location.href.split('?')[0];
                        }, 2000);
                    } else if (data.status === 'attempts_exceeded') {
                        showToast(data.message, 'error', 5000);
                        setTimeout(() => {
                            backToStep1();
                            // Clear digits
                            document.querySelectorAll('.reg-digit').forEach(d => d.value = '');
                            btn.disabled = false;
                            btn.classList.remove('btn-loading');
                            btn.innerText = originalText;
                        }, 2000);
                    } else {
                        showToast(data.message, 'error');
                        btn.disabled = false;
                        btn.classList.remove('btn-loading');
                        btn.innerText = originalText;
                    }
                })
                .catch(e => {
                    showToast("An error occurred. Please try again.", 'error');
                    btn.disabled = false;
                    btn.classList.remove('btn-loading');
                    btn.innerText = originalText;
                });
        }

        // Handle auto-focus for verification digits
        document.querySelectorAll('.reg-digit').forEach((input, index, array) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < array.length - 1) {
                    array[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                    array[index - 1].focus();
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            new TomSelect('#office_select', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "Type to search office...",
                maxOptions: 50
            });

            // Create animated grid background
            const gridBg = document.getElementById('gridBackground');
            const tileSize = 100;
            const gap = 2;
            const cols = Math.ceil(window.innerWidth / (tileSize + gap)) + 1;
            const rows = Math.ceil(window.innerHeight / (tileSize + gap)) + 1;
            const totalTiles = cols * rows;

            gridBg.style.gridTemplateColumns = `repeat(${cols}, ${tileSize}px)`;
            gridBg.style.gridTemplateRows = `repeat(${rows}, ${tileSize}px)`;

            for (let i = 0; i < totalTiles; i++) {
                const tile = document.createElement('div');
                tile.className = 'grid-tile';
                gridBg.appendChild(tile);
            }

            const tiles = document.querySelectorAll('.grid-tile');

            function randomGlow() {
                const randomTile = tiles[Math.floor(Math.random() * tiles.length)];
                randomTile.classList.add('glow');
                setTimeout(() => {
                    randomTile.classList.remove('glow');
                }, 2000);
            }

            setInterval(randomGlow, 400);

            gridBg.addEventListener('mousemove', (e) => {
                const rect = gridBg.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                tiles.forEach(tile => {
                    const tileRect = tile.getBoundingClientRect();
                    const tileCenterX = tileRect.left + tileRect.width / 2 - rect.left;
                    const tileCenterY = tileRect.top + tileRect.height / 2 - rect.top;

                    const distance = Math.sqrt(
                        Math.pow(x - tileCenterX, 2) + Math.pow(y - tileCenterY, 2)
                    );

                    if (distance < 150) {
                        tile.classList.add('active');
                    } else {
                        tile.classList.remove('active');
                    }
                });
            });

            gridBg.addEventListener('mouseleave', () => {
                tiles.forEach(tile => tile.classList.remove('active'));
            });
        });



        function showToast(message, type = 'error', duration = 4000) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'toast toast-' + type;

            const icon = type === 'success'
                ? `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`
                : `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>`;

            toast.innerHTML = icon + '<span>' + message + '</span>';
            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('removing');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }

    </script>
</body>

</html>