<?php
session_start();

$db = new PDO("mysql:host=localhost;dbname=wearit_2", "root", "");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  $statement=$db->prepare("SELECT * FROM user2 WHERE email = ?");
  $statement->execute([$email]);
  $user = $statement->fetch();

  if ($user && password_verify($password, $user['password_hash'])){
    $_SESSION['user']=[
      'id' => $user['id'],
      'name' => $user['name'],
      'email' => $user['email'],
      'role' => $user['role'],
    ];

    if ($user['role'] === 'admin') {
      header('Location: admincategory.php');

    } elseif ($user['role'] === 'user') {
      header('Location: wardrobe.php');

    } else {
      header('Location: gallery.php');
    }
    exit;
  }

  $error = 'Invalid email or password.';
}

?>




<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Document</title>
    <link rel="stylesheet" href="style2.css" />
  </head>
  <body class="background">
    <header>
      <a class="logo" href="#">WearIT</a>
      <nav>
        <a href="login.php">Login</a><a href="register.php">Register</a>
      </nav>
    </header>

    <main>
      <section>
        <div class="card">
          <h1>Login</h1>

          <?php if ($error): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
          <?php endif; ?>

          <form method="post">
            <label for="email">Email</label>
            <input type="email" name="email" required /><br /><br />

            <label for="password">Password</label>
            <input type="password" name="password" required /><br /><br />

            <button class="btn" type="submit">Login</button>
          </form>
          <p class="switch">
            No account yet? <a href="register.php">Register</a>
          </p>
        </div>
      </section>
    </main>

    <footer>
      <span class="foot">WearIT © 2026 All right reserved.</span>
    </footer>
  </body>
</html>
