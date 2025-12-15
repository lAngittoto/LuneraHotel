<?php
$title = "Create Room";
ob_start();
require_once 'header.php';
require_once __DIR__ . '/../../config/Helpers/amenityicon.php';

if (!isset($_SESSION['user'])) {
    header('Location: /LuneraHotel/App/Public');
    exit;
}
?>

<div class="p-10">
    <a href="/LuneraHotel/App/Public/managerooms">
        <h1 class="p-2 w-[200px] bg-white shadow-2xl border rounded-2xl hover:bg-amber-600 hover:text-white border-[#b4adad] hover:scale-105">
            <i class="fa-solid fa-arrow-left"></i> Back to All Rooms
        </h1>
    </a>

    <div class="w-full flex flex-col justify-center items-center p-10">

        <!-- SUCCESS MESSAGE -->
        <?php if (!empty($successMessage)): ?>
            <p class="text-green-600 text-2xl p-5">
                <?= htmlspecialchars($successMessage) ?>
            </p>
        <?php endif; ?>

        <!-- ERROR MESSAGES (MULTIPLE) -->
        <?php if (!empty($errors)): ?>
            <div class="text-red-600 text-xl p-5 space-y-2 text-center">
                <?php foreach ($errors as $error): ?>
                    <p>• <?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php elseif (!empty($errorMessage)): ?>
            <p class="text-red-600 text-2xl p-5 text-center">
                <?= htmlspecialchars($errorMessage) ?>
            </p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data"
              class="w-full md:w-[60vw] bg-white shadow-2xl border border-[#dddddd] p-6 md:p-10 rounded-2xl flex flex-col gap-6">

            <div>
                <h1 class="text-3xl font-semibold">Create Room</h1>
                <p class="text-xl">Add a new room to the system</p>
            </div>

            <!-- Room Number + Room Name -->
            <div class="flex flex-col md:flex-row gap-4 w-full">
                <div class="flex flex-col w-full">
                    <label class="font-semibold text-xl">Room Number</label>
                    <input
                        type="text"
                        name="room_number"
                        value="<?= htmlspecialchars($_POST['room_number'] ?? '') ?>"
                        class="outline-0 bg-[#ebebeb] p-3 rounded-xl border border-[#dcdcdc]"
                    >
                </div>

                <div class="flex flex-col w-full">
                    <label class="font-semibold text-xl">Room Name</label>
                    <input
                        type="text"
                        name="room_type"
                        value="<?= htmlspecialchars($_POST['room_type'] ?? '') ?>"
                        class="outline-0 bg-[#ebebeb] p-3 rounded-xl border border-[#dcdcdc]"
                    >
                </div>
            </div>

            <!-- Room Type -->
            <div class="flex flex-col w-full">
                <label class="font-semibold text-xl">Room Type</label>
                <select name="type_name"
                        class="border border-[#dcdcdc] p-3 rounded-xl bg-[#ebebeb] w-full md:w-[250px]">
                    <?php foreach ($roomTypes as $type): ?>
                        <option value="<?= htmlspecialchars($type['type_name']) ?>"
                            <?= ($_POST['type_name'] ?? '') === $type['type_name'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type['type_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Description -->
            <div class="flex flex-col w-full">
                <label class="font-semibold text-xl">Description</label>
                <input
                    type="text"
                    name="description"
                    value="<?= htmlspecialchars($_POST['description'] ?? '') ?>"
                    class="outline-0 bg-[#ebebeb] p-3 rounded-xl border border-[#dcdcdc]"
                >
            </div>

            <!-- Image -->
            <div class="flex flex-col w-full">
                <label class="font-semibold text-xl">Image</label>
                <input
                    type="file"
                    name="img"
                    class="outline-0 bg-[#ebebeb] p-3 rounded-xl w-full md:w-[250px] border border-[#dcdcdc]"
                >
            </div>

            <!-- Status -->
            <div class="flex flex-col md:flex-row gap-6 items-start md:items-center text-xl">
                <label class="flex items-center gap-3 font-semibold">
                    <i class="fa-solid fa-screwdriver-wrench text-[#800000]"></i>
                    Under Maintenance
                    <input type="radio" name="status" value="Under Maintenance"
                        <?= ($_POST['status'] ?? 'Under Maintenance') === 'Under Maintenance' ? 'checked' : '' ?>
                        class="w-6 h-6">
                </label>

                <label class="flex items-center gap-3 font-semibold">
                    Available
                    <input type="radio" name="status" value="Available"
                        <?= ($_POST['status'] ?? '') === 'Available' ? 'checked' : '' ?>
                        class="w-6 h-6">
                </label>

                <label class="flex items-center gap-3 font-semibold">
                    <i class="fa-solid fa-spinner text-indigo-600"></i>
                    In Progress
                    <input type="radio" name="status" value="In Progress"
                        <?= ($_POST['status'] ?? '') === 'In Progress' ? 'checked' : '' ?>
                        class="w-6 h-6">
                </label>
            </div>

            <!-- Floor + Capacity -->
            <div class="flex flex-col md:flex-row gap-4 w-full">
                <div class="flex flex-col w-full">
                    <label class="font-semibold text-xl">Floor</label>
                    <input
                        type="number"
                        name="floor"
                        value="<?= htmlspecialchars($_POST['floor'] ?? '') ?>"
                        min="1"
                        max="4"
                        class="border border-[#dcdcdc] p-3 rounded-xl bg-[#ebebeb] outline-0"
                    >
                </div>

                <div class="flex flex-col w-full">
                    <label class="font-semibold text-xl">Capacity</label>
                    <input
                        type="number"
                        name="people"
                        value="<?= htmlspecialchars($_POST['people'] ?? '') ?>"
                        min="1"
                        max="6"
                        class="border border-[#dcdcdc] p-3 rounded-xl bg-[#ebebeb] outline-0"
                    >
                </div>
            </div>

            <!-- Amenities -->
            <label class="font-semibold text-xl">Amenities</label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <?php foreach ($allAmenities as $amenity): ?>
                    <label class="flex items-center gap-3">
                        <input
                            type="checkbox"
                            name="amenities[]"
                            value="<?= htmlspecialchars($amenity) ?>"
                            <?= in_array($amenity, $_POST['amenities'] ?? []) ? 'checked' : '' ?>
                            class="w-6 h-6"
                        >
                        <i class="<?= htmlspecialchars(getAmenityIcon($amenity)) ?> text-[#800000]"></i>
                        <?= htmlspecialchars($amenity) ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <div>
                <button type="submit" name="create_room"
                        class="px-6 py-3 bg-[#800000] text-white rounded-2xl text-xl w-full md:w-[200px] cursor-pointer">
                    Create Room
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../../App/layout.php';
?>
