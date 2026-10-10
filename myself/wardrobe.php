<?php
session_start();
require __DIR__ . '/roles.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if (!isUser()) {
    header('Location: noaccess.php');
    exit;
}

$db = new PDO("mysql:host=localhost;dbname=wearit_2", "root", "");

$statement = $db->prepare("SELECT ci.*, c.name AS category2_name, s.name AS subcategory2_name
FROM clothing_item2 ci
JOIN category2 c ON ci.category2_id = c.id
LEFT JOIN subcategory2 s ON ci.subcategory2_id = s.id
WHERE ci.user2_id = ?
ORDER BY ci.created_at DESC
");

$statement->execute([$_SESSION['user']['id']]);
$items = $statement->fetchALL();

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="style2.css" />
  </head>
  <body>
    <header>
      <a class="logo" href="#">WearIT</a>

      <nav>
        <a href="gallery.php">Gallery</a>
        <a href="wardrobe.php">My Wardrobe</a>
        <a href="randomize.php">Randomize</a>
        <a href="myoutfit.php">My Outfits</a>
        <a href="favorite.php">Favorites</a>
        <span class="badge2"><?= $_SESSION['user']['name'] ?> · <?= $_SESSION['user']['role'] ?></span>
        <a href="logout.php">Log Out</a>
      </nav>
    </header>

    <main>
      <section id="favorite">
        <h1>My Wardrobe</h1>
        <a class="btn outfit-btn" href="itemform.php">+ Add clothing item</a>

        <?php if(empty($items)): ?>
        <div class="wardrobe-des">
          <p>Your wardrobe is empty. Add your first item to get started.</p>
        </div>
        <?php endif;?>

        <div class="container">
          <?php foreach ($items as $item): ?>
          <div class="card">
            <?php if (!empty($item['image_path'])): ?>
            <img src="<?= $item['image_path'] ?>" alt="<?= $item['name'] ?>" class="item-img">
            <?php endif; ?>
            <h3><?= $item['name'] ?></h3>
            <p><?= $item['category2_name'] ?><?= $item['subcategory2_name'] ? '/' . $item['subcategory2_name'] : '' ?></p>
            <p><?= $item['color'] ?? '' ?><?= $item['material'] ? ', ' . $item['material'] : '' ?><?= $item['pattern'] ? ', ' . $item['pattern'] : '' ?></p>

            <div class="wardrobe-btn">
              <a class="btn outfit-btn" href="itemform.php?id=<?= $item['id'] ?>">Edit</a>
              <form method="post" action="itemdelete.php" style="display:inline;">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                <button type="submit" class="btn outfit-btn" onclick="return confirm('Delete this item?');">Delete</button>
              </form>
            </div>
          </div>
          <?php endforeach;?>
        </div>
      </section>
    </main>

    <footer>
      <span class="foot">WearIT © 2026 All rights reserved.</span>
    </footer>
  </body>
</html>
