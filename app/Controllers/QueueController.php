<?php

class QueueController extends Controller
{
    public function index()
    {
        $this->guard();
        [$doctorId, $polyclinicIds] = $this->doctorScope();
        $status = $_GET['status'] ?? '';
        $visitStatus = $status === 'serving' ? 'in_service' : $status;
        $selfServiceStatus = $status === 'in_service' ? 'serving' : $status;
        $queues = (new VisitQueue())->today($visitStatus, $doctorId, $polyclinicIds);

        if (!$doctorId && empty($polyclinicIds)) {
            $queues = array_merge($queues, (new SelfServiceQueue())->waitingForCalling($selfServiceStatus));
        }

        $pagination = paginateRows($queues, 'simrs-queue', trim($_GET['search'] ?? ''), 10);
        $this->view('simrs/queue/index', array_merge([
            'title' => 'Antrean Poli',
            'queues' => $pagination['rows'],
        ], $pagination));
    }

    public function updateStatus()
    {
        $this->guard();
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? 'called';
        $source = $_GET['source'] ?? 'visit';

        if ($source === 'self_service') {
            if ((new SelfServiceQueue())->changeStatus($id, $status)) {
                activity_log('SIMRS - Antrean Self Service', 'status', 'Status antrean self service berubah ke ' . $status, $id);
            }

            $this->redirect('simrs-queue');
        }

        $queue = (new VisitQueue())->find($id);

        if ($queue) {
            (new VisitQueue())->changeStatus($id, $status);
            $visitStatus = match ($status) {
                'in_service' => 'in_consultation',
                'done' => 'pharmacy',
                'cancelled' => 'cancelled',
                default => 'waiting',
            };
            (new PatientVisit())->changeStatus($queue['visit_id'], $visitStatus);
            activity_log('SIMRS - Antrean', 'status', 'Status antrean berubah ke ' . $status, $id, $queue['queue_no']);
        }

        $this->redirect('simrs-queue');
    }

    private function guard()
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        requirePermission('simrs_queue.manage');
    }

    private function doctorScope()
    {
        if (role_name() !== 'dokter') {
            return [null, []];
        }

        $doctorModel = new Doctor();
        $doctor = $doctorModel->currentForUser();
        $doctorId = $doctor['id'] ?? null;

        return [$doctorId, $doctorModel->assignedPolyclinicIds($doctorId)];
    }
}
