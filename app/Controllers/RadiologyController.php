<?php

class RadiologyController extends Controller
{
    public function index()
    {
        $this->guard();
        $search = trim($_GET['search'] ?? '');
        $rows = (new RadiologyOrder())->list($search);
        $pagination = paginateRows($rows, 'simrs-radiology', $search, 10);

        $this->view('simrs/radiology/index', array_merge([
            'title' => 'Radiologi',
            'rows' => $pagination['rows'],
        ], $pagination));
    }

    private function guard()
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }

        requirePermission('simrs_radiology.manage');
    }
}
