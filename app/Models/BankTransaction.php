<?php

class BankTransaction
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO bank_transactions
            (
                bank_account_id,
                transaction_date,
                transaction_type,
                reference_type,
                reference_id,
                description,
                amount
                )
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

        return $stmt->execute([
            $data['bank_account_id'],
            $data['transaction_date'],
            $data['transaction_type'],
            $data['reference_type'],
            $data['reference_id'],
            $data['description'],
            $data['amount']
        ]);
    }

    public function getAll($bankAccountId = null)
    {
        $sql = "
        SELECT 
        bt.*,
        ba.account_name,
        ba.bank_name,
        ba.account_number
        FROM bank_transactions bt
        LEFT JOIN bank_accounts ba ON ba.id = bt.bank_account_id
        ";

        if (!empty($bankAccountId)) {
            $sql .= " WHERE bt.bank_account_id = :bank_account_id ";
        }

        $sql .= " ORDER BY bt.transaction_date DESC, bt.id DESC";

        $stmt = $this->db->prepare($sql);

        if (!empty($bankAccountId)) {
            $stmt->bindValue(':bank_account_id', $bankAccountId, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function deleteByReference($referenceType, $referenceId)
    {
        $stmt = $this->db->prepare("
            DELETE FROM bank_transactions
            WHERE reference_type = ?
            AND reference_id = ?
            ");

        return $stmt->execute([$referenceType, $referenceId]);
    }

    public function getCashFlow($startDate, $endDate)
    {
        $stmt = $this->db->prepare("
            SELECT
            bt.*,
            ba.account_code,
            ba.account_name,
            ba.bank_name,
            ba.account_number

            FROM bank_transactions bt

            LEFT JOIN bank_accounts ba
            ON ba.id = bt.bank_account_id

            WHERE bt.transaction_date BETWEEN ? AND ?

            ORDER BY bt.transaction_date ASC, bt.id ASC
            ");

        $stmt->execute([$startDate, $endDate]);

        return $stmt->fetchAll();
    }
}