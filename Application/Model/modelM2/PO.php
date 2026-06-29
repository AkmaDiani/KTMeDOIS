<?php
class PO
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function poNumberExists($po_number)
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM po
            WHERE PO_number = ?
        ");

        $stmt->execute([$po_number]);

        return $stmt->fetchColumn() > 0;
    }
}
?>