
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

        table,th,td,tr{
            border:1px solid black;
            border-collapse:collapse;
            padding:10px
        }
        .edit{
            padding:5px;
            background-color: green;
            cursor: pointer;
              text-decoration: none;
        }
        .del{
            padding:5px;
            background-color: red;
            cursor: pointer;
              text-decoration: none;
        }
        .insert{
            padding:5px;
            background-color: skyblue;
            cursor: pointer;
            text-decoration: none;
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

 <?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "config.php";

$sql = "SELECT * FROM users WHERE is_deleted ='0'";

$result = mysqli_query($conn,$sql);
if(!$result){
    echo "query unsuccessful".mysqli_error($conn);
}
$output ="";
if(mysqli_num_rows($result)>0){
       $output='<table>
          <tr>
        <td>Id</td>
        <td>Name</td>
        <td>Email</td>
        <td>Comments</td>
        <td>Gender</td>
        <td>Hobbies</td>
        <td>Country</td>
        <td>Role</td>
        <td>Status</td>
        <td>Action</td>
             
    </tr>';
     while($row=mysqli_fetch_assoc($result)){
        $output.=" <tr>
    <td>{$row['id']}</td>
    <td>{$row['username']}</td>
    <td>{$row['email']}</td>
    <td>{$row['comment']}</td>
    <td>{$row['gender']}</td>
    <td>{$row['hobbies']}</td>
    <td>{$row['country']}</td>
    <td>{$row['roles']}</td>
    <td>{$row['status']}</td>
    <td><a href='deleteuser.php?id={$row['id']}' class='del'>Delete</a>
    <a href='edituser.php?id={$row['id']}' class='edit'>Edit</a></td>
 </tr>";
     }
     $output.='</table>';
     echo $output;
     
}
else{
    echo "NO RECORD FOUND !";
}
?>
    
</div>

</body>
</html>




