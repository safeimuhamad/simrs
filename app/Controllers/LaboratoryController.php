<?php

class LaboratoryController extends Controller
{
    public function index()
    {
        $this->guard();
        $search = trim($_GET['search'] ?? '');
        $rows = (new LabOrder())->list($search);
        $pagination = paginateRows($rows, 'simrs-laboratory', $search, 10);

        $this->view('simrs/laboratory/index', array_merge([
            'title' => 'Laboratorium',
            'rows' => $pagination['rows'],
        ], $pagination));
    }

    private function guard()
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }

        requirePermission('simrs_lab.manage');
    }
}
