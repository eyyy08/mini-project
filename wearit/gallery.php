<?php
session_start();
require __DIR__ . '/roles.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$db = new PDO("mysql:host=localhost;dbname=wearit_2", "root", "");

$outfits=$db->query("SELECT o.*, u.name AS owner_name
FROM outfit2 o
JOIN user2 u ON o.user2_id = u.id
WHERE o.is_public = 1
ORDER BY o.created_at DESC
")->fetchALL();

$itemsStatement=$db->prepare("SELECT ci.name, c.name AS category2_name
FROM outfit_item2 oi
JOIN clothing_item2 ci ON oi.clothing_item2_id = ci.id
JOIN category2 c ON ci.category2_id = c.id
WHERE oi.outfit2_id =?
");

$favoriteIDs = [];
if (isUser() || isGuest()){
  $favStatement=$db->prepare("SELECT outfit2_id FROM favorite2 WHERE user2_id = ?");
  $favStatement->execute([$_SESSION['user']['id']]);
  $favoriteIDs = array_column($favStatement->fetchALL(), 'outfit2_id');
}
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

        <?php if (isAdmin()): ?>
          <a href="admincategory.php">Categories</a>
          <a href="adminuser.php">Users</a>
        <?php endif; ?>

        <?php if (isUser()): ?>
          <a href="wardrobe.php">My Wardrobe</a>
          <a href="randomize.php">Randomize</a>
          <a href="myoutfit.php">My Outfits</a>
        <?php endif; ?>

        <?php if (isUser() || isGuest()): ?>
          <a href="favorite.php">Favourites</a>
        <?php endif; ?>

        <span class="badge2"><?= $_SESSION['user']['name'] ?> · <?= $_SESSION['user']['role'] ?></span>
        <a href="logout.php">Log Out</a>
      </nav>
    </header>

    <main>
      <section id="favorite">
        <h1>Public outfit gallery</h1>

        <?php if(empty($outfits)):?>
        <p>No public outfits yet.</p>
        <?php endif; ?>

        <div class="container">
          <?php foreach ($outfits as $outfit):?>
            <?php $itemsStatement->execute([$outfit['id']]);
                  $outfit_items=$itemsStatement->fetchAll();
                  $isFavorited=in_array($outfit['id'], $favoriteIDs);
            ?>
          <div class="card">
            <p><span class="light2"><?= $outfit['owner_name'] ?></span></p>
            <ul>
              <?php foreach ($outfit_items as $oi): ?>
                  <li><?= $oi['category2_name'] ?>: <?= $oi['name'] ?></li>
                <?php endforeach; ?>
            </ul>

            <?php if (isUser() || isGuest()): ?>
              <form method="post" action="favoritetoggle.php">
                <input type="hidden" name="outfit2_id" value="<?= $outfit['id'] ?>">
                <button type="submit" class="heart"><?= $isFavorited ? 'Favorited <i class="fa-solid fa-heart"></i>' : 'Favorite <i class="fa-regular fa-heart"></i>' ?></button>
              </form>
            <?php endif; ?>
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
