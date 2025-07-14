<?php

session_start();
include "connect.php";
$seen = 1;

$seenstmt = $conn->prepare("update messages set seen = ? where receive_id = ?");
$seenstmt->bind_param("ii",$seen,$_SESSION["id"]);
$seenstmt->execute();
            $user_stmt = $conn->prepare("SELECT img FROM users WHERE unique_id = ?");
            $user_stmt->bind_param("i", $_SESSION["receive_id"]);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();
            $receiver = $user_result->fetch_assoc();
            $sql = $conn->prepare("select * from messages where (send_id = ? and receive_id = ?) or (send_id = ? and receive_id = ?)");
            $sql->bind_param("iiii",$_SESSION["id"],$_SESSION["receive_id"],$_SESSION["receive_id"],$_SESSION["id"]);
            $sql->execute();
            $result =  mysqli_stmt_get_result($sql);
            if(mysqli_num_rows($result)>0){
            while($rowselect = mysqli_fetch_assoc($result)){
            if($rowselect["send_id"] == $_SESSION["id"]){
                echo "<div id='sender'>
                <p>".htmlspecialchars($rowselect["msg"])."</p>
            </div>";
            }elseif($rowselect["send_id"] == $_SESSION["receive_id"]){
                echo "<div id='receiver'>
                <img src= 'uploads/". htmlspecialchars($receiver["img"]) ."' alt='' class='profile_pic'>
                <p>".htmlspecialchars($rowselect["msg"])."</p>
            </div>";
            }
        }
    }else{
        echo "<h2><span>Say Hi to your Friend!</span></h2>";
    }
            
?>
     