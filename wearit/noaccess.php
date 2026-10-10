<?php
session_start();
require __DIR__ . '/roles.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>No Access</title>
    <link rel="stylesheet" href="style2.css" />
</head>
<body class="main-background2">
    <main>
        <section>
            <div class="card">
                <h1>Access Denied</h1>
                <p>You are not allowed to view this page.</p>
                <p class="switch"><a href="login.php">Back to login</a></p>
            </div>
        </section>
    </main>
</body>
</html>