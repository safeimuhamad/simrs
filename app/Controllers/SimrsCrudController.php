<?php

abstract class SimrsCrudController extends Controller
{
    protected $modelClass;
    protected $permission;
    protected $baseRoute;
    protected $viewPath;
    protected $title;
    protected $moduleName;

    public function index()
    {
        $this->guard($this->permission);
        $search = trim($_GET['search'] ?? '');
        $limit = 15;
        $currentPage = max(1, (int) ($_GET['p'] ?? 1));
        $offset = ($currentPage - 1) * $limit;
        $model = new $this->modelClass();
        $totalData = $model->countAll($search);

        activity_log($this->moduleName, 'view', 'Melihat daftar ' . $this->title);

        $this->view($this->viewPath . '/index', [
            'title' => $this->title,
            'items' => $model->all($search, $limit, $offset),
            'search' => $search,
            'currentPage' => $currentPage,
            'totalPages' => max(1, (int) ceil($totalData / $limit)),
            'totalData' => $totalData,
            'limit' => $limit,
            'baseRoute' => $this->baseRoute,
        ]);
    }

    public function create()
    {
        $this->guard($this->permission);
        $this->view($this->viewPath . '/create', $this->formData(['title' => 'Tambah ' . $this->title]));
    }

    public function store()
    {
        $this->guard($this->permission);
        $model = new $this->modelClass();
        $data = $this->payload();
        $id = $model->create($data);
        activity_log($this->moduleName, 'create', 'Membuat ' . $this->title, $id, $data['name'] ?? null);
        $_SESSION['success'] = $this->title . ' berhasil dibuat.';
        $this->redirect($this->baseRoute);
    }

    public function edit()
    {
        $this->guard($this->permission);
        $id = $_GET['id'] ?? null;
        $model = new $this->modelClass();
        $item = $id ? $model->find($id) : null;

        if (!$item) {
            $this->redirect($this->baseRoute);
        }

        $this->view($this->viewPath . '/edit', $this->formData([
            'title' => 'Edit ' . $this->title,
            'item' => $item,
        ]));
    }

    public function update()
    {
        $this->guard($this->permission);
        $id = $_POST['id'] ?? null;

        if (!$id) {
            $this->redirect($this->baseRoute);
        }

        $model = new $this->modelClass();
        $data = $this->payload();
        $model->update($id, $data);
        activity_log($this->moduleName, 'update', 'Mengubah ' . $this->title, $id, $data['name'] ?? null);
        $_SESSION['success'] = $this->title . ' berhasil diubah.';
        $this->redirect($this->baseRoute);
    }

    protected function guard($permission)
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }

        requirePermission($permission);
    }

    protected function formData($data)
    {
        return $data;
    }

    abstract protected function payload();
}
