<?php
session_start();

include "config.php";
  error_reporting(E_ALL);
ini_set('display_errors', 1);
$title=$content="";
$titleerr=$contenterr="";

function test_input($data)
{
    $data =trim($data);
    $data=stripslashes($data);
    $data=htmlspecialchars($data);
    return $data;
}

$has_error=false;


if(isset($_SESSION['id'])){

    $userid =$_SESSION['id'];

if($_SERVER['REQUEST_METHOD']==='POST'){
    
    $title = isset($_POST['title'])?test_input($_POST['title']):"";
    $content = isset($_POST['content'])?test_input($_POST['content']):"";
    
    if(empty($title)){
        $titleerr="Title field is required";
        $has_error =true;
    }
    if(empty($content)){
        $contenterr="Content field is required";
        $has_error=true;
    }

    if(!$has_error){
        $sql="INSERT INTO posts(user_id,title,content) VALUES('$userid','$title','$content')";
        $result=mysqli_query($conn,$sql);
        if(!$result){
            echo "query Unsucessful".mysqli_error($conn);
        }
        else{
            echo "data inserted successfully";
            header('Refresh:3,URL=editor.php');
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
      <style>
        .error{
            color:red;
            font-weight:10px;
        }
    </style>
</head>
<body>
    <h1>CREATING BLOG</h1>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
        <label for="title">Title :</label>
        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>"> <span class="error"><?php echo htmlspecialchars($titleerr);?></span><br><br>
        <label for="content">Content :</label>
        <textarea name="content" id="content" rows="4" cols="30"><?php echo htmlspecialchars($content); ?></textarea><span class="error"><?php echo htmlspecialchars($contenterr);?></span>  <br><br>      
        <button type="submit">Submit</button>
    </form>
</body>
</html>
<?php

}
else{
    echo "please login first";
}


?>