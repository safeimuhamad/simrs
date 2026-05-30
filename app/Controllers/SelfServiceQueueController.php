<?php

class SelfServiceQueueController extends Controller
{
    public function kiosk()
    {
        $model = new SelfServiceQueue();

        $this->frontView('self-service/kiosk', [
            'title' => 'Self Service Queue',
            'services' => $model->services(),
            'doctors' => (new Doctor())->active(),
            'recent' => array_merge($model->latestCalled(4), $model->waiting(4)),
            'ticket' => !empty($_GET['ticket_id']) ? $model->find($_GET['ticket_id']) : null,
        ]);
    }

    public function store()
    {
        $model = new SelfServiceQueue();

        try {
            $ticket = $model->createTicket(
                $_POST['service_type'] ?? '',
                $_POST['polyclinic_id'] ?? null,
                $_POST['doctor_id'] ?? null,
                $_POST['visitor_name'] ?? '',
                $_POST['phone'] ?? ''
            );

            $this->redirect('self-service-queue', ['ticket_id' => $ticket['id'], 'print' => 1]);
        } catch (Throwable $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('self-service-queue');
        }
    }

    public function display()
    {
        $model = new SelfServiceQueue();

        $this->frontView('self-service/display', [
            'title' => 'Display Antrean',
            'called' => $model->displayCalled(6),
            'waiting' => $model->displayWaiting(10),
            'summary' => $model->summary(),
        ]);
    }
}
