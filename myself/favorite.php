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
        <a href="#">Gallery</a>
        <a href="#">My Wardrobe</a>
        <a href="#">Randomize</a>
        <a href="#">My Outfits</a>
        <a href="#">Favourites</a>
        <span class="badge">Martin · User</span>
        <a href="#">Log Out</a>
      </nav>
    </header>

    <main>
      <section id="favorite">
        <h1>My favorites</h1>
        <p class="switch">
          No favourites yet. Head to the <a href="#">gallery</a> and favourite
          some outfits.
        </p>

        <div class="container">
          <div class="card2">
            <ul>
              <li>Top: Name</li>
              <li>Bottom: Name</li>
              <li>Shoes: Name</li>
              <li>Outerwear: Name</li>
            </ul>

            <button class="btn">Remove favorite</button>
          </div>

          <div class="card2">
            <ul>
              <li>Top: Name</li>
              <li>Bottom: Name</li>
              <li>Shoes: Name</li>
              <li>Outerwear: Name</li>
            </ul>

            <button class="btn">Remove favorite</button>
          </div>

          <div class="card2">
            <ul>
              <li>Top: Name</li>
              <li>Bottom: Name</li>
              <li>Shoes: Name</li>
              <li>Outerwear: Name</li>
            </ul>

            <button class="btn">Remove favorite</button>
          </div>

          <div class="card2">
            <ul>
              <li>Top: Name</li>
              <li>Bottom: Name</li>
              <li>Shoes: Name</li>
              <li>Outerwear: Name</li>
            </ul>

            <button class="btn">Remove favorite</button>
          </div>
        </div>
      </section>
    </main>

    <footer>
      <span class="foot">WearIT © 2026 All rights reserved.</span>
    </footer>
  </body>
</html>
