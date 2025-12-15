<?php
class InventoryModel {
    private $pdo;
    private $max_use = 30;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    // Increment inventory when a room is booked
    public function bookRoom($roomId, $people) {
        // Update room's people count directly to the value submitted
        $stmt = $this->pdo->prepare("UPDATE rooms SET people = ? WHERE id = ?");
        $stmt->execute([$people, $roomId]);

        // Get all items
        $stmt = $this->pdo->query("SELECT id FROM items");
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as $item) {
            $check = $this->pdo->prepare("SELECT used_count FROM room_item_usage WHERE room_id = ? AND item_id = ?");
            $check->execute([$roomId, $item['id']]);
            $row = $check->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $newUsed = min($row['used_count'] + $people, $this->max_use);
                $update = $this->pdo->prepare("
                    UPDATE room_item_usage 
                    SET used_count = ?, condition_status = CASE WHEN ? >= ? THEN 'need to restock' ELSE 'good' END
                    WHERE room_id = ? AND item_id = ?
                ");
                $update->execute([$newUsed, $newUsed, $this->max_use, $roomId, $item['id']]);
            } else {
                $status = ($people >= $this->max_use) ? 'need to restock' : 'good';
                $insert = $this->pdo->prepare("
                    INSERT INTO room_item_usage (room_id, item_id, used_count, max_use, condition_status) 
                    VALUES (?, ?, ?, 30, ?)
                ");
                $insert->execute([$roomId, $item['id'], $people, $status]);
            }
        }
    }

    // Reset usage for an item
    public function restockItem($itemId) {
        $stmt = $this->pdo->prepare("
            UPDATE room_item_usage 
            SET used_count = 0, condition_status = 'good' 
            WHERE item_id = ?
        ");
        $stmt->execute([$itemId]);
    }

    // Get inventory status
    public function getInventoryStatus() {
        $stmt = $this->pdo->query("
            SELECT i.id, i.name, i.location,
                COALESCE(SUM(riu.used_count),0) AS used_count,
                30 AS max_use
            FROM items i
            LEFT JOIN room_item_usage riu ON i.id = riu.item_id
            GROUP BY i.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get inventory summary
    public function getSummaryStock() {
        $stmt = $this->pdo->query("
            SELECT i.name, i.location,
                (30 - COALESCE(SUM(riu.used_count),0)) AS stock
            FROM items i
            LEFT JOIN room_item_usage riu ON i.id = riu.item_id
            GROUP BY i.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
