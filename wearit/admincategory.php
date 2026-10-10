<?php
session_start();
require __DIR__ . '/roles.php';

if(!isLoggedIn()){
    header('Location: login.php');
    exit;
}

if(!isAdmin()){
    header('Location: noaccess.php');
    exit;
}

$db = new PDO("mysql:host=localhost;dbname=wearit_2", "root", "");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $action = $_POST['action'];

    if($action === 'add_category') {
        $name = trim($_POST['name']);
        if ($name !== ''){
            $statement=$db->prepare("INSERT INTO category2 (name) VALUES (?)");
            $statement->execute([$name]);
        }
    }

    if ($action === 'delete_category'){
      try {
          $statement = $db->prepare("DELETE FROM category2 WHERE id = ?");
          $statement->execute([(int) $_POST['category2_id']]);
      } catch (PDOException $e) {
        $_SESSION['flash'] = 'This category cannot be deleted.';
      }  
    }

    header('Location: admincategory.php');
    exit;
}

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$categories = $db ->query("SELECT * FROM category2 ORDER BY name")->fetchALL();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style2.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css" />

    
</head>
<body>
    <header>
      <a class="logo" href="#">WearIT</a>
      <nav>
        <a href="gallery.php">Gallery</a>
        <a href="admincategory.php">Categories</a>
        <a href="adminuser.php">Users</a>
        <span class="badge2"><?= $_SESSION['user']['name'] ?> · <?= $_SESSION['user']['role'] ?></span>
        <a href="logout.php">Log Out</a>
      </nav>
    </header>

    <main>
      <section id="favorite">
        <h1>Manage Categories</h1>

        <?php if ($flash): ?>
          <p style="color: red;"><?= $flash ?></p>
        <?php endif; ?>

        <h2 class="category">Categories</h2>
        <ul class="list">
          <?php foreach ($categories as $category): ?>
            <li>
              <?= $category['name'] ?>
              <form method="post" style="display:inline;">
                <input type="hidden" name="action" value="delete_category">
                <input type="hidden" name="category2_id" value="<?= $category['id'] ?>">
                <button type="submit" class="list-btn" onclick="return confirm('Delete this category?');"><i class="fa-solid fa-trash-can"></i></button>
              </form>
            </li>
          <?php endforeach; ?>
        </ul>

        <form method="post">
          <input type="hidden" name="action" value="add_category">
          <input type="text" name="name" placeholder="New category name" required>
          <button type="submit" class="btn outfit-btn">Add category</button>
        </form>
      </section>
    </main>
    
    <footer>
      <span class="foot">WearIT © 2026 All rights reserved.</span>
    </footer>

</body>
</html>