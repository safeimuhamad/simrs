<?php

class SimrsNumber
{
    public static function make($table, $column, $prefix, $date = null, $pad = 4)
    {
        $db = Database::connect();
        $date = $date ?: date('Y-m-d');
        $stamp = date('Ymd', strtotime($date));
        $base = "{$prefix}-{$stamp}-";

        $stmt = $db->prepare("
            SELECT {$column}
            FROM {$table}
            WHERE {$column} LIKE ?
            ORDER BY {$column} DESC
            LIMIT 1
        ");
        $stmt->execute([$base . '%']);
        $last = (string) $stmt->fetchColumn();
        $next = 1;

        if ($last !== '') {
            $next = ((int) substr($last, -$pad)) + 1;
        }

        return $base . str_pad((string) $next, $pad, '0', STR_PAD_LEFT);
    }

    public static function queueNo($polyclinicId, $date = null)
    {
        $db = Database::connect();
        $date = $date ?: date('Y-m-d');
        $stmt = $db->prepare("SELECT queue_prefix FROM polyclinics WHERE id = ?");
        $stmt->execute([$polyclinicId]);
        $prefix = $stmt->fetchColumn() ?: 'A';

        $stmt = $db->prepare("
            SELECT queue_no
            FROM visit_queue
            WHERE polyclinic_id = ? AND queue_date = ?
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmt->execute([$polyclinicId, $date]);
        $last = (string) $stmt->fetchColumn();
        $next = $last !== '' ? ((int) substr($last, 1)) + 1 : 1;

        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}
