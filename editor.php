<?php
session_start();
include "config.php";

if (!isset($_SESSION['id']) && isset($_COOKIE['remember'])) {
    $token = $_COOKIE['remember'];
    $sql = "SELECT id FROM users WHERE remember_token=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['id'] = $row['id'];
    }
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userid = $_SESSION['id'];
$role = $_SESSION['role'] ?? 'viewer';
$profileImage = (isset($_SESSION['profile_img']) && file_exists($_SESSION['profile_img']))
    ? $_SESSION['profile_img']
    : '../uploads/default-avatar.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blog Post Project</title>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        /* Sidebar */
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
        }

        .sidebar a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #444;
        }

     header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #f4f4f4;
    padding: 10px 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    position: fixed;
    top: 0;
    left: 200px;                   /* Start right after sidebar */
    width: calc(97% - 200px);    /* Full width minus sidebar */
    z-index: 1000;
}

.main-content {
    margin-top:40px;
    margin-left: 200px;   /* Sidebar width */
    padding: 80px 30px 40px; /* Top padding equals header height + some space */
}

        .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            min-width: 120px;
            z-index: 1;
            border-radius: 5px;
            overflow: hidden;
        }

        .dropdown-content a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: black;
            border-bottom: 1px solid #ddd;
        }

        .dropdown-content a:hover {
            background-color: #f0f0f0;
        }

        .profile-dropdown:hover .dropdown-content {
            display: block;
        }

        .create-button {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .create-button:hover {
            background-color: #0056b3;
        }

        .blog-container {
            display: flex;
            flex-direction: column;
            gap: 30px;
            margin-top: 20px;
        }

        .blog-post {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        .blog-post h2 {
            margin-top: 0;
            color: #333;
        }

        .blog-post .meta {
            font-size: 14px;
            color: #777;
            margin-bottom: 15px;
        }

        .blog-post p {
            font-size: 16px;
            color: #444;
            line-height: 1.6;
        }

        .update-button {
            margin-top: 15px;
            background-color: #28a745;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .update-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main-content">
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h1>
        <div class="profile-dropdown">
            <img src="<?php echo $profileImage; ?>" alt="Profile" class="profile-img">
            <div class="dropdown-content">
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>

    <h1>Blog Posts</h1>

    <div class="top-bar">
        <form action="createblog.php" method="POST" style="display: inline;">
            <button type="submit" class="create-button">+ Create New Post</button>
        </form>
    </div>

    <div class="blog-container">
        <?php
        $sql = "SELECT * FROM posts WHERE user_id = '$userid' ORDER BY created_at DESC";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "
                <div class='blog-post'>
                    <h2>" . htmlspecialchars($row['title']) . "</h2>
                    <p class='meta'>Posted on " . date('F j, Y', strtotime($row['created_at'])) . "</p>
                    <p>" . nl2br(htmlspecialchars(substr($row['content'], 0, 100))) . "...</p>
                    <form action='Editorupdate.php' method='get'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <button type='submit' class='update-button'>Update</button>
                    </form>
                </div>";
            }
        } else {
            echo "<p>No blog posts found. Click 'Create New Post' to add one.</p>";
        }
        ?>
    </div>
</div>

</body>
</html>
