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

if($_SERVER['REQUEST_METHOD'] === "POST" && $_POST['action'] === 'toggle_public') {
  $outfitID = (int) $_POST['outfit2_id'];
  $statement = $db->prepare("UPDATE outfit2 SET is_public = NOT is_public WHERE id = ? AND user2_id = ?");
  $statement->execute([$outfitID, $_SESSION['user']['id']]);
  header('Location: myoutfit.php');
  exit;
}

$statement = $db->prepare("SELECT * FROM outfit2 WHERE user2_id = ? ORDER BY created_at DESC");
$statement->execute([$_SESSION['user']['id']]);
$outfits = $statement->fetchAll();

$itemsStatement = $db->prepare("SELECT ci.name, c.name AS category2_name
  FROM outfit_item2 oi
  JOIN clothing_item2 ci ON oi.clothing_item2_id = ci.id
  JOIN category2 c ON ci.category2_id = c.id
  WHERE oi.outfit2_id = ?");
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
        <a href="favorite.php">Favourites</a>
        <span class="badge"><?= htmlspecialchars($_SESSION['user']['name']) ?> · <?= htmlspecialchars($_SESSION['user']['role']) ?></span>
        <a href="logout.php">Log Out</a>
      </nav>
    </header>

    <main>
      <section id="favorite">
        <h1>My outfits</h1>

        <?php if (empty($outfits)):?>
        <p class="switch">
          You haven't saved any outfits yet. <br>
          <a href="randomize.php">Generate one</a>
        </p>
        <?php endif; ?>

        <div class="container">
          <?php foreach ($outfits as $outfit): ?>
            <?php $itemsStatement->execute([$outfit['id']]);
                  $outfit_items = $itemsStatement->fetchAll();
            ?>
            <div class="card">
                <ul>
                  <?php foreach ($outfit_items as $oi):?>
                    <li><?= htmlspecialchars($oi['category2_name']) ?>: <?= htmlspecialchars($oi['name']) ?></li>
                  <?php endforeach; ?>
                </ul>

                <form method="post" style="display:inline;">
                  <input type="hidden" name="action" value="toggle_public">
                  <input type="hidden" name="outfit2_id" value="<?= $outfit['id'] ?>">
                  <button type="submit" class="btn outfit-btn"><?= $outfit['is_public'] ? 'Make private' : 'Share publicly' ?></button>
                </form>

                <form method="post" action="outfitdelete.php" style="display:inline;">
                  <input type="hidden" name="id" value="<?= $outfit['id'] ?>">
                <button type="submit" class="btn outfit-btn" onclick="return confirm('Delete this outfit?');">Delete</button>
              </form>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    </main>

    <footer>
      <span class="foot">WearIT © 2026 All rights reserved.</span>
    </footer>
  </body>
</html>