<?php

session_start();
include "connect.php";
if(!isset($_SESSION["id"])){
    header("Location: index.php");exit;
}
$status = "offline";
$sql = $conn->prepare("update users set status = ? where unique_id=?");
$sql->bind_param("si",$status,$_SESSION["id"]);
$sql->execute();
session_unset();
session_destroy();
header("Location: index.php");exit;

?>
