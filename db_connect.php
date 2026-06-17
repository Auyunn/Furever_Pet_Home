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

    try 
    {
    $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    } 
    catch (PDOException $e) 
    {
        die("Connection failed: " . $e->getMessage());
    }
?>