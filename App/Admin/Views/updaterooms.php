<?php
ob_start();
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../../config/Helpers/amenityicon.php';
?>

<div>
    <a href="/LuneraHotel/App/Public/managerooms">
        <h1 class="p-5 flex">Back to All Rooms</h1>
    </a>
    <div class="w-full flex flex-col justify-center items-center p-10">

        <?php if (!$room): ?>
            <p>Room information is not available.</p>
        <?php else: ?>
            <?php if (isset($successMessage)) echo "<p class='text-green-600'>$successMessage</p>"; ?>

            <form method="POST" enctype="multipart/form-data" class="w-[50vw] max-h-min bg-white shadow-2xl border border-[#dddddd] p-10 rounded-2xl flex flex-col gap-5">

                <h1>Edit Room Details</h1>
                <p>Update the information for <?= htmlspecialchars($room['room_type']) ?></p>

                <!-- Room Number -->
                 <div>
                <label>Room Number</label>
                <input type="text" name="room_number" value="<?= htmlspecialchars($room['room_number']) ?>">

                <!-- Room Name (added back) -->
                <label>Room Name</label>
                <input type="text" name="room_name" value="<?= htmlspecialchars($room['room_type']) ?>">
                 </div>
                <!-- Room Type -->


                <label>Room Type</label>
                <select name="room_type" class="border border-[#dcdcdc] p-2 md:w-[200px] w-[150px]">
                    <?php foreach ($roomTypes as $type): ?>
                        <option value="<?= htmlspecialchars($type['type_name']) ?>"
                            <?= strpos($room['room_type'], $type['type_name']) !== false ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type['type_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Description -->
                <label>Description</label>
                <input type="text" name="description" value="<?= htmlspecialchars($room['description']) ?>">

                <!-- Image -->
                <label>Image</label>
                <input type="file" name="img">

                <!-- Status -->
                <div class="flex gap-5 items-center">
                    <label>Set as Under Maintenance</label>
                    <input type="checkbox" name="status_maintenance" <?= $room['status'] === 'Maintenance' ? 'checked' : '' ?>>

                    <label>Available for Booking</label>
                    <input type="checkbox" name="status_available" <?= $room['status'] === 'Available' ? 'checked' : '' ?>>
                </div>

                <!-- Floor -->
                 <div>
                <label>Floor</label>
                <input type="text" name="floor" value="<?= htmlspecialchars($room['floor']) ?>">

                <!-- Capacity -->
                <label>Capacity</label>
                <input type="number" name="people" value="<?= htmlspecialchars($room['people']) ?>">
                    </div>
                <!-- Amenities Grid -->
                <label>Amenities</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    <?php foreach ($allAmenities as $amenity): ?>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="amenities[]" value="<?= htmlspecialchars($amenity) ?>"
                                <?= in_array($amenity, $roomAmenities) ? 'checked' : '' ?>>
                            <i class="<?= htmlspecialchars(getAmenityIcon($amenity)) ?> text-[#800000]"></i>
                            <?= htmlspecialchars($amenity) ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div>
                    <button type="submit" name="update_room">Save changes</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../../App/layout.php';
?>