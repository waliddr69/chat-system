<?php

session_start();
include "connect.php";
if(!isset($_SESSION["id"])){
    header("Location: index.php");
}


$stmt = $conn->prepare("select * from users where unique_id = ?");
$stmt->bind_param("i",$_SESSION["receive_id"]);
$stmt->execute();
$result = mysqli_stmt_get_result($stmt);
$row=mysqli_fetch_assoc($result);

//send message

$message = $_POST["message"]??"";

if($message!=""){
    $insert = $conn->prepare("insert into messages(send_id,receive_id,msg) values(?,?,?)");
    $insert->bind_param("iis",$_SESSION["id"],$_SESSION["receive_id"],$message);
    $insert->execute();
    echo "inserted";
}





?>