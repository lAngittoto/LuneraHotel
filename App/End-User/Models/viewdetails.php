
<?php
class RoomModel {
    private $pdo;
    private $uploadDir; // absolute
    private $publicPath; // web accessible prefix

    public function __construct($pdo) {
        $this->pdo = $pdo;
        // Upload directory inside public folder
        $this->uploadDir = realpath(__DIR__ . '/../../public') . '/uploads/issues';
        if ($this->uploadDir === false) {
            // fallback to project path
            $this->uploadDir = __DIR__ . '/../../../public/uploads/issues';
        }
        $this->publicPath = '/LuneraHotel/App/Public/uploads/issues';
        if (!is_dir($this->uploadDir)) {
            @mkdir($this->uploadDir, 0755, true);
        }
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

    /**
     * Report an issue for a room.
     * - Allows reporting regardless of room status (occupied/available/booked) for testing.
     * - Accepts array of uploaded files (from $_FILES).
     * - Stores images into public/uploads/issues and saves JSON list in notifications.images
     */
    public function reportIssue($roomId, $description, $files = []) {
        // fetch basic room info
        $stmt = $this->pdo->prepare("SELECT room_number, status FROM rooms WHERE id = ?");
        $stmt->execute([$roomId]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$room) {
            return ['success' => false, 'message' => 'Room not found.'];
        }

        $fullDescription = "Room {$room['room_number']}: " . trim($description);

        // handle files upload
        $savedFiles = [];
        if (!empty($files) && isset($files['name']) && is_array($files['name'])) {
            $count = count($files['name']);
            // limit to 5 files
            $limit = min($count, 5);
            for ($i = 0; $i < $limit; $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $tmpName = $files['tmp_name'][$i];
                $origName = basename($files['name'][$i]);
                $size = $files['size'][$i];

                // basic validations
                if ($size <= 0 || $size > 5 * 1024 * 1024) { // 5MB
                    continue;
                }

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $tmpName);
                finfo_close($finfo);

                $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($mime, $allowed)) {
                    continue;
                }

                $ext = pathinfo($origName, PATHINFO_EXTENSION);
                $safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
                $newName = time() . '_' . uniqid() . '_' . $safeBase . '.' . $ext;
                $destination = rtrim($this->uploadDir, '/') . '/' . $newName;

                if (@move_uploaded_file($tmpName, $destination)) {
                    $savedFiles[] = $this->publicPath . '/' . $newName;
                }
            }
        }

        // store notification with images as JSON (if any)
        $imagesJson = !empty($savedFiles) ? json_encode(array_values($savedFiles)) : null;

        $stmt = $this->pdo->prepare("INSERT INTO notifications (room_id, description, status, images) VALUES (?, ?, 'pending', ?)");
        $stmt->execute([$roomId, $fullDescription, $imagesJson]);

        return ['success' => true, 'message' => 'Issue reported successfully!'];
    }
}
