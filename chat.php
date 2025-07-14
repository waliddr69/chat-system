<?php

session_start();
include "connect.php";

if(!isset($_SESSION["id"])){
    header("Location: index.php");
}

$_SESSION["receive_id"] = (int)$_GET["id"];
$stmt = $conn->prepare("select * from users where unique_id = ?");
$stmt->bind_param("i",$_SESSION["receive_id"]);
$stmt->execute();
$result = mysqli_stmt_get_result($stmt);
$row=mysqli_fetch_assoc($result);






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

    <div class="chat">
        <header>
            <a href="users.php"><img src="arrow.png" alt="" width="20px" height="20px" style="cursor: pointer;"></a>
            <img src=<?php echo "uploads/". htmlspecialchars($row["img"]) ?> alt="" class="profile_pic">
            <div class="name">
                <h3><?php echo htmlspecialchars($row["fname"])." ".$row["lname"] ?></h3>
                <p><?php echo htmlspecialchars($row["status"]) ?></p>
            </div>
        </header>

        <section>
                   
            
            
        </section>
        <form action="" method="post" class="typing">
            <input type="text" placeholder="Type a message here..." name="message">
            <button type="submit" class="send"><img src="send.png" alt=""  height="20px" width="20px"></button>
        </form>
        
    </div>

    <script src="chat.js"></script>
    
</body>
</html>