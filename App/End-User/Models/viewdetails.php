<?php
class RoomModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getRoomById($roomId) {
        $stmt = $this->pdo->prepare("SELECT * FROM rooms WHERE id = ?");
        $stmt->execute([$roomId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRoomAmenities($roomId) {
        $stmt = $this->pdo->prepare("SELECT amenity FROM amenities WHERE room_id = ?");
        $stmt->execute([$roomId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function reportIssue($roomId, $description) {
        $stmt = $this->pdo->prepare("SELECT room_number, status FROM rooms WHERE id = ?");
        $stmt->execute([$roomId]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$room) {
            return ['success' => false, 'message' => 'Room not found.'];
        }

        // Only allow reporting if room is occupied
        if (strtolower($room['status']) !== 'occupied') {
            return ['success' => false, 'message' => 'You can only report issues for rooms that are currently occupied.'];
        }

        $fullDescription = "Room {$room['room_number']}: " . trim($description);

        $stmt = $this->pdo->prepare("INSERT INTO notifications (room_id, description, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$roomId, $fullDescription]);

        return ['success' => true, 'message' => 'Issue reported successfully!'];
    }
}
