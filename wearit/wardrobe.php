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

$statement = $db->prepare("SELECT ci.*, c.name AS category2_name
FROM clothing_item2 ci
JOIN category2 c ON ci.category2_id = c.id
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
    <title>WearIT</title>
    <link rel="stylesheet" href="style2.css" />
  </head>
  <body class="main-background2">
    <header>
      <a class="logo" href="#">WearIT</a>

      <nav>
        <a href="gallery.php">Gallery</a>
        <a href="wardrobe.php">My Wardrobe</a>
        <a href="randomize.php">Randomize</a>
        <a href="myoutfit.php">My Outfits</a>
        <a href="favorite.php">Favorites</a>
        <span class="badge">
        <span class="username"><?= htmlspecialchars($_SESSION['user']['name']) ?></span> 
        <span class="role"><?= htmlspecialchars($_SESSION['user']['role']) ?></span>
        </span>
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
            <img src="<?= htmlspecialchars($item['image_path'] ?? '') ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="img">
            <?php endif; ?>
            <h3><?= htmlspecialchars($item['name']) ?></h3>
            <p><?= htmlspecialchars($item['category2_name']) ?></p>
            <?php $details = array_filter([$item['color'], $item['material'], $item['pattern']]); ?>
            <p><?= htmlspecialchars(implode(', ', $details)) ?></p>

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
