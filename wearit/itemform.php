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

$itemid = isset($_GET['id']) ? (int) $_GET['id'] :null;
$item = [
    'name' => '', 
    'category2_id' => '', 
    'color' => '', 
    'material' => '', 
    'pattern' => '', 
    'image_path' => ''];
$errors =[];

if ($itemid){
    $statement = $db->prepare("SELECT * FROM clothing_item2 WHERE id = ? AND user2_id = ?");
    $statement->execute([$itemid, $_SESSION['user']['id']]);
    $existing = $statement->fetch();

    if (!$existing){
        header('Location: wardrobe.php');
        exit;
    }
    $item = $existing;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item['name'] = trim($_POST['name']);
    $item['category2_id'] = (int)($_POST['category2_id']);
    $item['color'] = trim($_POST['color']);
    $item['material'] = trim($_POST['material']);
    $item['pattern'] = trim($_POST['pattern']);
    $item['image_path'] = trim($_POST['image_path']);

    if($item['name'] === '' || $item['category2_id'] === 0){
        $errors[] = 'Name and category are required.';
    }

    if (empty($errors)) {
        if($itemid){
            $statement = $db->prepare("UPDATE clothing_item2 SET name = ?, category2_id = ?, color = ?, material = ?, pattern = ?, image_path = ? WHERE id = ? AND user2_id = ? ");

            $statement->execute([
                $item['name'],
                $item['category2_id'],
                $item['color'],
                $item['material'],
                $item['pattern'],
                $item['image_path'],
                $itemid,
                $_SESSION['user']['id'],
            ]);
        } else {
            $statement = $db->prepare("INSERT INTO clothing_item2 (user2_id, category2_id, name, color, material, pattern, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");

            $statement->execute([$_SESSION['user']['id'], $item['category2_id'], $item['name'], $item['color'], $item['material'], $item['pattern'], $item['image_path']]);
        }

        header('Location: wardrobe.php');
        exit;

    }
}

$categories=$db->query("SELECT * FROM category2 ORDER BY name")->fetchALL();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" />
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
        <span class="badge2"><?= $_SESSION['user']['name'] ?> · <?= $_SESSION['user']['role'] ?></span>
        <a href="logout.php">Log Out</a>
      </nav>
    </header>

    <main>
        <section>
            <div class="card">
                <h1><?=$itemid ? 'Edit item' : 'Add a clothing item' ?></h1>

                <?php foreach ($errors as $error): ?>
                    <p style="color: red;"><?= $error ?></p>
                <?php endforeach; ?>

                <form method="post">
                    <label>Name</label>
                    <input type="text" name="name" value="<?= $item['name'] ?>" required> <br><br>

                    <label>Category</label>
                    <select class="select" name="category2_id" id="category2_id" required>
                        <option value="">Select a category</option>
                        <?php foreach ($categories as $category):?>
                            <option value="<?= $category['id'] ?>" <?=$item['category2_id'] == $category['id'] ? 'selected' : ''?>> <?= $category['name'] ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Color</label>
                    <input type="text" name="color" value="<?= $item['color'] ?>"> <br><br>

                    <label>Material</label>
                    <input type="text" name="material" value="<?= $item['material'] ?>"> <br><br>

                    <label>Pattern</label>
                    <input type="text" name="pattern" value="<?= $item['pattern'] ?>"> <br><br>

                    <label>Image URL</label>
                    <input type="text" name="image_path" value="<?= $item['image_path'] ?>" placeholder="https://..."> <br><br>

                    <button class="btn" type="submit"><?= $itemid ? 'Save changes' : 'Add item' ?></button>
                </form>
            </div>
        </section>
    </main>

    <footer>
      <span class="foot">WearIT © 2026 All rights reserved.</span>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</body>
</html>