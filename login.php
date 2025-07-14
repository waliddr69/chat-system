<?php

session_start();
require "connect.php";
$empty = false;
$wrg_em = false;
if(isset($_COOKIE["id"])){
    header("Location: users.php");
}
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = $_POST["email"];
    $pass = $_POST["pass"];

    if(empty($email)||empty($pass)){
        echo "all fields are required";
    }elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        echo "this is not a valid email";
    }else{
        $stmt = $conn->prepare("select * from users where email = ?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result)>0){
            $row = mysqli_fetch_assoc($result);
            if(password_verify($pass,$row["password"])){
                $_SESSION["id"] = $row["unique_id"];
               echo "success"; 
            }else{
                echo "wrong password";
            }
            
        }else{
            echo "user doesn't exist";
        }
        $_SESSION["status"] = "online now";
        $stmt = $conn->prepare("update users set status = ? where email = ?");
        $stmt->bind_param("ss",$_SESSION["status"],$email);
        $stmt->execute();
    }
    exit;
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form action="login.php" method="post" enctype="multipart/form-data" class="login">
        <h1>Realtime Chat App</h1>
        <hr>

        <div class="error_message"></div>
        
        <div class="from_element">
        <label for="">Email Address</label><br>
        <input type="email" name="email" placeholder="Enter your email"><br>
        </div>
        <div class="form_element">
          <label for="">Password</label><br>
        <input type="password" name="pass" placeholder="Enter your password"><br> 
        <img class="eye" src="view.png" alt="">
        </div>
        
        
        
        

        <button type="submit" name="submit" value="submit">Continue to Chat</button>

        <p>Don't have an Acount?<a href="index.php">Sign in</a></p>

    </form>

    <script>
        let img = document.querySelector("img");
        let pass = document.querySelector("input[type='password']");
        img.addEventListener("click",()=>{
            if(pass.type == "password"){
                img.setAttribute("src","hide.png");
                pass.setAttribute("type","text")
            
            }else if(pass.type == "text"){
                img.setAttribute("src","view.png");
                pass.setAttribute("type","password")
            }
        });

        //ajax

        const form = document.querySelector("form");
        const err = document.querySelector(".error_message");
        
        form.addEventListener("submit",function(e){
            e.preventDefault();
            let xhr= new XMLHttpRequest();
            xhr.open("POST","login.php");
            xhr.onload = ()=>{
                if(xhr.readyState == 4 && xhr.status == 200){
                let data = xhr.response;
                
                if(data == "success"){
                    window.location.href = "users.php";
                }else {
                    err.style.display = "block";
                    err.textContent = data;
                }
                
                }
        }
        let formdata = new FormData(form);
        xhr.send(formdata);
        })
            
            
            
           
        
        
    </script>
    
</body>
</html>





