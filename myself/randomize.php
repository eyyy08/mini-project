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

$outfit = [];
$message = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  if($_POST['action'] === 'generate') {
    $requiredCategories = ['Top', 'Bottom', 'Shoes'];
    $optionalCategories = ['Outerwear', 'Accessory'];

    $statement=$db->prepare("
    SELECT ci.* FROM clothing_item2 ci
    JOIN category2 c ON ci.category2_id = c.id
    WHERE ci.user2_id = ? AND c.name = ?
    ORDER BY RAND()
    LIMIT 1
    ");

    $missingRequired = false;

    foreach (array_merge($requiredCategories, $optionalCategories) as $categoryName) {
      $statement->execute([$_SESSION['user']['id'], $categoryName]);
      $item = $statement->fetch();

      if($item){
        $outfit[$categoryName] = $item;
      } elseif (in_array($categoryName, $requiredCategories)) {
        $missingRequired = true;
      }
    }

    if($missingRequired){
      $message = 'You need at least one Top, Bottom and a pair of Shoes to generate an outfit.';
      $outfit = [];
    } else {
      $_SESSION['last_outfit'] = array_column($outfit, 'id');
    }
  }

  if ($_POST['action'] === 'save') {
    $itemids = $_SESSION['last_outfit'] ?? [];

    if (!empty($itemids)){
      $statement=$db->prepare("INSERT INTO outfit2 (user2_id, is_public) VALUES (?, 0)");
      $statement->execute([$_SESSION['user']['id']]);

      $outfitId=$db->lastInsertId();

      $linkstatement = $db->prepare("INSERT INTO outfit2_item2 (outfit2_id, item2_id) VALUES (?, ?)");
      foreach ($itemIds as $itemId) {
      $linkstatement->execute([$outfitId, $itemId]);
    }

    unset($_SESSION['last_outfit']);
    header('Location: myoutfit.php');
    exit;
    }
  }
}
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
        <h1>Randomize an outfit</h1>

        <form method="post">
          <input type="hidden" name="action" value="generate">
          <button type="submit" class="btn randomize1-btn">Randomize my outfit</button>
        </form>

        <?php if ($message): ?>
          <p style="color: red;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <?php if(!empty($outfit)):?>
        <div class="container">
          <?php foreach ($outfit as $categoryName =>$item):?>
          <div class="card">
            <h3><?= htmlspecialchars($categoryName) ?></h3>
                <h5><?= htmlspecialchars($item['name']) ?></h5>
                <p><?= htmlspecialchars($item['color']) ?>, <?= htmlspecialchars($item['pattern']) ?>, <?= htmlspecialchars($item['material']) ?></p>
          </div>
          <?php endforeach; ?>
        </div>

        <form method="post">
          <input type="hidden" name="action" value="save">
          <button type="submit" class="btn randomize2-btn">Save this outfit</button>
        </form>
        <?php endif; ?>
      </section>
    </main>

    <footer>
      <span class="foot">WearIT © 2026 All rights reserved.</span>
    </footer>
  </body>
</html>
