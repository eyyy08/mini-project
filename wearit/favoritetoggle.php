<?php
session_start();
require __DIR__ . '/roles.php';

if (!isLoggedIn() || (!isUser() && !isGuest())) {
    header('Location: login.php');
    exit;
}

$db = new PDO("mysql:host=localhost;dbname=wearit_2", "root", "");

// favorite / remove favorite
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $outfitID = (int) $_POST['outfit2_id'];
    $userID = $_SESSION['user']['id'];

    $statement = $db->prepare("SELECT id FROM favorite2 WHERE user2_id = ? AND outfit2_id = ?");
    $statement->execute([$userID, $outfitID]);

    // remove favorite (delete)
    if ($statement->fetch()) {
        $delete = $db->prepare("DELETE FROM favorite2 WHERE user2_id = ? AND outfit2_id = ?");
        $delete->execute([$userID, $outfitID]);

    // add to favorite
    } else {
        $check = $db->prepare("SELECT id FROM outfit2 WHERE id = ? AND is_public = 1");
        $check->execute([$outfitID]);

        if ($check->fetch()) {
            $insert = $db->prepare("INSERT INTO favorite2 (user2_id, outfit2_id) VALUES (?, ?)");
            $insert->execute([$userID, $outfitID]);
        }
    }
}

// two pages need gallery.php - button in gallery.php/remove button in favorite.php
$back = $_SERVER['HTTP_REFERER'] ?? 'gallery.php';
header('Location: ' . $back);
exit;
?>