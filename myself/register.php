<?php
$db = new PDO("mysql:host=localhost;dbname=wearit_2", "root", "");

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $role = $_POST['role'] === 'guest' ? 'guest' : 'user';

  if ($name === '' || $email === '' || $password === '') {
    $errors[] = 'All fields are required.';
  }

  if (empty($errors)){
    $check = $db->prepare("SELECT id FROM user2 WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
      $errors[] = 'This account already exists.';
    }
  }

  if (empty($errors)){
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $statement=$db->prepare("INSERT INTO user2 (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    $statement->execute([$name, $email, $hash, $role]);

    header('Location: login.php');
    exit;
  }
}

?>



<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Document</title>
    <link rel="stylesheet" href="style2.css" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" />
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
          <h1>Create an account</h1>

          <?php foreach ($errors as $error): ?>
            <p style="color: red;"><?= htmlspecialchars($error)?></p>
          <?php endforeach; ?>

          <form method="post">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required /><br /><br />

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($name) ?>" required /><br /><br />

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required />

            <p style="margin: 1rem 0 0.5rem 0">I want to</p>

            <select class="select" name="role">
              <option value="user">Build my own wardrobe</option>
              <option value="guest">Browse others' outfit</option>
            </select>

            <button class="btn" type="submit">Create account</button>
          </form>
          <p class="switch">
            Already have an account? <a href="login.html">Login</a>
          </p>
        </div>
      </section>
    </main>

    <footer>
      <span class="foot">WearIT © 2026 All right reserved.</span>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  </body>
</html>
