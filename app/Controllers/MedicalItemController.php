<?php

class MedicalItemController extends Controller
{
    public function index()
    {
        $this->guard('simrs_pharmacy.manage');
        $search = trim($_GET['search'] ?? '');
        $limit = 10;
        $currentPage = max(1, (int) ($_GET['p'] ?? 1));
        $model = new MedicalItem();
        $totalData = $model->countAll($search);
        $totalPages = max(1, (int) ceil($totalData / $limit));
        $currentPage = min($currentPage, $totalPages);
        $offset = ($currentPage - 1) * $limit;

        $this->view('simrs/medical-items/index', [
            'title' => 'Inventori Farmasi',
            'items' => $model->getPaginated($limit, $offset, $search),
            'search' => $search,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalData' => $totalData,
            'limit' => $limit,
        ]);
    }

    public function create()
    {
        $this->guard('simrs_pharmacy.manage');
        $this->view('simrs/medical-items/create', [
            'title' => 'Tambah Item Farmasi',
            'item' => [],
        ]);
    }

    public function store()
    {
        $this->guard('simrs_pharmacy.manage');
        $id = (new MedicalItem())->create($this->payload());
        activity_log('SIMRS - Inventori Farmasi', 'create', 'Item farmasi dibuat', $id);
        $this->redirect('simrs-medical-items');
    }

    public function edit()
    {
        $this->guard('simrs_pharmacy.manage');
        $item = (new MedicalItem())->find($_GET['id'] ?? null);
        if (!$item) {
            $this->redirect('simrs-medical-items');
        }

        $this->view('simrs/medical-items/edit', [
            'title' => 'Edit Item Farmasi',
            'item' => $item,
        ]);
    }

    public function update()
    {
        $this->guard('simrs_pharmacy.manage');
        $id = $_POST['id'] ?? null;
        (new MedicalItem())->update($id, $this->payload());
        activity_log('SIMRS - Inventori Farmasi', 'update', 'Item farmasi diperbarui', $id);
        $this->redirect('simrs-medical-items');
    }

    public function delete()
    {
        $this->guard('simrs_pharmacy.manage');
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if ($id) {
            (new MedicalItem())->deactivate($id);
            activity_log('SIMRS - Inventori Farmasi', 'delete', 'Item farmasi dinonaktifkan', $id);
        }

        $this->redirect('simrs-medical-items');
    }

    private function payload()
    {
        return [
            'sku' => trim($_POST['sku'] ?? ''),
            'name' => trim($_POST['name'] ?? ''),
            'category' => $_POST['category'] ?? 'obat',
            'item_type' => $_POST['item_type'] ?? 'medicine',
            'unit_name' => trim($_POST['unit_name'] ?? 'pcs'),
            'current_stock' => (float) ($_POST['current_stock'] ?? 0),
            'minimum_stock' => (float) ($_POST['minimum_stock'] ?? 0),
            'unit_price' => (float) str_replace('.', '', $_POST['unit_price'] ?? 0),
            'description' => trim($_POST['description'] ?? ''),
            'status' => $_POST['status'] ?? 'active',
        ];
    }

    private function guard($permission)
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }

        requirePermission($permission);
    }
}
