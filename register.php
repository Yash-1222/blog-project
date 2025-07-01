<!-- textarea, radio, checkbox, dropdown, image upload -->
<?php
     include "config.php";
     error_reporting(E_ALL);
ini_set('display_errors', 1);
        $name=$email=$pwd=$text=$gender=$country=$img=$file=$role="";
        $nameerr=$emailerr=$pwderr=$texterr=$gendererr=$countryerr=$imgerr=$hobbieerr=$fileerr=$roleerr="";
            $allowedhobbies=['Reading','Traveling','Gaming'];
            $hobbies= [];

          function test_input($data){
            $data= trim($data);
            $data= stripslashes($data);
            $data= htmlspecialchars($data);
            return $data;
        }

        if($_SERVER['REQUEST_METHOD']==="POST"){
            $name =isset($_POST['name'])?test_input($_POST['name']):'';
            $email =isset($_POST['mail'])?test_input($_POST['mail']):'';
            $pwd =isset($_POST['pwd'])?test_input($_POST['pwd']):'';
            $text =isset($_POST['comments'])?test_input($_POST['comments']):'';

            $has_error=false;
        if(!$name||!$email||!$pwd){
            echo "<p class='error'>All fields are required</p>";
            $has_error=true;
        }
        if(empty($name)){
            $nameerr="Name is required";
              $has_error=true;
        }
        else if(!preg_match('/^[a-zA-Z ]+$/', $name)) {
            $nameerr = "Only alphabets and spaces are allowed";
             $has_error=true;
        }
      
        if(empty($email)){
            $emailerr="Gmail is required";
              $has_error=true;
        }
        else if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            $emailerr="Email is not valid";
             $has_error=true;
        }
         else{
            $checkemail ="SELECT * FROM users WHERE email='$email'";
            $check_query =mysqli_query($conn,$checkemail);
            if(mysqli_num_rows($check_query)>0){
                $emailerr="Email Already exist please login";
                $has_error=true;
            }
        }
          $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        if(empty($pwd)){
            $pwderr="password is required";
            $has_error=true;
        }
        else if(!preg_match($pattern,$pwd)){
            $pwderr="passward must have atleast 8 character cantaining atleast one uppercase ,lowercase,number,special  character ";
            $has_error=true;
        }
        
        if(empty($text)){
            $texterr="textarea must have content ";
            $has_error=true;
        }
      
        $gender = isset($_POST['gender']) ? test_input($_POST['gender']) : '';
        if(empty($gender)){
            $gendererr="gender is required";
            $has_error=true;
        }


        if (isset($_POST['hobbies']) && is_array($_POST['hobbies']) && count($_POST['hobbies']) > 0) {
            $hobbies = array_map('test_input', $_POST['hobbies']);
            $hobbies_sting = implode(',',$hobbies);
        } else {
            $hobbieerr = "Please select at least one hobby";
            $has_error = true;
        }

        

       $country = isset($_POST['country']) ? $_POST['country'] : '';

        if (empty($country)) {
            $countryerr = "Please select at least one country";
            $has_error = true;
        }
        
          $role = isset($_POST['role']) ? $_POST['role'] : '';

        if (empty($role)) {
            $roleerr = "Please select at least one role";
            $has_error = true;
        }
        


        if(isset($_FILES['myfile'])){
            $file = $_FILES['myfile'];
            if($file['error']==0){
                $allowed_ext = array("jpg" => "image/jpg",
                               "jpeg" => "image/jpeg",
                               "gif" => "image/gif",
                               "png" => "image/png");
                $filename=basename($file['name']);
                $upload_dir="../uploads/";
                $filepath= $upload_dir.$filename;
                $filesize =$file['size'];
                $filetype =$file['type'];
                $ext =strtolower(pathinfo($filename,PATHINFO_EXTENSION));

                if(!array_key_exists($ext,$allowed_ext)){
                    $fileerr="error :Please select valid file format";
                    $has_error=true;
                }
                $size = (2*1024*1024);

                if($filesize>$size){
                    $fileerr="File Size is larger than limit";
                    $has_error=true;
                }

                if(in_array($filetype,$allowed_ext)){
                    if(file_exists($filepath)){
                        $fileerr="$filename already exist";
                        $has_error=true;
                    }
                    else{
                        if(!move_uploaded_file($file['tmp_name'],$filepath)){
                          $fileerr="file can't uploaded";
                          $has_error=true;  
                        } 
                        else{
                            echo "uploaded successfully";
                        }          
                    }

                } else{
                    $fileerr="please try again";
                    $has_error=true;
                }
                
            }
        }else{
            $fileerr="please select file";
            $has_error=true;
        }

        if(!$has_error){
            $status ="active";
            $pass =password_hash($pwd,PASSWORD_DEFAULT);
             $sql="INSERT INTO users(username,email,password_hash,comment,gender,hobbies,country,profile_name,profile_path,roles,status) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
                $stmt=mysqli_prepare($conn,$sql);
                if(!$stmt){
                    echo "query failed".mysqli_error($conn);
                    exit;
                }
                if(!mysqli_stmt_bind_param($stmt,"sssssssssss",$name,$email,$pass,$text,$gender,$hobbies_sting,$country,$filename,$filepath,$role,$status)){
                    echo "binding failed".'('.mysqli_stmt_errno($stmt).')'.mysqli_stmt_error($stmt);
                    exit;
                }
                if(!mysqli_stmt_execute($stmt)){
                    echo "query failed".'('.mysqli_stmt_errno($stmt).')'.mysqli_stmt_error($stmt);
                }
                else{
                    echo "Registration successfully";
                }
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
            }
        }
      
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert</title>
    <style>
        .error{
            color:red;
            font-weight:10px;
        }
    </style>
</head>
<!-- textarea, radio, checkbox, dropdown, image upload -->
<body>
    
    <form action="" method="post" enctype="multipart/form-data">

        <label for="role">Roles:</label>
        <select id="role" name="role">
        <option value=""disabled selected>Select your role :</option>
        <option value="admin" <?PHP if($country=='admin') echo "selected";?>>Admin</option>
        <option value="editor" <?PHP if($country=='editor') echo "selected";?>>Editor</option>
        <option value="viewer" <?PHP if($country=='viewer') echo "selected";?>>Viewer</option>
        </select><span class="error"><?php echo $roleerr;?></span><br><br>

        <label for="name">Name :</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name);?>"><span class="error"><?php echo $nameerr;?></span><br>
        <label for="mail">email :</label>
        <input type="email" id="mail" name="mail" value="<?php echo htmlspecialchars($email);?>"><span class="error"><?php echo $emailerr;?></span><br>
        
        <label for="comments">Comments:</label>
        <textarea id="comments" name="comments" rows="4" cols="30" placeholder="Enter your comments..." ><?php echo htmlspecialchars($text);?></textarea><span class="error"><?php echo $texterr;?></span><br>
        
        <label>Gender:</label>
        <input type="radio" id="male" name="gender" value="male"<?php if($gender=="male") echo "checked"; ?>>
        <label for="male">Male</label>
        <input type="radio" id="female" name="gender" value="female"<?php if($gender=="female") echo "checked"; ?>>
        <label for="female">Female</label>
        <input type="radio" id="other" name="gender" value="other" <?php if($gender=="other") echo "checked"; ?>>
        <label for="other">Other</label><span class="error"><?php echo $gendererr;?></span><br>

        <label>Hobbies:</label>
        <input type="checkbox" id="reading" name="hobbies[]" value="Reading"  <?php if( isset($hobbies)&&in_array("Reading", $hobbies)) echo 'checked'; ?>>
        <label for="reading">Reading</label>
        <input type="checkbox" id="traveling" name="hobbies[]" value="Traveling" <?php if(isset($hobbies)&&in_array("Traveling", $hobbies)) echo 'checked';?>>
        <label for="traveling">Traveling</label>
         <input type="checkbox" id="gaming" name="hobbies[]" value="Gaming" <?php if(isset($hobbies)&&in_array("Gaming", $hobbies)) echo 'checked'; ?>>
        <label for="gaming">Gaming</label><br><span class="error"><?php echo $hobbieerr;?></span><br>


        <label for="country">Country:</label>
        <select id="country" name="country">
        <option value="" disabled selected>Select your country</option>
        <option value="United_States" <?PHP if($country=='United_States') echo "selected";?>>United States</option>
        <option value="United_Kingdom" <?PHP if($country=='United_Kingdom') echo "selected";?>>United Kingdom</option>
        <option value="Canada" <?PHP if($country=='Canada') echo "selected";?>>Canada</option>
        <option value="Australia" <?PHP if($country=='Australia') echo "selected";?>>Australia</option>
        <option value="other" <?PHP if($country=='other') echo "selected";?>>Other</option>
        </select><span class="error"><?php echo $countryerr;?></span><br><br>

        <label for="pwd">Password:</label>
        <input type="password" id="pwd" name="pwd"><span class="error"><?php echo $pwderr;?></span><br><br>

        <label for="myfile">Select a file to upload</label>
        <input type="file" name="myfile" id="myfile"><span class="error"><?php echo $fileerr;?></span><br><br><br><br>
        <button type="submit">Submit</button>
        You have an account<a href="login.php">Login</a>
        </form>
        
</body>
</html>