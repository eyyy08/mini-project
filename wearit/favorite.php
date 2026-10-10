<?php
session_start();
require __DIR__ . '/roles.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if (!isUser() && !isGuest()) {
    header('Location: noaccess.php');
    exit;
}

$db = new PDO("mysql:host=localhost;dbname=wearit_2", "root", "");

$statement = $db->prepare("SELECT o.*, u.name AS owner_name
  FROM favorite2 f
  JOIN outfit2 o ON f.outfit2_id = o.id
  JOIN user2 u ON o.user2_id = u.id
  WHERE f.user2_id = ? AND o.is_public = 1
");
$statement->execute([$_SESSION['user']['id']]);
$outfits = $statement->fetchAll();

$itemsStatement = $db->prepare("SELECT ci.name, c.name AS category2_name
  FROM outfit_item2 oi
  JOIN clothing_item2 ci ON oi.clothing_item2_id = ci.id
  JOIN category2 c ON ci.category2_id = c.id
  WHERE oi.outfit2_id = ?
");
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="style2.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css" />
  </head>
  <body class="main-background2">
    <header>
      <a class="logo" href="#">WearIT</a>

      <nav>
        <a href="gallery.php">Gallery</a>

        <?php if (isUser()): ?>
          <a href="wardrobe.php">My Wardrobe</a>
          <a href="randomize.php">Randomize</a>
          <a href="myoutfit.php">My Outfits</a>
        <?php endif; ?>

        <a href="favorite.php">Favourites</a>
        <span class="badge2"><?= $_SESSION['user']['name'] ?> · <?= $_SESSION['user']['role'] ?></span>
        <a href="logout.php">Log Out</a>
      </nav>
    </header>

    <main>
      <section id="favorite">
        <h1>My favorites</h1>

        <?php if (empty($outfits)): ?>
          <p class="switch">
            No favourites yet. Head to the <a href="gallery.php">gallery</a> and favourite some outfits.
          </p>
        <?php endif; ?>

        <div class="container">
          <?php foreach ($outfits as $outfit): ?>
            <?php $itemsStatement->execute([$outfit['id']]);
                  $outfit_items = $itemsStatement->fetchAll();
            ?>
          <div class="card2">
            <p><span class="light2">Shared by <?= $outfit['owner_name'] ?></span></p>
            <ul>
              <?php foreach ($outfit_items as $oi): ?>
                  <li><?= $oi['category2_name'] ?>: <?= $oi['name'] ?></li>
                <?php endforeach; ?>
            </ul>

            <form method="post" action="favoritetoggle.php">
              <input type="hidden" name="outfit2_id" value="<?= $outfit['id'] ?>">
              <button type="submit" class="btn">Remove favorite <i class="fa-solid fa-heart"></i></button>
            </form>
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
