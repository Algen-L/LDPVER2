<?php
// Use constants if defined (MVC context), otherwise calculate relative paths (Legacy context)
if (defined('PUBLIC_ROOT')) {
    $path_to_public = PUBLIC_ROOT;
    $path_to_root = APP_ROOT;
} else {
    $current_script = $_SERVER['SCRIPT_NAME'];
    $current_page_dir = basename(dirname($current_script));

    if ($current_page_dir === 'admin') {
        $path_to_public = '../public/';
        $path_to_root = '../';
    } elseif ($current_page_dir === 'public') {
        $path_to_public = './';
        $path_to_root = '../';
    } else {
        $path_to_public = 'public/';
        $path_to_root = '';
    }
}
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
<meta name="theme-color" content="#0f4c75">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<!-- Base Styles -->
<link rel="stylesheet" href="<?php echo $path_to_public; ?>css/base/variables.css?v=3.0">
<!-- Layout Styles -->
<link rel="stylesheet" href="<?php echo $path_to_public; ?>css/layout/notifications.css?v=3.0">
<!-- Admin Panel Styles -->
<link rel="stylesheet" href="<?php echo $path_to_public; ?>css/layout/sidebar.css?v=3.0">
<link rel="stylesheet" href="<?php echo $path_to_public; ?>css/admin/admin.css?v=3.0">

<!-- Global Notification JS -->
<script src="<?php echo $path_to_public; ?>js/notifications.js"></script>

<!-- Real-Time Clock JS -->
<script>
    function updateClock() {
        const now = new Date();
        const options = {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        };
        const timeString = now.toLocaleTimeString('en-US', options);
        const clockElement = document.getElementById('real-time-clock');
        if (clockElement) {
            clockElement.textContent = timeString;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateClock();
        setInterval(updateClock, 1000);
    });
</script>

<?php if (isset($_SESSION['toast'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof showToast === 'function') {
                showToast(
                    "<?php echo htmlspecialchars($_SESSION['toast']['title']); ?>",
                    "<?php echo htmlspecialchars($_SESSION['toast']['message']); ?>",
                    "<?php echo htmlspecialchars($_SESSION['toast']['type']); ?>"
                );
            }
        });
    </script>
    <?php unset($_SESSION['toast']); ?>
<?php endif; ?>