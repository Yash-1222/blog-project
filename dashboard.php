<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Dashboard</title>
<script src="https://code.jquery.com/jquery-3.7.1.js" crossorigin="anonymous"></script>
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

    /* Main content shifted right */
    .main-content {
        margin-top:70px;
        margin-left: 200px;
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
    left: 200px; /* shift to right of sidebar */
    width: calc(100% - 200px); /* adjust width */
    z-index: 1000;
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
    margin-right: 100px; /* reduced margin */
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

    .dropdown-content a:last-child {
        border-bottom: none;
    }

    .dropdown-content a:hover {
        background-color: #f0f0f0;
    }

    .profile-dropdown:hover .dropdown-content {
        display: block;
    }

    h1 {
        margin: 0;
    }

    /* Blog posts container */
    .blog-container {
        display: flex;
        flex-direction: column;
        gap: 30px;
        padding: 40px 20px;
    }

    /* Individual blog post */
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

    /* Link styling for blog titles */
    .blog-title-link {
        text-decoration: none;
        color: #007bff;
        transition: color 0.2s;
    }

    .blog-title-link:hover {
        text-decoration: underline;
        color: #0056b3;
    }
</style>
</head>
<body>

<?php
session_start();
include "config.php";

if (!isset($_SESSION['id']) && isset($_COOKIE['remember'])) {
    $token = $_COOKIE['remember'];
    $sql = "SELECT id FROM users WHERE remember_token=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_bind_stmt_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($rows = mysqli_fetch_assoc($result)) {
        $_SESSION['id'] = $rows['id'];
    }
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$defaultImage = 'uploads/default.png';
$storedImage = $_SESSION['profile_img'] ?? $defaultImage;
$serverFilePath = __DIR__ . '/' . $storedImage;

$profileImage = file_exists($serverFilePath) ? $storedImage : $defaultImage;

$role = $_SESSION['role'] ?? 'viewer';
?>

<?php include 'sidebar.php'; ?>

<div class="main-content">
<header>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h1>
    <div style="flex: 1; display: flex; justify-content: center; gap: 10px; align-items: center;">
        <input type="text" name="search_item" id="search_item" placeholder="Search..." style="padding: 6px 10px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px; width: 40%;" />
        <select id="filter_option" style="padding: 6px 10px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px;">
            <option value="recent" selected>Most Recent</option>
            <option value="author">By Author</option>
        </select>
    </div>
    <div class="profile-dropdown">
        <img src="<?php echo $profileImage; ?>" alt="Profile" class="profile-img" />
        <div class="dropdown-content">
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</header>

<div class="blog-container">
<?php
$sql = "SELECT p.*, u.username
        FROM posts p 
        INNER JOIN users u ON p.user_id = u.id 
        WHERE p.status = 'active' 
        ORDER BY p.created_at DESC";

$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "
        <div class='blog-post'>
            <h2>Author : " . htmlspecialchars($row['username']) . "</h2>
            <h2><a href='viewpost.php?id=" . $row['id'] . "' class='blog-title-link'>Title : " . htmlspecialchars($row['title']) . "</a></h2>
            <p class='meta'>Posted on " . date('F j, Y', strtotime($row['created_at'])) . "</p>
            <p>" . nl2br(substr($row['content'], 0, 100)) . "...</p>
        </div>";
    }
} else {
    echo "<h1>No blog posts found.</h1>";
}
?>
</div>

<script>
$(document).ready(function() {
    function fetchPosts() {
        var search_item = $("#search_item").val();
        var filter_item = $("#filter_option").val();

        $.ajax({
            url: "search.php",
            method: "POST",
            data: { char: search_item, filter: filter_item },
            success: function(data) {
                $(".blog-container").html(data);
            }
        });
    }

    $("#search_item").on("keyup", function() {
        fetchPosts();
    });

    $("#filter_option").on("change", function() {
        fetchPosts();
    });
});
</script>

</div>

</body>
</html>
