<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Blog Post</title>
    <style>
        .profile-img {
            display: block;
            margin: 30px auto 10px auto;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
        }

        #align {
            text-align: center;
        }

        .blog-post {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            margin: 20px;
        }

        .blog-post .meta {
            font-size: 14px;
            color: #777;
            margin-bottom: 15px;
            text-align: center;
        }

        .blog-post p {
            font-size: 16px;
            color: #444;
            line-height: 1.6;
        }
    </style>
</head>
<body>
<?php
include "config.php";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $postId = $_GET['id'];

    $sql = "SELECT p.*, u.username, u.profile_path 
            FROM posts p 
            INNER JOIN users u ON p.user_id = u.id 
            WHERE p.id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $postId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $imagePath = (!empty($row['profile_path']) && file_exists($row['profile_path']))
            ? $row['profile_path']
            : 'uploads/default.png';

        echo '<img src="' . htmlspecialchars($imagePath) . '" alt="Profile Image" class="profile-img">';
        echo '<h2 id="align">' . htmlspecialchars($row['username']) . '</h2>';
        
        echo '<div class="blog-post">';
        echo '<p class="meta align">Posted on ' . date('F j, Y', strtotime($row['created_at'])) . '</p>';
        echo '<h2>' . htmlspecialchars($row['title']) . '</h2>';
        echo '<p>' . nl2br(htmlspecialchars($row['content'])) . '</p>';
        echo '</div>';
    } else {
        echo "<p style='text-align:center;'>Post not found.</p>";
    }
} else {
    echo "<p style='text-align:center;'>Invalid post ID.</p>";
}
?>
</body>
</html>
