<?php

session_start();
if(!isset($_SESSION["id"])){
    header("Location: index.php");exit;
}
include "connect.php";
$stmt = $conn->prepare("select * from users where unique_id = ?");
$stmt->bind_param("s",$_SESSION["id"]);
$stmt->execute();
$result = mysqli_stmt_get_result($stmt);
$rowuser = mysqli_fetch_assoc($result);






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
    <div class="users">
        <div class="info">
            <div class="user">
                <img class="profile_pic" src=<?php echo "uploads/". htmlspecialchars($rowuser["img"]) ?> alt="">
                <div class="name">
                    <h3><?php echo htmlspecialchars($rowuser["fname"])." ".$rowuser["lname"] ?></h3>
                    <p><?php echo htmlspecialchars($rowuser["status"])  ?></p>
                </div>
            </div>
            <button><a href="logout.php" style="color: white;">Logout</a></button>
        </div>
        <hr class="hr">
        <form action="" method="get" class="search">
            <h4>Select an user to start chat</h4>
            <input type="search" name="search" placeholder="Enter name to search...">
            <button type="submit" class="search_btn"><img src="search.png" alt="" width="30px" height="30px"></button>
        </form>
        <div class="messages">
            
        </div>
        
    </div>
    <script>
        const search_btn = document.querySelector(".search_btn");
        let inp = document.querySelector(".search input");
        let h4 = document.querySelector(".search h4");
        let img = document.querySelector("form button img");
        search_btn.addEventListener("click",function(e){
            e.preventDefault();
            inp.classList.toggle("active");
            h4.classList.toggle("active");
            if(inp.classList.contains("active")){
               img.setAttribute("src","close.png"); 
            }else{
                img.setAttribute("src","search.png");
            }
            
        })  
        let message = document.querySelector(".messages");
        let isSearching = false;
        let form = document.querySelector("form");
        let searchBar = form.querySelector("input");
        //ajax

        setInterval(() => {
           form.addEventListener("submit",(e)=>e.preventDefault());
        searchBar.onkeyup = ()=>{
            isSearching = true;
            let content = searchBar.value??"";
            
                let xhr= new XMLHttpRequest();
            xhr.open("GET","search.php?search="+encodeURIComponent(content));
            xhr.onload = ()=>{
                if(xhr.readyState == 4 && xhr.status == 200){
                let data = xhr.response;
                
               if (data == ""){
                    let h2 = document.createElement("h2");
                    h2.textContent = "no users matche your search";
                    
                    message.innerHTML = "";
                    message.appendChild(h2)
               }else{
                message.innerHTML = "";
                message.innerHTML = data;
               }

                
            }
            

            }
            xhr.send();
        } 
        }, 500);
         
        
        setInterval(() => {
            if(isSearching) return;
          let xhr= new XMLHttpRequest();
            xhr.open("GET","get_users.php");
            xhr.onload = ()=>{
                if(xhr.readyState == 4 && xhr.status == 200){
                let data = xhr.response;
                
                if(data == ""){
                    let h2 = document.createElement("h2");
                    h2.textContent = "no users are logged in";
                    message.appendChild(h2);

                    
                }else{
                    
                    message.innerHTML = data;
                }
                
                }
        }
        
        xhr.send();  
        }, 500);

     
       
        

    </script>
    
</body>
</html>