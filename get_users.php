<?php
session_start();
include "connect.php";

$currentUserId = $_SESSION["id"];

$users = $conn->prepare("SELECT * FROM users WHERE unique_id != ?");
$users->bind_param("i", $currentUserId);
$users->execute();
$usrresult = mysqli_stmt_get_result($users);

if (mysqli_num_rows($usrresult) > 0) {
    while ($row = mysqli_fetch_assoc($usrresult)) {
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
        $seenClass = "'font-weight: normal';";
        $time = "";

        if ($msgRow = mysqli_fetch_assoc($result)) {
            $prefix = ($msgRow["send_id"] == $currentUserId) ? "You: " : "";
            $msgRow["msg"] = strlen($msgRow["msg"])>28?substr($msgRow["msg"],0,28)."...":$msgRow["msg"];
            
            $lastMsg = $prefix . htmlspecialchars($msgRow["msg"]);
            if($msgRow["receive_id"] == $currentUserId){
            $seenClass = $msgRow["seen"] == 0? "'font-weight: 900';" : "'font-weight: normal';";
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
?>
