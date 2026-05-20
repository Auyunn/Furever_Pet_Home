<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "furever_pet_home";

    //create connection
    $conn =new mysqli($servername, $username, $password, $database);

    //check connection
    if($conn->error)
        {
            die("Connection failed: ".$conn->connect_error);
        }
?>