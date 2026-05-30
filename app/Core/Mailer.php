<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../public/vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/../../public/vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../public/vendor/phpmailer/src/SMTP.php';

class Mailer
{
    private static function configureSmtp(PHPMailer $mail)
    {
        $username = getenv('SMTP_USERNAME') ?: '';
        $password = getenv('SMTP_PASSWORD') ?: '';

        if ($username === '' || $password === '') {
            throw new Exception('Konfigurasi email belum tersedia.');
        }

        $mail->isSMTP();
        $mail->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->SMTPSecure = getenv('SMTP_ENCRYPTION') ?: PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = (int) (getenv('SMTP_PORT') ?: 587);
        $mail->setFrom(
            getenv('SMTP_FROM_ADDRESS') ?: $username,
            getenv('SMTP_FROM_NAME') ?: 'SIM Rumah Sakit'
        );
    }

    public static function sendActivationEmail($toEmail, $toName, $activationLink)
    {
        $mail = new PHPMailer(true);
        try {

            self::configureSmtp($mail);

            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);

            $mail->Subject = 'Aktivasi Akun SIM Rumah Sakit';

            $mail->Body = "
            <div style='
            font-family:Arial,sans-serif;
            font-size:14px;
            color:#333;
            line-height:1.7;
            '>

            <h2 style='color:#0d6efd; margin-bottom:20px;'>
            Aktivasi Akun SIM Rumah Sakit
            </h2>

            <p>Yth. <strong>{$toName}</strong>,</p>

            <p>
            Akun Anda telah berhasil terdaftar pada sistem
            <strong>SIM Rumah Sakit</strong>.
            </p>

            <p>
            Untuk mulai menggunakan sistem, silakan lakukan
            aktivasi akun melalui tombol berikut:
            </p>

            <p style='margin:30px 0;'>
            <a href='{$activationLink}'
            style='
            background:#0d6efd;
            color:#ffffff;
            padding:12px 24px;
            border-radius:6px;
            text-decoration:none;
            display:inline-block;
            font-weight:600;
            '>
            Aktivasi Akun
            </a>
            </p>

            <p>
            Link aktivasi ini berlaku selama
            <strong>24 jam</strong>.
            </p>

            <p>
            Jika Anda tidak merasa melakukan permintaan ini,
            silakan abaikan email ini.
            </p>

            <br>

            <p>
            Hormat kami,<br>
            <strong>SIM Rumah Sakit</strong><br>
            HVAC Engineering Solution
            </p>

            </div>
            ";

            return $mail->send();

        } catch (Exception $e) {
            return false;
        }
    }

    public static function sendResetPasswordEmail($toEmail, $toName, $resetLink)
    {
        $mail = new PHPMailer(true);

        try {

            self::configureSmtp($mail);

            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);

            $mail->Subject = 'Reset Password Akun SIM Rumah Sakit';

            $mail->Body = "
            <div style='
            font-family:Arial,sans-serif;
            font-size:14px;
            color:#333;
            line-height:1.7;
            '>

            <h2 style='color:#0d6efd; margin-bottom:20px;'>
            Reset Password SIM Rumah Sakit
            </h2>

            <p>Yth. <strong>{$toName}</strong>,</p>

            <p>
            Kami menerima permintaan untuk melakukan reset password
            pada akun <strong>SIM Rumah Sakit</strong> Anda.
            </p>

            <p>
            Silakan klik tombol berikut untuk membuat password baru:
            </p>

            <p style='margin:30px 0;'>
            <a href='{$resetLink}'
            style='
            background:#0d6efd;
            color:#ffffff;
            padding:12px 24px;
            border-radius:6px;
            text-decoration:none;
            display:inline-block;
            font-weight:600;
            '>
            Reset Password
            </a>
            </p>

            <p>
            Link reset password ini berlaku selama
            <strong>1 jam</strong>.
            </p>

            <p>
            Jika Anda tidak merasa melakukan permintaan ini,
            silakan abaikan email ini.
            </p>

            <br>

            <p>
            Hormat kami,<br>
            <strong>SIM Rumah Sakit</strong><br>
            HVAC Engineering Solution
            </p>

            </div>
            ";

            return $mail->send();

        } catch (Exception $e) {
            return false;
        }
    }
}
