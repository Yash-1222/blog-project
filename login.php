<?php
session_start();
include 'config.php';
  error_reporting(E_ALL);
ini_set('display_errors', 1);
      function test_input($data){
            $data= trim($data);
            $data= stripslashes($data);
            $data= htmlspecialchars($data);
            return $data;
        }
    $email=$pwd=$role="";
    $emailerr=$pwderr=$roleerr="";
    $no_error=false;
if($_SERVER['REQUEST_METHOD']=='POST'){
    $email =test_input($_POST['mail']);
    $pwd =test_input($_POST['pwd']);
    $role = isset($_POST['role']) ? $_POST['role'] : '';

    $sql="SELECT * FROM users WHERE email='$email'";
    $result=mysqli_query($conn,$sql);
    if(mysqli_num_rows($result)<1){
        $emailerr="Invalid Email";
    }
    else{
        $row=mysqli_fetch_assoc($result);
        if(empty($pwd)){
             $pwderr="Please Enter Password";
            
        }
        else if(!password_verify($pwd,$row['password_hash'])){
            $pwderr="Uncorrect Password ";
           
        }
        else if (empty($role)) {
            $roleerr = "Please select at least one role";
        }
        else if($role!==$row['roles']){
                $roleerr="selected role is not match";
            }

        else if($row['status']=='unactive'){
            echo "User is inactive. contact to admin";
            }
            else if($row['is_deleted']=='1'){
                echo "account is deleted. contact to admin";
            }
            else{
                $_SESSION['id']=$row['id'];
            $_SESSION['user']=$row['username'];
            $_SESSION['profile_img']=$row['profile_path'];
            $_SESSION['role']=$row['roles'];
                if(isset($_POST['remember'])){
                    $token =bin2hex(random_bytes(16));
                    setcookie('remember',$token,time()+(86400*30),'/',"",true,true);
                    $update="UPDATE users SET remember_token=? WHERE id=?";
                    $stmt =mysqli_prepare($conn,$update);
                    mysqli_stmt_bind_param($stmt,"si",$token,$row['id']);
                    mysqli_stmt_execute($stmt);
                }
                $no_error=true;
            }
        }
          
         if($no_error){
            header('Location:dashboard.php');
            exit;
            // switch($role)
            //     {
            //         case 'editor': 
            //             header('Location:dashboard.php');
            //             exit;
            //         case 'admin':
            //             header('Location:dashboard.php');
            //             exit;
            //         case 'viewer':
            //             header('Location:dashboard.php');
            //             exit;
            //         default :
            //             echo "Please select a role";

            //     }
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
  
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
        <label for="mail">email :</label>
        <input type="email" id="mail" name="mail" value="<?php echo htmlspecialchars($email);?>"><span class="error"><?php echo $emailerr;?></span><br>
        <label for="pwd">Password:</label>
        <input type="password" id="pwd" name="pwd" value="<?php echo htmlspecialchars($pwd);?>"><span class="error"><?php echo $pwderr;?></span><br>
         <label><input type="checkbox" name="remember"> Remember Me</label><br>
        <label for="role">Roles:</label>
        <select id="role" name="role">
        <option value="" <?php if ($role == "") echo "selected"; ?>disabled selected>Select your role :</option>
        <option value="admin" <?PHP if($role=='admin') echo "selected";?>>Admin</option>
        <option value="editor" <?PHP if($role=='editor') echo "selected";?>>Editor</option>
        <option value="viewer" <?PHP if($role=='viewer') echo "selected";?>>Viewer</option>
        </select><span class="error"><?php echo $roleerr;?></span><br><br>
        <button type="submit">Submit</button>
         <a href="forget-password.php">Forget passward</a>
    </form>
     Don't have an account<a href="register.php">Register</a>
</body>
</html>
