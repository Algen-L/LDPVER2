<?php
namespace App\Core;

class Controller
{
    public function view($__view_name, $data = [])
    {
        extract($data, EXTR_SKIP);
        $__view_path = BASE_PATH . "app/Views/" . $__view_name . ".php";

        if (file_exists($__view_path)) {
            require_once $__view_path;
        } else {
            // Fallback for legacy relative paths if BASE_PATH fails or for debugging
            require_once "../app/Views/" . $__view_name . ".php";
        }
    }

    public function redirect($url)
    {
        if (strpos($url, 'http') === 0 || strpos($url, '/') === 0) {
            header("Location: " . $url);
        } else {
            // Prepend the script name (public/index.php) to make it relative to the router
            $base = $_SERVER['SCRIPT_NAME'];
            header("Location: " . $base . "/" . ltrim($url, '/'));
        }
        exit;
    }

    // Helper to get database connection if needed
    protected function getDB()
    {
        $db = new \App\Config\Database();
        return $db->getConnection();
    }

    protected function sendEmail($to, $name, $subject, $body)
    {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ggeenggeen@gmail.com';
            $mail->Password = 'vvxx xvrx szws esno';
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('ggeenggeen@gmail.com', 'SDO L&D Passbook System');
            $mail->addAddress($to, $name);

            // Attach Logos
            $depedLogo = BASE_PATH . 'public/assets/logo.png';
            $ldpLogo = BASE_PATH . 'public/assets/LogoLDP.png';
            if (file_exists($depedLogo)) {
                $mail->addEmbeddedImage($depedLogo, 'deped_logo');
            }
            if (file_exists($ldpLogo)) {
                $mail->addEmbeddedImage($ldpLogo, 'ldp_logo');
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function getEmailTemplate($title, $subtitle, $code)
    {
        $year = date('Y');
        return "
<!DOCTYPE html>
<html>
<head>
    <style>
        .email-container { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f4f7f9; padding: 20px; }
        .card { background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        .header-gradient { background: linear-gradient(to left, #9ecdf8 0%, #005a8a 100%); padding: 40px 10px; text-align: center; }
        .header-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .header-cell { vertical-align: middle; text-align: center; padding: 0; }
        .logo-img { height: 75px; width: auto; display: block; margin: 0 auto; }
        .header-title-cell { width: 56%; }
        .logo-side-cell { width: 22%; }
        .header-title h2 { margin: 0; color: #ffffff; font-family: 'Segoe UI', Arial, sans-serif; font-size: 20px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.25; }
        @media screen and (max-width: 480px) { .logo-img { height: 55px; } .header-title h2 { font-size: 15px; } }
        .content { padding: 40px 20px; text-align: center; color: #334155; }
        .code-label { font-size: 16px; color: #64748b; margin-bottom: 10px; }
        .code-box { display: inline-block; padding: 15px 30px; background-color: #f1f5f9; border: 2px dashed #1b6ca8; border-radius: 12px; font-size: 36px; font-weight: 800; letter-spacing: 6px; color: #003366; margin: 25px 0; }
        .footer { padding: 25px; background-color: #f8fafc; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='card'>
            <div class='header-gradient'>
                <table class='header-table'>
                    <tr>
                        <td class='header-cell logo-side-cell'><img src='cid:deped_logo' class='logo-img' alt='DepEd Logo'></td>
                        <td class='header-cell header-title-cell'><div class='header-title'><h2>SCHOOLS DIVISION OFFICE<br>L&D PASSBOOK SYSTEM</h2></div></td>
                        <td class='header-cell logo-side-cell'><img src='cid:ldp_logo' style='height: 75px; width: auto; display: block; margin: 0 auto; filter: brightness(0.3) contrast(1.8); -webkit-filter: brightness(0.3) contrast(1.8);' alt='LDP Logo'></td>
                    </tr>
                </table>
            </div>
            <div class='content'>
                <p style='font-size: 18px; font-weight: 500;'>$title</p>
                <p class='code-label'>$subtitle</p>
                <div class='code-box'>$code</div>
                <p style='margin-top: 20px;'>If you didn't request this code, you can safely ignore this email.</p>
            </div>
            <div class='footer'>&copy; $year San Pedro Division Office - Learning & Development Unit<br>This is an automated message, please do not reply.</div>
        </div>
    </div>
</body>
</html>";
    }
}
