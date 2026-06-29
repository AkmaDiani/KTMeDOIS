<?php
class Notification
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createNotification($user_id, $supplier_id, string $content): void
    {
        $stmt = $this->pdo->query("
            SELECT IFNULL(MAX(Notification), 60000) + 1 AS next_id
            FROM notification
        ");

        $notification_id = (int)$stmt->fetch(PDO::FETCH_ASSOC)["next_id"];

        $stmt = $this->pdo->prepare("
            INSERT INTO notification
            (
                Notification,
                User_ID,
                Supplier_id,
                Type,
                Content,
                Status,
                Creates_At
            )
            VALUES
            (
                ?,
                ?,
                ?,
                'System',
                ?,
                'Sent',
                NOW()
            )
        ");

        $stmt->execute([
            $notification_id,
            $user_id,
            $supplier_id,
            $content
        ]);
    }
}
