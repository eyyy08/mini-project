<?php
session_start();
require __DIR__ . '/roles.php';

if (!isLoggedIn() || !isUser()) {
    header('Location: login.php');
    exit;
}

$db = new PDO("mysql:host=localhost;dbname=wearit_2", "root", "");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $statement = $db->prepare("DELETE FROM outfit2 WHERE id = ? AND user2_id = ?");
    $statement->execute([(int) $_POST['id'], $_SESSION['user']['id']]);
}

header('Location: myoutfit.php');
exit;
?>