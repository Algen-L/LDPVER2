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

</head>

<body class="auth-page">
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
            <div class="alert <?php echo strpos($message, 'successful') !== false ? 'alert-success' : 'alert-error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <div id="loginSection" class="form-section <?php echo !$isRegistration ? 'active' : ''; ?>">
            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password"
                        required>
                </div>
                <button type="submit" class="btn">Sign In</button>
            </form>
            <div class="footer-text">
                Don't have an account? <span class="toggle-link" onclick="toggleAuth(true)">Register here</span>
            </div>
        </div>

        <!-- Register Form -->
        <div id="registerSection" class="form-section <?php echo $isRegistration ? 'active' : ''; ?>">
            <form method="POST" action="">
                <input type="hidden" name="register" value="1">
                <div class="form-group">
                    <label>Full Name <span class="required-asterisk">*</span></label>
                    <input type="text" name="full_name" class="form-control" placeholder="John Doe" required>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Username <span class="required-asterisk">*</span></label>
                        <input type="text" name="reg_username" class="form-control" placeholder="j.doe" required>
                    </div>
                    <div class="form-group">
                        <label>Password <span class="required-asterisk">*</span></label>
                        <input type="password" name="reg_password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Office / Station</label>
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
                        <label>Position</label>
                        <input type="text" name="position" class="form-control" placeholder="Teacher I">
                    </div>
                    <div class="form-group">
                        <label>Specialization</label>
                        <input type="text" name="area_of_specialization" class="form-control" placeholder="Science">
                    </div>
                    <div class="form-group">
                        <label>Age</label>
                        <input type="number" name="age" class="form-control" placeholder="25">
                    </div>
                </div>

                <div class="form-grid grid-3">
                    <div class="form-group">
                        <label>Sex</label>
                        <select name="sex" class="form-control">
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="form-group span-2">
                        <label>Employee Number</label>
                        <input type="text" name="employee_number" class="form-control" placeholder="e.g. 1234567">
                    </div>
                </div>

                <button type="submit" class="btn">Register Account</button>
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
        function toggleAuth(isReg) {
            const container = document.getElementById('authContainer');
            const loginSec = document.getElementById('loginSection');
            const registerSec = document.getElementById('registerSection');
            const title = document.getElementById('authTitle');
            const subtitle = document.getElementById('authSubtitle');

            if (isReg) {
                container.classList.add('register-mode');
                loginSec.classList.remove('active');
                registerSec.classList.add('active');
                title.innerText = 'Create Account';
                subtitle.innerText = 'Fill in your details to get started';
            } else {
                container.classList.remove('register-mode');
                loginSec.classList.add('active');
                registerSec.classList.remove('active');
                title.innerText = 'SDO L&D Passbook System';
                subtitle.innerText = 'San Pedro Division Office';
            }
        }

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
    </script>
</body>

</html>