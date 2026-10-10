<?php
session_start();
require __DIR__ . '/roles.php';

if (!isLoggedIn() || (!isUser() && !isGuest())) {
    header('Location: login.php');
    exit;
}

$db = new PDO("mysql:host=localhost;dbname=wearit_2", "root", "");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $outfitID = (int) $_POST['outfit2_id'];
    $userID = $_SESSION['user']['id'];

    $statement = $db->prepare("SELECT id FROM favorite2 WHERE user2_id = ? AND outfit2_id = ?");
    $statement->execute([$userID, $outfitID]);

    if ($statement->fetch()) {
        $delete = $db->prepare("DELETE FROM favorite2 WHERE user2_id = ? AND outfit2_id = ?");
        $delete->execute([$userID, $outfitID]);

    } else {
        $check = $db->prepare("SELECT id FROM outfit2 WHERE id = ? AND is_public = 1");
        $check->execute([$outfitID]);

        if ($check->fetch()) {
            $insert = $db->prepare("INSERT INTO favorite2 (user2_id, outfit2_id) VALUES (?, ?)");
            $insert->execute([$userID, $outfitID]);
        }
    }
}

$back = $_SERVER['HTTP_REFERER'] ?? 'gallery.php';
header('Location: ' . $back);
exit;
?>