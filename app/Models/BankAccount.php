<?php

class BankAccount
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getActive()
    {
        $stmt = $this->db->query("
            SELECT *
            FROM bank_accounts
            WHERE status = 'active'
            ORDER BY account_name ASC
            ");

        return $stmt->fetchAll();
    }

    public function increaseBalance($id, $amount)
    {
        $stmt = $this->db->prepare("
            UPDATE bank_accounts
            SET current_balance = current_balance + ?
            WHERE id = ?
            ");

        return $stmt->execute([$amount, $id]);
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM bank_accounts
            WHERE id = ?
            LIMIT 1
            ");

        $stmt->execute([$id]);

        return $stmt->fetch();
    }


    public function decreaseBalance($id, $amount)
    {
        $stmt = $this->db->prepare("
            UPDATE bank_accounts
            SET current_balance = current_balance - ?
            WHERE id = ?
            ");

        return $stmt->execute([$amount, $id]);
    }

public function create($data)
{
    $stmt = $this->db->prepare("
        INSERT INTO bank_accounts
        (
            account_code,
            account_name,
            bank_name,
            account_number,
            account_holder,
            current_balance,
            coa_id,
            is_active
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, 1)
    ");

    return $stmt->execute([
        $data['account_code'],
        $data['account_name'],
        $data['bank_name'],
        $data['account_number'],
        $data['account_holder'],
        $data['current_balance'],
        $data['coa_id']
    ]);
}

public function update($id, $data)
{
    $stmt = $this->db->prepare("
        UPDATE bank_accounts SET
            account_code = ?,
            account_name = ?,
            bank_name = ?,
            account_number = ?,
            account_holder = ?,
            current_balance = ?,
            coa_id = ?
        WHERE id = ?
    ");

    return $stmt->execute([
        $data['account_code'],
        $data['account_name'],
        $data['bank_name'],
        $data['account_number'],
        $data['account_holder'],
        $data['current_balance'],
        $data['coa_id'],
        $id
    ]);
}

}