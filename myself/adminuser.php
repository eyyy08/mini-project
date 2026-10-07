<?php
session_start();
require __DIR__ . '/roles.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}
if (!isAdmin()) {
    header('Location: noaccess.php');
    exit;
}

$db = new PDO("mysql:host=localhost;dbname=wearit_2", "root", "");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $userId = (int) $_POST['user2_id'];

    if ($action === 'change_role') {
        $role = $_POST['role'];
        if (in_array($role, ['admin', 'user', 'guest'])) {
            $statement = $db->prepare("UPDATE user2 SET role = ? WHERE id = ?");
            $statement->execute([$role, $userId]);
        }
    }

    if ($action === 'delete_user') {
        if ($userId !== (int) $_SESSION['user']['id']) {
            $statement = $db->prepare("DELETE FROM user2 WHERE id = ?");
            $statement->execute([$userId]);
        }
    }

    header('Location: adminuser.php');
    exit;
}

$users = $db->query("SELECT id, name, email, role FROM user2 ORDER BY created_at DESC")->fetchAll();
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
        <a href="admincategory.php">Categories</a>
        <a href="adminuser.php">Users</a>
        <span class="badge"><?= htmlspecialchars($_SESSION['user']['name']) ?> · <?= htmlspecialchars($_SESSION['user']['role']) ?></span>
        <a href="logout.php">Log Out</a>
      </nav>
    </header>

    <main>
      <section id="favorite">
        <h1>Manage Users</h1>

        <table border="1" cellpadding="8" style="width:100%; background:#fff; border-collapse: collapse;">
          <thead>
            <tr><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
              <td><?= htmlspecialchars($u['name']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td><?= htmlspecialchars($u['role']) ?></td>
              <td>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="action" value="change_role">
                  <input type="hidden" name="user2_id" value="<?= $u['id'] ?>">
                  <select name="role">
                    <option value="admin"  <?= $u['role'] === 'admin'  ? 'selected' : '' ?>>admin</option>
                    <option value="user" <?= $u['role'] === 'user' ? 'selected' : '' ?>>user</option>
                    <option value="guest"  <?= $u['role'] === 'guest'  ? 'selected' : '' ?>>guest</option>
                  </select>
                  <button type="submit">Update</button>
                </form>

                <?php if ((int)$u['id'] !== (int) $_SESSION['user']['id']): ?>
                  <form method="post" style="display:inline;">
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="user2_id" value="<?= $u['id'] ?>">
                    <button type="submit" onclick="return confirm('Delete this user?');">Delete</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>
    </main>

    <footer>
      <span class="foot">WearIT © 2026 All rights reserved.</span>
    </footer>
  </body>
</html>