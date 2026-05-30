<?php

require_once __DIR__ . '/../Core/Mailer.php';

class UserController extends Controller
{
    private function authorize($permissionKey)
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }

        requirePermission($permissionKey);
    }

    public function index()
    {
        $this->authorize('user.view');

        $model = new User();

        $limit = 10;
        $currentPage = isset($_GET['p']) ? (int) $_GET['p'] : 1;
        $currentPage = max($currentPage, 1);
        $search = trim((string) ($_GET['search'] ?? ''));
        $status = trim((string) ($_GET['status'] ?? ''));

        $totalData = $model->countAll($search, $status);
        $totalPages = max(1, (int) ceil($totalData / $limit));
        $currentPage = min($currentPage, $totalPages);
        $offset = ($currentPage - 1) * $limit;

        activity_log(
            'System - User',
            'view',
            'Melihat daftar user'
        );

        $this->view('users/index', [
            'title' => 'Master User',
            'users' => $model->getPaginated($limit, $offset, $search, $status),
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalData' => $totalData,
            'limit' => $limit,
            'search' => $search,
            'status' => $status
        ]);
    }

    public function create()
    {
        $this->authorize('user.create');

        $roleModel = new Role();

        activity_log(
            'System - User',
            'create_form',
            'Membuka form tambah user'
        );

        $this->view('users/create', [
            'title' => 'Tambah User',
            'roles' => $roleModel->all(),
        ]);
    }

    public function store()
    {
        $this->authorize('user.create');

        $model = new User();

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name === '' || $email === '') {

            activity_log(
                'System - User',
                'create_failed',
                'Gagal menambahkan user karena nama atau email kosong'
            );

            $_SESSION['error'] =
                'Nama dan email wajib diisi.';

            $this->redirect('users-create');
        }

        if ($model->findByEmail($email)) {

            activity_log(
                'System - User',
                'create_failed',
                'Gagal menambahkan user karena email sudah digunakan: ' . $email
            );

            $_SESSION['error'] =
                'Email sudah digunakan.';

            $this->redirect('users-create');
        }

        $token = bin2hex(random_bytes(32));

        $tempPassword = password_hash(
            bin2hex(random_bytes(16)),
            PASSWORD_DEFAULT
        );

        $username =
            explode('@', $email)[0] .
            rand(100, 999);

        $userId = $model->create([

            'name' => $name,
            'username' => $username,

            'email' => $email,
            'password' => $tempPassword,

            'role_id' => $_POST['role_id'] ?? null,
            'employee_id' => $_POST['employee_id'] ?? null,

            'data_scope' => $_POST['data_scope'] ?? 'own',

            'status' => 'pending',

            'activation_token' => $token,

            'activation_expires_at' =>
                date('Y-m-d H:i:s', strtotime('+24 hours'))
        ]);

        $activationLink = url(
            'activate-account',
            [
                'token' => $token
            ]
        );

        $emailSent = Mailer::sendActivationEmail(
            $email,
            $name,
            $activationLink
        );

        activity_log(
            'System - User',
            'create',
            'Menambahkan user: ' . $name . ' (' . $email . ')',
            $userId,
            $username
        );

        if ($emailSent) {

            activity_log(
                'System - User',
                'send_activation',
                'Mengirim email aktivasi ke user: ' . $email,
                $userId,
                $username
            );
        } else {

            activity_log(
                'System - User',
                'send_activation_failed',
                'Gagal mengirim email aktivasi ke user: ' . $email,
                $userId,
                $username
            );
        }

        $_SESSION['success'] = $emailSent
            ? 'User berhasil dibuat dan email aktivasi berhasil dikirim.'
            : 'User berhasil dibuat, tetapi email aktivasi gagal dikirim.';

        $this->redirect('users');
    }

    public function edit()
    {
        $this->authorize('user.edit');

        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect('users');
        }

        $model = new User();
        $roleModel = new Role();

        $user = $model->find($id);

        if (!$user) {

            activity_log(
                'System - User',
                'edit_failed',
                'Gagal membuka form edit user karena data tidak ditemukan',
                $id
            );

            $this->redirect('users');
        }

        activity_log(
            'System - User',
            'edit_form',
            'Membuka form edit user: ' . ($user['name'] ?? '-'),
            $id,
            $user['username'] ?? null
        );

        $this->view('users/edit', [
            'title' => 'Edit User',
            'user' => $user,
            'roles' => $roleModel->all(),
        ]);
    }

    public function update()
    {
        $this->authorize('user.edit');

        $id = $_POST['id'] ?? null;

        if (!$id) {
            $this->redirect('users');
        }

        $model = new User();

        $oldUser = $model->find($id);

        if (!$oldUser) {

            activity_log(
                'System - User',
                'update_failed',
                'Gagal update user karena data tidak ditemukan',
                $id
            );

            $this->redirect('users');
        }

        $model->update($id, [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'role_id' => $_POST['role_id'] ?? null,
            'employee_id' => $_POST['employee_id'] ?? null,
            'data_scope' => $_POST['data_scope'] ?? 'own',
            'status' => $_POST['status'] ?? 'active',
        ]);

        activity_log(
            'System - User',
            'update',
            'Mengubah user: ' . ($_POST['name'] ?? '-'),
            $id,
            $oldUser['username'] ?? null
        );

        $_SESSION['success'] = 'Data user berhasil diperbarui.';

        $this->redirect('users');
    }

    public function delete()
    {
        $this->authorize('user.delete');

        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect('users');
        }

        if ((int) $id === (int) ($_SESSION['user_id'] ?? 0)) {

            activity_log(
                'System - User',
                'delete_failed',
                'Gagal menghapus user karena mencoba menghapus akun sendiri',
                $id
            );

            $_SESSION['error'] = 'User yang sedang login tidak bisa dihapus.';
            $this->redirect('users');
        }

        $model = new User();
        $user = $model->find($id);

        if (!$user) {

            activity_log(
                'System - User',
                'delete_failed',
                'Gagal menghapus user karena data tidak ditemukan',
                $id
            );

            $this->redirect('users');
        }

        if ((int) ($user['role_id'] ?? 0) === 1) {

            activity_log(
                'System - User',
                'delete_failed',
                'Gagal menghapus super admin: ' . ($user['name'] ?? '-'),
                $id,
                $user['username'] ?? null
            );

            $_SESSION['error'] = 'Super Admin tidak bisa dihapus.';
            $this->redirect('users');
        }

        $model->delete($id);

        activity_log(
            'System - User',
            'delete',
            'Menghapus user: ' . ($user['name'] ?? '-'),
            $id,
            $user['username'] ?? null
        );

        $this->redirect('users');
    }
}
