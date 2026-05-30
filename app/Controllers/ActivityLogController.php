<?php

class ActivityLogController extends Controller
{
    public function index()
    {
        requirePermission('activity_logs.view');

        $model = new ActivityLog();

        $page = max(1, (int) ($_GET['p'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $logs = $model->paginate($limit, $offset);
        $total = $model->countAll();
        $totalPages = (int) ceil($total / $limit);

        $this->view('activity-logs/index', [
            'title' => 'Activity Logs',
            'limit' => $limit,
            'logs' => $logs,
            'page' => $page,
            'total' => $total,
            'totalPages' => $totalPages
        ]);
    }
}