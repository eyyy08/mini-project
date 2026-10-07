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

    if ($action === 'add_subcategory') {
        $name = trim($_POST['name']);
        $categoryID = (int) $_POST['category2_id'];
        if ($name !== '' && $categoryID > 0){
            $statement=$db->prepare("INSERT INTO subcategory2 (category2_id, name) VALUES (?, ?)");
            $statement->execute([$categoryID, $name]);
        }
    }

    if ($action === 'delete_subcategory') {
        $statement=$db->prepare("DELETE FROM subcategory2 WHERE id = ?");
        $statement->execute([(int) $_POST['subcategory2_id']]);
    }

    header('Location: admincategory.php');
    exit;
}

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$categories = $db ->query("SELECT * FROM category2 ORDER BY name")->fetchALL();
$subcategories = $db ->query("SELECT s.*, c.name AS category2_name
  FROM subcategory2 s
  JOIN category2 c ON s.category2_id = c.id
  ORDER BY c.name, s.name
")->fetchAll();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style2.css" />
</head>
<body>
    <header>
      <a class="logo" href="#">WearIT</a>
      <nav>
        <a href="gallery.php">Gallery</a>
        <a href="admincategory.php">Categories</a>
        <a href="adminuser.php">Users</a>
        <span class="badge"><?= htmlspecialchars($_SESSION['user']['name']) ?> · <?= htmlspecialchars($_SESSION['user']['role']) ?></span>
        <a href="logout.php">Log Out</a>
      </nav>
    </header>

    <main>
      <section id="favorite">
        <h1>Manage Categories</h1>

        <?php if ($flash): ?>
          <p style="color: red;"><?= htmlspecialchars($flash) ?></p>
        <?php endif; ?>

        <h3>Categories</h3>
        <ul>
          <?php foreach ($categories as $category): ?>
            <li>
              <?= htmlspecialchars($category['name']) ?>
              <form method="post" style="display:inline;">
                <input type="hidden" name="action" value="delete_category">
                <input type="hidden" name="category2_id" value="<?= $category['id'] ?>">
                <button type="submit" onclick="return confirm('Delete this category?');">Delete</button>
              </form>
            </li>
          <?php endforeach; ?>
        </ul>

        <form method="post">
          <input type="hidden" name="action" value="add_category">
          <input type="text" name="name" placeholder="New category name" required>
          <button type="submit" class="btn outfit-btn">Add category</button>
        </form>

        <h3>Subcategories</h3>
        <ul>
          <?php foreach ($subcategories as $sub): ?>
            <li>
              <?= htmlspecialchars($sub['category2_name']) ?> / <?= htmlspecialchars($sub['name']) ?>
              <form method="post" style="display:inline;">
                <input type="hidden" name="action" value="delete_subcategory">
                <input type="hidden" name="subcategory2_id" value="<?= $sub['id'] ?>">
                <button type="submit" onclick="return confirm('Delete this subcategory?');">Delete</button>
              </form>
            </li>
          <?php endforeach; ?>
        </ul>

        <form method="post">
          <input type="hidden" name="action" value="add_subcategory">
          <select name="category2_id" class="select" required>
            <?php foreach ($categories as $category): ?>
              <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <input type="text" name="name" placeholder="New subcategory name" required>
          <button type="submit" class="btn outfit-btn">Add subcategory</button>
        </form>
      </section>
    </main>
    
    <footer>
      <span class="foot">WearIT © 2026 All rights reserved.</span>
    </footer>
</body>
</html>