<?php

session_start();
require "connect.php";
$empty = false;
$wrg_email = false;
$exists = false;
$wrg_type = false;
$short_pass = false;
$status = "offline";
if(isset($_COOKIE["id"])){
    header("Location: users.php");
}
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])){
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $email = $_POST["email"];
    $pass = $_POST["pass"];
    $file = $_FILES["img"];
    

    if(empty($fname)||empty($lname)||empty($email)||empty($pass)||$file["error"] == 4){
        $empty = true;
    }
    $types = ["image/jpeg","image/jpg","image/png"];
    $extension = ["png","jpeg","jpg"];

    $file_exet = pathinfo($file["name"],PATHINFO_EXTENSION);

    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $wrg_email = true;
    }elseif(!in_array($file["type"],$types)&&!in_array($file_exet,$extension)){
        $wrg_type = true;
    }else{
        $select = $conn->prepare("select * from users where email = ?");
    $select->bind_param("s",$email);
    $select->execute();
    $result = mysqli_stmt_get_result($select);
    if(mysqli_num_rows($result)>0){
        $exists = true;
    }else{
        $filename = uniqid().".".$file_exet;
        move_uploaded_file($file["tmp_name"],"uploads/".$filename);
        $random_id = rand(time(),10000000);
        if(strlen($pass)<6){
            $short_pass = true;
        }else{
          $pass = password_hash($pass,PASSWORD_DEFAULT);
        $insert = $conn->prepare("insert into users (unique_id,fname,lname,email,password,img,status) values(?,?,?,?,?,?,?)");
        $status = "online now";
        $_SESSION["status"] = $status;
        $insert->bind_param("issssss",$random_id,$fname,$lname,$email,$pass,$filename,$status); 
        $insert->execute();
        $_SESSION["id"] = $random_id;
        setcookie("id",$_SESSION["id"],strtotime("+2 days"));
        header("Location: users.php");
         
        }
        
    }
    }

    

    

    
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>chat app</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <form action="" method="post" enctype="multipart/form-data">
        <h1>Realtime Chat App</h1>
        <hr>

        <div class="error_message"></div>
        <div class="username">
            <div class="fname">
            <label for="">First Name</label>
            <input type="text" placeholder="First Name" name="fname" required>
            </div>
            <div class="lname">
               <label for="">Last Name</label><br>
            <input type="text" placeholder="Last Name" name="lname" required> 
            </div>
            
        </div>
        <div class="from_element">
        <label for="">Email Address</label><br>
        <input type="email" name="email" placeholder="Enter your email" required><br>
        </div>
        <div class="form_element">
          <label for="">Password</label><br>
        <input type="password" name="pass" placeholder="Enter your password" required><br>  
        <img src="view.png" alt="" class="eye">
        </div>
        <div class="form_element">
            <label for="">Select image</label><br>
        <input type="file" name="img" required><br>
        </div>
        
        

        <button type="submit" name="submit" value="submit">Continue to Chat</button>

        <p>Already signed up?<a href="login.php">Login now</a></p>

    </form>
    <script>
        let img = document.querySelector("img");
        let pass = document.querySelector("input[type='password']");
        console.log(img.src)
        img.addEventListener("click",()=>{
            if(pass.type == "password"){
                img.setAttribute("src","hide.png");
                pass.setAttribute("type","text")
            
            }else if(pass.type == "text"){
                img.setAttribute("src","view.png");
                pass.setAttribute("type","password")
            }
        });
        
    </script>

    <?php if($empty):?>
        <script>
            let div = document.getElementsByClassName("error_message")[0];
            div.textContent = "all fields are necessary";
            div.style.display = "block";

        
        </script>
    <?php endif; ?>
    <?php if($wrg_email): ?>
<script>
    let div = document.getElementsByClassName("error_message")[0];
    div.textContent = "Invalid email format!";
    div.style.display = "block";

</script>
<?php endif; ?>

<?php if($wrg_type): ?>
<script>
    let div = document.getElementsByClassName("error_message")[0];
    div.textContent = "Only JPG, JPEG, or PNG files are allowed.";
                div.style.display = "block";

</script>
<?php endif; ?>

<?php if($exists): ?>
<script>
    let div = document.getElementsByClassName("error_message")[0];
    div.textContent = "This email is already registered.";
                div.style.display = "block";

</script>
<?php endif; ?>
<?php if($short_pass): ?>
<script>
    let div = document.getElementsByClassName("error_message")[0];
    div.textContent = "passworrd must be at least > 6";
                div.style.display = "block";

</script>
<?php endif; ?>

    
</body>
</html>