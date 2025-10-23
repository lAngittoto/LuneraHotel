<?php
function getUser($pdo, $email) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
