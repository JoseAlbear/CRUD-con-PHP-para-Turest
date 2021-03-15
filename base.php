<?php
    //error_reporting(0);
    $host ="mysql:host=localhost;dbname=restaurante";
    $user = "root";
    $pwd = "18Restaurante591";
    /*$serverName = "localhost";
    $user = "root";
    $pwd = "18Restaurante591";
    $db = "restaurante";
    $conn = new mysqli($serverName,$user,$pwd,$db);
    if($conn->conect_errno){
        die("Conexión fallida...!!!");
    }*/
    $pdo = new PDO($host, $user, $pwd);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>

