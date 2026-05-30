<?php

class SimrsExtensionController extends Controller
{
    public function index(string $route)
    {
        $module = $this->module($route);
        $search = trim($_GET['search'] ?? '');
        $limit = 10;
        $page = max(1, (int) ($_GET['p'] ?? 1));
        $offset = ($page - 1) * $limit;
        $model = new SimrsExtension();
        $total = $model->count($module, $search);

        activity_log($module['title'], 'view', 'Melihat daftar ' . $module['title']);

        $this->view('simrs/extensions/index', [
            'title' => $module['title'],
            'module' => $module,
            'rows' => $model->paginate($module, $search, $limit, $offset),
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => max(1, (int) ceil($total / $limit)),
            'totalData' => $total,
            'limit' => $limit,
            'baseRoute' => $route,
        ]);
    }

    public function create(string $route)
    {
        $module = $this->module($route);
        $this->view('simrs/extensions/form', $this->formData($module, [
            'title' => 'Tambah ' . $module['title'],
            'item' => null,
        ]));
    }

    public function store(string $route)
    {
        $module = $this->module($route);
        $model = new SimrsExtension();
        $id = $model->create($module, $this->payload());
        activity_log($module['title'], 'create', 'Membuat data ' . $module['title'], $id, $_POST['subject'] ?? null);
        $_SESSION['success'] = $module['title'] . ' berhasil dibuat.';
        $this->redirect($route . '-show', ['id' => $id]);
    }

    public function show(string $route)
    {
        $module = $this->module($route);
        $item = (new SimrsExtension())->find($module, $_GET['id'] ?? null);

        if (!$item) {
            $_SESSION['error'] = 'Data tidak ditemukan.';
            $this->redirect($route);
        }

        activity_log($module['title'], 'show', 'Melihat detail ' . $module['title'], $item['id'], $item['record_no'] ?? null);
        $this->view('simrs/extensions/show', [
            'title' => 'Detail ' . $module['title'],
            'module' => $module,
            'item' => $item,
        ]);
    }

    public function edit(string $route)
    {
        $module = $this->module($route);
        $item = (new SimrsExtension())->find($module, $_GET['id'] ?? null);

        if (!$item) {
            $_SESSION['error'] = 'Data tidak ditemukan.';
            $this->redirect($route);
        }

        $this->view('simrs/extensions/form', $this->formData($module, [
            'title' => 'Edit ' . $module['title'],
            'item' => $item,
        ]));
    }

    public function update(string $route)
    {
        $module = $this->module($route);
        $id = $_POST['id'] ?? null;

        if (!$id) {
            $this->redirect($route);
        }

        (new SimrsExtension())->update($module, $id, $this->payload());
        activity_log($module['title'], 'update', 'Mengubah data ' . $module['title'], $id, $_POST['subject'] ?? null);
        $_SESSION['success'] = $module['title'] . ' berhasil diubah.';
        $this->redirect($route . '-show', ['id' => $id]);
    }

    private function module(string $route): array
    {
        $module = SimrsExtension::config($route);

        if (!$module) {
            http_response_code(404);
            exit('404 - Module not found');
        }

        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }

        requirePermission($module['permission']);

        return $module;
    }

    private function formData(array $module, array $data): array
    {
        $model = new SimrsExtension();
        return array_merge($data, [
            'module' => $module,
            'patients' => $model->patients(),
            'visits' => $model->visits(),
            'billings' => $model->billings(),
        ]);
    }

    private function payload(): array
    {
        $payload = [
            'record_no' => trim($_POST['record_no'] ?? ''),
            'patient_id' => $_POST['patient_id'] ?? null,
            'visit_id' => $_POST['visit_id'] ?? null,
            'medical_record_id' => $_POST['medical_record_id'] ?? null,
            'billing_id' => $_POST['billing_id'] ?? null,
            'reference_no' => trim($_POST['reference_no'] ?? ''),
            'module_type' => trim($_POST['module_type'] ?? ''),
            'subject' => trim($_POST['subject'] ?? ''),
            'record_date' => str_replace('T', ' ', $_POST['record_date'] ?? date('Y-m-d H:i:s')),
            'status' => $_POST['status'] ?? 'draft',
            'priority' => $_POST['priority'] ?? 'normal',
            'amount' => str_replace('.', '', $_POST['amount'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'assigned_to' => trim($_POST['assigned_to'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'payload_json' => trim($_POST['payload_json'] ?? ''),
            'interoperability_status' => $_POST['interoperability_status'] ?? 'not_ready',
        ];

        if ($payload['payload_json'] === '') {
            $payload['payload_json'] = json_encode([
                'source' => 'simrs_extension',
                'readiness' => $payload['interoperability_status'],
                'updated_at' => date('c'),
            ], JSON_UNESCAPED_SLASHES);
        }

        return $payload;
    }
}
