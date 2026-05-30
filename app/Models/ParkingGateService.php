<?php

class ParkingGateService
{
    public function openGate($gateId, $ticketId = null, $action = 'open_manual')
    {
        return $this->logGateActivity($ticketId, $gateId, $action, 'simulated', 'Gate opened in simulation mode.');
    }

    public function closeGate($gateId, $ticketId = null, $action = 'close_manual')
    {
        return $this->logGateActivity($ticketId, $gateId, $action, 'simulated', 'Gate closed in simulation mode.');
    }

    public function logGateActivity($ticketId, $gateId, $action, $status = 'simulated', $message = '')
    {
        return (new ParkingGateLog())->create([
            'ticket_id' => $ticketId ?: null,
            'gate_id' => $gateId,
            'action' => $action,
            'response_status' => $status,
            'response_message' => $message,
            'created_by' => $_SESSION['user_id'] ?? null,
        ]);
    }
}
