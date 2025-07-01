<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
.sidebar {
    width: 200px;
    background-color: #333;
    color: white;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    padding-top: 60px;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    z-index: 10;
}

.sidebar a {
    display: block;
    padding: 15px 20px;
    color: white;
    text-decoration: none;
    transition: background-color 0.2s;
}

.sidebar a:hover,
.sidebar a.active {
    background-color: #444;
}
</style>

</head>
<body>
    
<div class="sidebar">
    <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a>

    <?php if ($role === 'editor' || $role === 'admin'): ?>
        <a href="editor.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'editor.php' ? 'active' : ''; ?>">Posts</a>
    <?php endif; ?>

    <?php if ($role === 'admin'): ?>
        <a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>">Users</a>
    <?php endif; ?>
</div>
</body>
</html>
