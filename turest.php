<?php
    require 'datos.php';
    $datos = new Datos();
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        switch ($_GET["method"]){
            case "query":
                $datos->query($_GET["option"]);
            break;  
            case "querySimple":
                $datos->querySimple($_GET["option"], $_GET["value"]);
            break;    
            case "queryPersonalized":
                $datos->queryPersonalized($_GET["option"], $_GET["value"]);
            break;    
        }
    }
    header('Content-Type: application/json');
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        switch ($_GET["method"]){
            case "insertArray":
                $data = json_decode(file_get_contents('php://input'), true);
                $datos->insert($_GET["option"], $data);
            break;    
            case "insertArrayAndModify":
                $data = json_decode(file_get_contents('php://input'),true);
                $datos->insertAndModify($_GET["option"], $data, $_GET["priceTotal"]);
            break;    
            case "insertObject":
                $data = json_decode(file_get_contents('php://input'));
                $datos->insert($_GET["option"], $data);
            break;    
        }
    }
?>
