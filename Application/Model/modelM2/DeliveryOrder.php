<?php
class DeliveryOrder
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function poHasActiveSubmission($po_number)
    {
        $stmt = $this->pdo->prepare("
        SELECT COUNT(*)
        FROM `do`
        WHERE PO_number = ?
        AND Status IN ('Under Review', 'Approved')
    ");

        $stmt->execute([$po_number]);

        return $stmt->fetchColumn() > 0;
    }

    public function getNextDOId()
    {
        $stmt = $this->pdo->query("
            SELECT IFNULL(MAX(DO_id), 40000) + 1 AS next_id
            FROM `do`
        ");

        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['next_id'];
    }

    public function createDO($data)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO `do`
            (
                DO_id,
                DO_number,
                PO_number,
                supplier_ID,
                Staff_id,
                DO_link,
                Proof_link,
                Status,
                Reason,
                created_by,
                created_date
            )
            VALUES
            (
                :DO_id,
                :DO_number,
                :PO_number,
                :supplier_ID,
                :Staff_id,
                :DO_link,
                :Proof_link,
                :Status,
                :Reason,
                :created_by,
                NOW()
            )
        ");

        $stmt->bindValue(":DO_id", $data["DO_id"], PDO::PARAM_INT);
        $stmt->bindValue(":DO_number", $data["DO_number"]);
        $stmt->bindValue(":PO_number", $data["PO_number"]);
        $stmt->bindValue(":supplier_ID", $data["supplier_ID"], PDO::PARAM_INT);
        $stmt->bindValue(":Staff_id", $data["Staff_id"], PDO::PARAM_INT);
        $stmt->bindValue(":DO_link", $data["DO_link"], PDO::PARAM_LOB);
        $stmt->bindValue(":Proof_link", $data["Proof_link"], PDO::PARAM_LOB);
        $stmt->bindValue(":Status", $data["Status"]);
        $stmt->bindValue(":Reason", $data["Reason"]);
        $stmt->bindValue(":created_by", $data["created_by"]);

        $stmt->execute();
    }

    public function getDOById($do_id, $supplier_id = null)
    {
        $sql = "
            SELECT
                d.DO_id,
                d.DO_number,
                d.PO_number,
                d.supplier_ID,
                d.Staff_id,
                d.Status,
                d.Reason,
                d.created_by,
                d.created_date,
                s.Supplier_name,
                s.Contact_person AS Contact_person,
                s.email,
                s.phone
            FROM `do` d
            JOIN supplier s ON d.supplier_ID = s.Supplier_id
            WHERE d.DO_id = ?
        ";

        $params = [$do_id];

        if ($supplier_id !== null) {
            $sql .= " AND d.supplier_ID = ?";
            $params[] = $supplier_id;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDOHistoryBySupplier($supplier_id, $month = '')
    {
        $sql = "
            SELECT
                d.DO_id,
                d.DO_number,
                d.PO_number,
                d.Status,
                d.created_date,
                s.Supplier_name
            FROM `do` d
            JOIN supplier s ON d.supplier_ID = s.Supplier_id
            WHERE d.supplier_ID = ?
        ";

        $params = [$supplier_id];

        if (!empty($month)) {
            $sql .= " AND DATE_FORMAT(d.created_date, '%Y-%m') = ?";
            $params[] = $month;
        }

        $sql .= " ORDER BY d.created_date DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFile($do_id, $type, $supplier_id = null)
    {
        $column = ($type === "proof") ? "Proof_link" : "DO_link";

        $sql = "SELECT $column FROM `do` WHERE DO_id = ?";
        $params = [$do_id];

        if ($supplier_id !== null) {
            $sql .= " AND supplier_ID = ?";
            $params[] = $supplier_id;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn();
    }
}