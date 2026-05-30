<?php

class AuthController extends Controller
{
    public function login()
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect($this->defaultLandingPage($_SESSION['permissions'] ?? []));
        }

        $this->view('auth/login', [
            'title' => 'Login',
            'auth_layout' => true
        ]);
    }

    public function processLogin()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['error'] = 'Email dan password wajib diisi.';
            $this->redirect('login');
        }

        if (!consumeRateLimit('login', 8, 300)) {
            $_SESSION['error'] = 'Terlalu banyak percobaan login. Silakan coba kembali beberapa menit lagi.';
            $this->redirect('login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {

            activity_log(
                'Authentication',
                'failed_login',
                'Login gagal untuk email: ' . $email
            );

            $_SESSION['error'] = 'Email atau password salah.';
            $this->redirect('login');
        }

        if (($user['status'] ?? '') !== 'active') {

            activity_log(
                'Authentication',
                'inactive_login',
                'Percobaan login akun nonaktif: ' . ($user['email'] ?? '-'),
                $user['id'] ?? null
            );

            $_SESSION['error'] = 'Akun Anda belum aktif atau sedang dinonaktifkan.';
            $this->redirect('login');
        }

        session_regenerate_id(true);
        clearRateLimit('login');
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];

        $_SESSION['user_role'] = $user['role_name'] ?? $user['role'] ?? null;
        $_SESSION['role_id'] = $user['role_id'] ?? null;
        $_SESSION['role_name'] = $user['role_name'] ?? null;
        $_SESSION['data_scope'] = $user['data_scope'] ?? 'own';
        $_SESSION['employee_id'] = $user['employee_id'] ?? null;

        $_SESSION['permissions'] = [];

        if (!empty($user['role_id'])) {
            $_SESSION['permissions'] = $userModel->getPermissionsByRoleId($user['role_id']);
        }

        $userModel->updateLastLogin($user['id']);

        activity_log(
            'Authentication',
            'login',
            'Login ke sistem',
            $user['id'],
            $user['email'] ?? null
        );

        $this->redirect($this->defaultLandingPage($_SESSION['permissions']));
    }

    private function defaultLandingPage(array $permissions)
    {
        return roleDashboardRoute($_SESSION['role_name'] ?? $_SESSION['user_role'] ?? null, $permissions);
    }
    
    public function logout()
    {
        activity_log(
            'Authentication',
            'logout',
            'Logout dari sistem',
            $_SESSION['user_id'] ?? null,
            $_SESSION['email'] ?? null
        );

        session_destroy();

        $this->redirect('login');
    }

    public function activateAccount()
    {
        $token = $_GET['token'] ?? '';

        if ($token === '') {
            $_SESSION['error'] = 'Token aktivasi tidak valid.';
            $this->redirect('login');
        }

        $userModel = new User();
        $user = $userModel->findByActivationToken($token);

        if (!$user) {
            $_SESSION['error'] = 'Link aktivasi tidak valid atau sudah kedaluwarsa.';
            $this->redirect('login');
        }

        $this->view('auth/activate-account', [
            'title' => 'Aktivasi Akun',
            'token' => $token,
            'user' => $user,
            'auth_layout' => true
        ]);
    }

    public function saveActivationPassword()
    {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if ($token === '' || $password === '' || $passwordConfirm === '') {
            $_SESSION['error'] = 'Password wajib diisi.';
            $this->redirect('activate-account', ['token' => $token]);
        }

        if ($password !== $passwordConfirm) {
            $_SESSION['error'] = 'Konfirmasi password tidak sama.';
            $this->redirect('activate-account', ['token' => $token]);
        }

        if (strlen($password) < 8) {
            $_SESSION['error'] = 'Password minimal 8 karakter.';
            $this->redirect('activate-account', ['token' => $token]);
        }

        $userModel = new User();
        $user = $userModel->findByActivationToken($token);

        if (!$user) {
            $_SESSION['error'] = 'Link aktivasi tidak valid atau sudah kedaluwarsa.';
            $this->redirect('login');
        }

        $userModel->activateAccount($user['id'], $password);

        activity_log(
            'Authentication',
            'activate_account',
            'Aktivasi akun berhasil',
            $user['id'],
            $user['email'] ?? null
        );

        $_SESSION['success'] = 'Akun berhasil diaktifkan. Silakan login.';
        $this->redirect('login');
    }

    public function forgotPassword()
    {
        $this->view('auth/forgot-password', [
            'title' => 'Lupa Password',
            'auth_layout' => true
        ]);
    }

    public function sendResetPassword()
    {
        $email = trim($_POST['email'] ?? '');

        if (!consumeRateLimit('password_reset_request', 3, 900)) {
            $_SESSION['error'] = 'Terlalu banyak permintaan reset password. Silakan coba kembali nanti.';
            $this->redirect('forgot-password');
        }

        if ($email === '') {
            $_SESSION['error'] = 'Email wajib diisi.';
            $this->redirect('forgot-password');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || ($user['status'] ?? '') !== 'active') {
            $_SESSION['success'] = 'Jika email terdaftar, link reset password akan dikirim.';
            $this->redirect('forgot-password');
        }

        $token = bin2hex(random_bytes(32));

        $userModel->saveResetToken($user['id'], $token);

        activity_log(
            'Authentication',
            'forgot_password',
            'Meminta reset password',
            $user['id'],
            $user['email'] ?? null
        );

        $resetLink = fullUrl('reset-password', [
            'token' => $token
        ]);

        Mailer::sendResetPasswordEmail(
            $user['email'],
            $user['name'],
            $resetLink
        );

        $_SESSION['success'] = 'Jika email terdaftar, link reset password akan dikirim.';
        $this->redirect('forgot-password');
    }

    public function resetPassword()
    {
        $token = $_GET['token'] ?? '';

        if ($token === '') {
            $_SESSION['error'] = 'Token reset password tidak valid.';
            $this->redirect('login');
        }

        $userModel = new User();
        $user = $userModel->findByResetToken($token);

        if (!$user) {
            $_SESSION['error'] = 'Link reset password tidak valid atau sudah kedaluwarsa.';
            $this->redirect('login');
        }

        $this->view('auth/reset-password', [
            'title' => 'Reset Password',
            'token' => $token,
            'user' => $user,
            'auth_layout' => true
        ]);
    }

    public function saveResetPassword()
    {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if ($token === '' || $password === '' || $passwordConfirm === '') {
            $_SESSION['error'] = 'Password wajib diisi.';
            $this->redirect('reset-password', ['token' => $token]);
        }

        if ($password !== $passwordConfirm) {
            $_SESSION['error'] = 'Konfirmasi password tidak sama.';
            $this->redirect('reset-password', ['token' => $token]);
        }

        if (strlen($password) < 8) {
            $_SESSION['error'] = 'Password minimal 8 karakter.';
            $this->redirect('reset-password', ['token' => $token]);
        }

        $userModel = new User();
        $user = $userModel->findByResetToken($token);

        if (!$user) {
            $_SESSION['error'] = 'Link reset password tidak valid atau sudah kedaluwarsa.';
            $this->redirect('login');
        }

        $userModel->updatePasswordByResetToken($user['id'], $password);

        activity_log(
            'Authentication',
            'reset_password',
            'Berhasil reset password',
            $user['id'],
            $user['email'] ?? null
        );

        $_SESSION['success'] = 'Password berhasil diperbarui. Silakan login.';
        $this->redirect('login');
    }
}
