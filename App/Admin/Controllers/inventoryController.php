<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../Models/InventoryModel.php';

class InventoryController {
    private $model;

    public function __construct($pdo) {
        $this->model = new InventoryModel($pdo);
    }

    public function index() {
        // Handle restock button
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
            $this->model->restockItem($_POST['item_id']);
        }

        // Handle room booking increment
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_room_people'], $_POST['room_id'])) {
            $people = intval($_POST['book_room_people']); 
            $roomId = intval($_POST['room_id']);
            $this->model->bookRoom($roomId, $people);
        }

        $items = $this->model->getInventoryStatus();
        $summary = $this->model->getSummaryStock();

        // Separate items by category
        $bedsheets = array_filter($items, fn($i) => $i['location'] === 'BEDSHEETS');
        $toiletries = array_filter($items, fn($i) => $i['location'] === 'TOILETRIES');

        require __DIR__.'/../Views/inventory.php';
    }
}
?>
