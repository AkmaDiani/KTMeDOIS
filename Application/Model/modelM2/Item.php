<?php
class Item
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function itemNoExists($item_no): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM item
            WHERE item_no = ?
        ");
        $stmt->execute([$item_no]);

        return (int)$stmt->fetchColumn() > 0;
    }

    public function addItem($item_no, $description, $quantity, $do_id): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO item
            (
                item_no,
                item_description,
                quantity,
                DO_id
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?
            )
        ");

        $stmt->execute([
            $item_no,
            $description,
            $quantity,
            $do_id
        ]);
    }

    public function getItemsByDOId($do_id): array
    {
        $stmt = $this->pdo->prepare("
            SELECT item_no, item_description, quantity
            FROM item
            WHERE DO_id = ?
            ORDER BY item_no ASC
        ");
        $stmt->execute([$do_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
