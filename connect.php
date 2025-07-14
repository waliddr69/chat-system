<?php
try{
   $conn = mysqli_connect("localhost","root","","chat"); 
}catch(mysqli_sql_exception){
    echo "error";exit;
}
