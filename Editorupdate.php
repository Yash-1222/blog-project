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
$sql ="SELECT * FROM posts WHERE id='$id'";
$result =mysqli_query($conn,$sql);
if($result){
    if(mysqli_num_rows($result)){
        while($row =mysqli_fetch_assoc($result)){?>
            <h1>Update Post</h1>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
            <input type="hidden" name="pid" value="<?php echo $row['id'];?>"><br><br>
            <label for="title">Title :</label>
            <input type="text" name="title" id="title" value="<?php echo $row['title'];?>"><br><br>
            <label for="content">Content :</label>
            <textarea name="content" id="content" rows='1' cols='20'><?php echo $row['content'];?></textarea><br><br>
            <label for="createdat">created_at</label>
            <input type="text" disabled value="<?php echo $row['created_at'];?>"><br><br>
            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="active" <?php if($row['status'] == 'active') echo 'selected'; ?>>Active</option>
                <option value="unactive" <?php if($row['status'] == 'unactive') echo 'selected'; ?>>Unactive</option>
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
    $title =$_POST['title'];
    $content =$_POST['content'];
    $updatedat = date('Y-m-d H:i:s');
    $status =$_POST['status'];
    
    $update ="UPDATE posts SET title='$title',content='$content',updated_at='$updatedat',status='$status' WHERE id =$pid";
    $result = mysqli_query($conn,$update);
    if($result){
        echo "updated successfully";
        header('Refresh:3,URL=editor.php');
    }else{
        echo "query failed".mysqli_error($conn);
    }
}
mysqli_close($conn);
?>

   
