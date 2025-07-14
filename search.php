<?php
session_start();
require "connect.php";

    if(!isset($_SESSION["id"])){
        header("Location: index.php");exit;
    }

    $search = $_GET["search"];
    $currentUserId = $_SESSION["id"];

    if($search!=""){
     $sql = $conn->prepare("select * from users where unique_id != ? and CONCAT(fname,' ',lname) LIKE ? ");

     $searchparam = "%$search%";
     $sql->bind_param("is",$_SESSION["id"],$searchparam);
     $sql->execute();
     $searchresult = mysqli_stmt_get_result($sql);
     if(mysqli_num_rows($searchresult)>0){
        while($row=mysqli_fetch_assoc($searchresult)){
$otherUserId = $row["unique_id"];
        $fullName = htmlspecialchars($row["fname"]) . " " . htmlspecialchars($row["lname"]);
        $img = htmlspecialchars($row["img"]);
        $status = $row["status"];

        // Get last message
        $sql = $conn->prepare("
            SELECT * FROM messages 
            WHERE (send_id = ? AND receive_id = ?) 
               OR (send_id = ? AND receive_id = ?) 
            ORDER BY id DESC LIMIT 1
        ");
        $sql->bind_param("iiii", $currentUserId, $otherUserId, $otherUserId, $currentUserId);
        $sql->execute();
        $result = mysqli_stmt_get_result($sql);
        $lastMsg = "No messages";
        $seenClass = "font-weight: normal;";
        $time = "";

        if ($msgRow = mysqli_fetch_assoc($result)) {
            $prefix = ($msgRow["send_id"] == $currentUserId) ? "You: " : "";
            $msgRow["msg"] = strlen($msgRow["msg"])>28?substr($msgRow["msg"],0,28)."...":$msgRow["msg"];
            $lastMsg = $prefix . htmlspecialchars($msgRow["msg"]);
            if($msgRow["receive_id"] == $currentUserId){
            $seenClass = $msgRow["seen"] == 0? "'font-weight: 900;'" : "font-weight: normal;";
            }
            $time = $msgRow["time"];
        }

        // Online or Offline indicator
        $statusClass = ($status === "online now") ? "new_msg" : "offline";

        // Output user block
        echo "<a href='chat.php?id={$otherUserId}' class='auser'>
                <div class='message'>
                    <div class='user'>
                        <img class='profile_pic' src='uploads/{$img}'>
                        <div class='name'>
                            <h3>{$fullName}</h3>
                            <p style={$seenClass}>{$lastMsg}</p>
                        </div>
                    </div>
                    <div class='status'>
                    <div class='{$statusClass}'></div>
                    <div class='time'>{$time}</div>
                    </div>
                </div>
              </a>
              <hr class='hr'>";
    }
} else {
    echo "";
}


}else{
    
    $users = $conn->prepare("select * from users where unique_id != ?");
        $users->bind_param("i",$_SESSION["id"]);
        $users->execute();
        $searchresult = mysqli_stmt_get_result($users);
        if(mysqli_num_rows($searchresult)>0){
        while($row=mysqli_fetch_assoc($searchresult)){
               $otherUserId = $row["unique_id"];
        $fullName = htmlspecialchars($row["fname"]) . " " . htmlspecialchars($row["lname"]);
        $img = htmlspecialchars($row["img"]);
        $status = $row["status"];


        // Get last message
        $sql = $conn->prepare("
            SELECT * FROM messages 
            WHERE (send_id = ? AND receive_id = ?) 
               OR (send_id = ? AND receive_id = ?) 
            ORDER BY id DESC LIMIT 1
        ");
        $sql->bind_param("iiii", $currentUserId, $otherUserId, $otherUserId, $currentUserId);
        $sql->execute();
        $result = mysqli_stmt_get_result($sql);
        $lastMsg = "No messages";
        $seenClass = "font-weight: normal;";
        $time = "";

        if ($msgRow = mysqli_fetch_assoc($result)) {
            $prefix = ($msgRow["send_id"] == $currentUserId) ? "You: " : "";
            $msgRow["msg"] = strlen($msgRow["msg"])>28?substr($msgRow["msg"],0,28)."...":$msgRow["msg"];
            $lastMsg = $prefix . htmlspecialchars($msgRow["msg"]);
            if($msgRow["receive_id"] == $currentUserId){
            $seenClass = $msgRow["seen"] == 0? "'font-weight: 900;'" : "font-weight: normal;";
            }
            $time = $msgRow["time"];
        }

        // Online or Offline indicator
        $statusClass = ($status === "online now") ? "new_msg" : "offline";

        // Output user block
        echo "<a href='chat.php?id={$otherUserId}' class='auser'>
                <div class='message'>
                    <div class='user'>
                        <img class='profile_pic' src='uploads/{$img}'>
                        <div class='name'>
                            <h3>{$fullName}</h3>
                            <p style={$seenClass}>{$lastMsg}</p>
                        </div>
                    </div>
                    <div class='status'>
                    <div class='{$statusClass}'></div>
                    <div class='time'>{$time}</div>
                    </div>
                </div>
              </a>
              <hr class='hr'>";
    }
} else {
    echo "no users are logged in";
}

}

