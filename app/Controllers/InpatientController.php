<?php

class InpatientController extends Controller
{
    public function index()
    {
        $this->guard();
        $search = trim($_GET['search'] ?? '');
        $rows = (new InpatientAdmission())->list($search);
        $pagination = paginateRows($rows, 'simrs-inpatient', $search, 10);

        $this->view('simrs/inpatient/index', array_merge([
            'title' => 'Rawat Inap',
            'rows' => $pagination['rows'],
        ], $pagination));
    }

    private function guard()
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }

        requirePermission('simrs_inpatient.manage');
    }
}
