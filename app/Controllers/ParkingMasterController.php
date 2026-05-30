<?php

class ParkingMasterController extends Controller
{
    private array $configs = [
        'areas' => [
            'title' => 'Master Area Parkir',
            'model' => ParkingArea::class,
            'route' => 'parking-areas',
            'fields' => ['area_code' => 'Kode Area', 'name' => 'Nama Area', 'location' => 'Lokasi', 'capacity' => 'Kapasitas', 'reserved_capacity' => 'Slot Reserved', 'status' => 'Status'],
        ],
        'gates' => [
            'title' => 'Gate / Pintu',
            'model' => ParkingGate::class,
            'route' => 'parking-gates',
            'fields' => ['gate_code' => 'Kode Gate', 'name' => 'Nama Gate', 'gate_type' => 'Tipe Gate', 'area_id' => 'Area', 'device_type' => 'Device', 'device_endpoint' => 'Endpoint', 'status' => 'Status'],
        ],
        'vehicle-types' => [
            'title' => 'Kategori Kendaraan',
            'model' => ParkingVehicleType::class,
            'route' => 'parking-vehicle-types',
            'fields' => ['type_code' => 'Kode', 'name' => 'Nama', 'category' => 'Kategori', 'is_free' => 'Gratis', 'status' => 'Status'],
        ],
        'rates' => [
            'title' => 'Tarif Parkir',
            'model' => ParkingRate::class,
            'route' => 'parking-rates',
            'fields' => ['rate_code' => 'Kode Tarif', 'name' => 'Nama Tarif', 'vehicle_type_id' => 'Jenis Kendaraan', 'rate_type' => 'Tipe Tarif', 'initial_minutes' => 'Menit Awal', 'initial_rate' => 'Tarif Awal', 'next_hour_rate' => 'Tarif Jam Berikutnya', 'progressive_rate' => 'Tarif Progresif', 'max_daily_rate' => 'Maks Harian', 'grace_minutes' => 'Grace Period', 'lost_ticket_fee' => 'Denda Tiket Hilang', 'inpatient_special_rate' => 'Tarif Khusus Rawat Inap', 'status' => 'Status'],
        ],
        'members' => [
            'title' => 'Member Parkir',
            'model' => ParkingMember::class,
            'route' => 'parking-members',
            'fields' => ['member_no' => 'No Member', 'member_type' => 'Tipe Member', 'name' => 'Nama', 'plate_number' => 'Plat Nomor', 'vehicle_type_id' => 'Jenis Kendaraan', 'user_id' => 'User ID', 'employee_id' => 'Employee ID', 'doctor_id' => 'Doctor ID', 'valid_from' => 'Berlaku Dari', 'valid_to' => 'Berlaku Sampai', 'status' => 'Status', 'notes' => 'Catatan'],
        ],
    ];

    public function index($key)
    {
        $this->guard();
        $config = $this->config($key);
        if (($_GET['page'] ?? '') === 'parking-rate-settings') {
            $config['title'] = 'Pengaturan Tarif';
            $config['route'] = 'parking-rates';
        }
        $model = new $config['model']();
        $search = trim($_GET['search'] ?? '');
        $limit = 10;
        $currentPage = max(1, (int) ($_GET['p'] ?? 1));
        $totalData = $model->countAll($search);
        $totalPages = max(1, (int) ceil($totalData / $limit));
        $offset = ($currentPage - 1) * $limit;
        $this->view('parking/master/index', [
            'title' => $config['title'],
            'config' => $config,
            'items' => $model->all($search, $limit, $offset),
            'search' => $search,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalData' => $totalData,
            'limit' => $limit,
            'baseRoute' => $config['route'],
        ]);
    }

    public function create($key)
    {
        $this->guard();
        $config = $this->config($key);
        $this->view('parking/master/form', $this->formData($config, []));
    }

    public function store($key)
    {
        $this->guard();
        $config = $this->config($key);
        $model = new $config['model']();
        $id = $model->create($this->payload($config));
        activity_log('Parking - Master', 'create', 'Menambahkan ' . $config['title'], $id);
        $this->redirect($config['route']);
    }

    public function edit($key)
    {
        $this->guard();
        $config = $this->config($key);
        $model = new $config['model']();
        $item = $model->find($_GET['id'] ?? null);
        if (!$item) {
            $this->redirect($config['route']);
        }
        $this->view('parking/master/form', $this->formData($config, $item));
    }

    public function update($key)
    {
        $this->guard();
        $config = $this->config($key);
        $id = $_POST['id'] ?? null;
        (new $config['model']())->update($id, $this->payload($config));
        activity_log('Parking - Master', 'update', 'Mengubah ' . $config['title'], $id);
        $this->redirect($config['route']);
    }

    private function formData($config, $item)
    {
        return [
            'title' => (empty($item) ? 'Tambah ' : 'Edit ') . $config['title'],
            'config' => $config,
            'item' => $item,
            'areas' => (new ParkingArea())->active(),
            'vehicleTypes' => (new ParkingVehicleType())->active(),
        ];
    }

    private function payload($config)
    {
        $data = [];
        foreach ($config['fields'] as $field => $label) {
            $data[$field] = $_POST[$field] ?? null;
        }
        if (array_key_exists('is_free', $data)) {
            $data['is_free'] = !empty($_POST['is_free']) ? 1 : 0;
        }
        if (isset($data['plate_number'])) {
            $data['plate_number'] = strtoupper(trim($data['plate_number']));
        }
        return $data;
    }

    private function config($key)
    {
        if (!isset($this->configs[$key])) {
            $this->redirect('parking-dashboard');
        }
        return $this->configs[$key];
    }

    private function guard()
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        requirePermission('parking_master.manage');
    }
}
