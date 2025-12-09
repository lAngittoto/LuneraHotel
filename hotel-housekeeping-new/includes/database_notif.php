<?php
$connNotif = new mysqli("localhost", "root", "P@ssw0rd", "roomslunera_hotel");

if ($connNotif->connect_error) {
    die("Notification DB Connection Failed: " . $connNotif->connect_error);
}
