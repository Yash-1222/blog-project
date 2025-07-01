<?php
if(isset($_GET['id'])){
    $id=$_GET['id'];
}
else{
    $id="";
}
include "config.php";
   error_reporting(E_ALL);
ini_set('display_errors', 1);
$sql ="SELECT * FROM users WHERE id='$id'";
$result =mysqli_query($conn,$sql);
if($result){
    if(mysqli_num_rows($result)){
        while($row =mysqli_fetch_assoc($result)){?>
            <h1>Update user</h1>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
            <input type="hidden" name="pid" value="<?php echo $row['id'];?>"><br><br>
            <label for="name">Name :</label>
            <input type="text" name="name" id="name" value="<?php echo $row['username'];?>"><br><br>
            <label for="content">Content :</label>
            <textarea name="content" id="content" rows='1' cols='20'><?php echo $row['comment'];?></textarea><br><br>
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" value="<?php echo $row['email'];?>"><br><br>
            <label for="role">Status :</label>
            <input type="text" name="role" id="role" value="<?php echo $row['roles'];?>"><br><br>
                <select name="status" id="status">
                <option value="active" <?PHP if($row['status']=='active') echo "selected";?>>Active</option>
                <option value="unactive" <?PHP if($row['status']=='unactive') echo "selected";?>>Unactive</option>
            </select><br><br>
            <button type="submit">Submit</button>
            </form>

<?php
        }
    }
}else{
    echo "query Unsucessful !".mysqli_error($conn);
    exit;
}

if($_SERVER['REQUEST_METHOD']==="POST"){
    $pid=$_POST['pid'];
    $name =$_POST['name'];
    $content =$_POST['content'];
    $email =$_POST['email'];
    $role =$_POST['role'];
    $status=$_POST['status'];

    $update ="UPDATE users SET username='$name',comment='$content',email='$email',roles='$role',status='$status' WHERE id =$pid";
    $result = mysqli_query($conn,$update);
    if($result){
        echo "updated successfully";
        header('Refresh:3,URL=users.php');
    }else{
        echo "query failed".mysqli_error($conn);
    }
}
mysqli_close($conn);
?>

   
