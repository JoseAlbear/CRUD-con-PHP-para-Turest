<?php
    
    class Datos{  
        ///////////////////////////////////////////////////////////////////////////////////
        public function insert($option, $data){
            header('Content-Type: application/json');
            switch ($option){
                case "order":
                    include 'base.php';
                    try{
                        $pdo->beginTransaction();
                        $insertSql = "INSERT INTO orden (mesaOrden,fechaOrden,precioOrden,estado) VALUES (?,?,?,?)";
                        $statement = $pdo->prepare($insertSql);
                        $statement->execute([$data->table, $data->date, $data->price, $data->state]);
                        $id = $pdo->lastInsertId();
                        $pdo->commit();
                        $response = array("responseResult" => "true", "id" => $id);
                        echo json_encode($response);
                    } catch (Exception $ex) {
                        $response = array("responseResult" => "false", "responseError" => "ERROR AL INSERTAR ORDER " . $ex);
                        echo json_encode($response);
                        $pdo->rollBack();
                    }
                break;  
                
                case "detailOrder":
                    include 'base.php';
                    $idGeneral = 0;
                    try{
                        $pdo->beginTransaction();
                        foreach ($data as $detailOrder){
                            $idGeneral = $detailOrder['idOrder'];
                            $id_order = $detailOrder['idOrder'];
                            $id_dish = $detailOrder['idDish'];
                            $quantity = $detailOrder['quantity'];
                            $price = $detailOrder['price'];
                            $observation = $detailOrder['observation'];
                            /////////////////////////////////////////////
                            $insertSql = "INSERT INTO detalleorden (ID_ORDEN,ID_PLATO,cantidadDetalleOrden,precioDetalleOrden,observacion) VALUES (?,?,?,?,?)";
                            $statement = $pdo->prepare($insertSql);
                            $statement->execute([$id_order, $id_dish, $quantity, $price, $observation]);
                        }
                        $pdo->commit();
                        $response[] = array("responseResult" => "true");
                        echo json_encode($response);
                    } catch (PDOException $ex) {
                        $pdo->rollBack();
                        //Remove the registry with id of the detail order
                        try{
                            $sql = "DELETE FROM orden WHERE ID_ORDEN = ?";
                            $statement = $pdo->prepare($sql);
                            $statement->execute([$idGeneral]);
                            $response[] = array("responseResult" => "false", "message" => "SE ELIMNO CORRECTAMENTE EL REGISTRO DE LA TABLA ORDEN CON EL ID: " . $idGeneral,
                                "responseError" => "ERROR AL INSERTAR DETAIL ORDER " . $ex);
                            echo json_encode($response);
                        } catch (Exception $ex1){
                            $response[] = array("responseResult" => "false", "message" => "NO SE PUDO ELIMINAR EL REGISTRO DE LA TABLA ORDEN CON EL ID: ".  $idGeneral,
                                "responseError" => "ERROR AL INSERTAR DETAIL ORDER " . $ex .$ex1);
                            echo json_encode($response);
                        }
                    }
                break;  
                
            }
        }
        ///////////////////////////////////////////////////////////////////////////////////
        public function insertAndModify($option, $data, $priceTotal){
            header('Content-Type: application/json');
            switch ($option){ 
                case "detailOrder":
                    include 'base.php';
                    $idGeneral = 0;
                    try{
                        $pdo->beginTransaction();
                        foreach ($data as $detailOrder){
                            $idGeneral = $detailOrder['idOrder'];
                            $id_order = $detailOrder['idOrder'];
                            $id_dish = $detailOrder['idDish'];
                            $quantity = $detailOrder['quantity'];
                            $price = $detailOrder['price'];
                            $observation = $detailOrder['observation'];
                            /////////////////////////////////////////////
                            $insertSql = "INSERT INTO detalleorden (ID_ORDEN,ID_PLATO,cantidadDetalleOrden,precioDetalleOrden,observacion) VALUES (?,?,?,?,?)";
                            $statement = $pdo->prepare($insertSql);
                            $statement->execute([$id_order, $id_dish, $quantity, $price, $observation]);
                        }
                        $modifySql = "UPDATE orden SET precioOrden = ? WHERE ID_ORDEN = ?";
                        $statement = $pdo->prepare($modifySql);
                        $statement->execute([$priceTotal, $idGeneral]);
                        $pdo->commit();
                        $response[] = array("responseResult" => "true");
                        echo json_encode($response);
                    } catch (PDOException $ex) {
                        $pdo->rollBack();
                        $response[] = array("responseResult" => "false", "responseError" => "ERROR AL INSERTAR DETAIL ORDER " . $ex);
                        echo json_encode($response);
                    }
                break; 
                
                case "createdDetailOrder":
                    include 'base.php';
                    
                    try{
                        $pdo->beginTransaction();
                        $idGeneral = $data[0]['idOrder'];
                        //ELIMINA TODOS LOS DETALLE DE LA ORDEN
                        $deleteSql = "DELETE FROM detalleorden WHERE ID_ORDEN = ?";
                        $statement = $pdo->prepare($deleteSql);
                        $statement->execute([$idGeneral]);
                        //INSERTA LOS NUEVOS DETALLE DE ORDEN
                        foreach ($data as $detailOrder){
                            $id_order = $detailOrder['idOrder'];
                            $id_dish = $detailOrder['idDish'];
                            $quantity = $detailOrder['quantity'];
                            $price = $detailOrder['price'];
                            $observation = $detailOrder['observation'];
                            /////////////////////////////////////////////
                            $insertSql = "INSERT INTO detalleorden (ID_ORDEN,ID_PLATO,cantidadDetalleOrden,precioDetalleOrden,observacion) VALUES (?,?,?,?,?)";
                            $statement = $pdo->prepare($insertSql);
                            $statement->execute([$id_order, $id_dish, $quantity, $price, $observation]);
                        }
                        //MODIFICA EL PRECIO DE LA ORDEN
                        $modifySql = "UPDATE orden SET precioOrden = ? WHERE ID_ORDEN = ?";
                        $statement = $pdo->prepare($modifySql);
                        $statement->execute([$priceTotal, $idGeneral]);
                        $pdo->commit();
                        $response[] = array("responseResult" => "true");
                        echo json_encode($response);
                    } catch (PDOException $ex) {
                        $pdo->rollBack();
                        $response[] = array("responseResult" => "false", "responseError" => "ERROR AL INSERTAR DETAIL ORDER " . $ex);
                        echo json_encode($response);
                    }
                    
                break;  
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////
        public function query($option){
            switch ($option){
                case "menu":
                    include 'base.php';
                    try{
                        $statement = $pdo->prepare("SELECT T.nombreTipo, COUNT(*) AS 'cantidad' FROM tipo T INNER JOIN plato P ON T.ID_TIPO = P.ID_TIPO  GROUP BY T.nombreTipo ORDER BY T.ubicacionTipo ASC");        
                        $statement->execute();
                        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
                        echo json_encode($results);
                    } catch (PDOException $ex){
                        $response[] = array("responseError" => "ERROR AL CONSULTAR " . $ex);
                        echo json_encode($response);
                    }    
                break;      
                case "dishes":
                    include 'base.php';
                    try{
                        $statement = $pdo->prepare("SELECT P.ID_PLATO, P.nombre, P.precio, T.nombreTipo FROM tipo T INNER JOIN plato P ON T.ID_TIPO = P.ID_TIPO  ORDER BY T.ubicacionTipo,T.ID_TIPO, P.nombre");        
                        $statement->execute();
                        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
                        echo json_encode($results);
                    } catch (PDOException $ex){
                       $response[] = array("responseError" => "ERROR AL CONSULTAR " . $ex);
                       echo json_encode($response);
                    }      
                break;  
                case "orders";
                    include 'base.php';
                    try{
                        $statement = $pdo->prepare("SELECT mesaOrden FROM orden WHERE estado like 'PREPARANDO'");
                        $statement->execute();
                        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
                        echo json_encode($results);
                    } catch (PDOException $ex) {
                        $response[] = array("responseError" => "ERROR AL CONSULTAR ". $ex);    
                        echo json_encode($response);
                    }
                break;    
                /*case "detailOrders";
                    include 'base.php';
                    try{
                        $statement = $pdo->prepare("SELECT D.ID_PLATO, D.cantidadDetalleOrden, P.nombre, D.precioDetalleOrden, D.observacion "
                                . "FROM detalleorden D INNER JOIN plato P ON D.ID_PLATO = P.ID_PLATO INNER JOIN orden O ON D.ID_ORDEN = O.ID_ORDEN "
                                . "WHERE O.mesaOrden LIKE 'MESA 06' AND O.estado LIKE 'PREPARANDO'");
                        $statement->execute();
                        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
                        echo json_encode($results);
                    } catch (PDOException $ex) {
                        $response[] = array("responseError" => "ERROR AL CONSULTAR ". $ex);    
                        echo json_encode($response);
                    }
                break;   */ 
            }
        }
        
        public function queryPersonalized($option, $value){
            switch ($option){
                case "detailOrders";
                    include 'base.php';
                    try{
                        $statement = $pdo->prepare("SELECT D.ID_PLATO, D.cantidadDetalleOrden, P.nombre, D.precioDetalleOrden, D.observacion, D.ID_ORDEN "
                                . "FROM detalleorden D INNER JOIN plato P ON D.ID_PLATO = P.ID_PLATO INNER JOIN orden O ON D.ID_ORDEN = O.ID_ORDEN "
                                . "WHERE O.mesaOrden LIKE ? AND O.estado LIKE 'PREPARANDO'");
                        $statement->execute([$value]);
                        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
                        echo json_encode($results);
                    } catch (PDOException $ex) {
                        $response[] = array("responseError" => "ERROR AL CONSULTAR ". $ex);    
                        echo json_encode($response);
                    }
                break;  
            }
        }
        
        public function querySimple($option, $value){
            switch ($option){
                case "idOrder";
                    include 'base.php';
                    try{
                        $statement = $pdo->prepare("SELECT ID_ORDEN, precioOrden FROM orden WHERE mesaOrden = ? and estado = ? LIMIT 1");
                        $statement->execute([$value, "PREPARANDO"]);
                        $results = $statement->fetch();
                        if($results!= false){
                            $id = $results[0];        
                            $price = $results[1];
                            $response = array("responseResult" => "true", "exist"=> "true", "id" => $id, "price" => $price);
                        }else{
                            $id = 0;
                            $response = array("responseResult" => "true", "exist"=> "false");
                        }
                        echo json_encode($response);
                    } catch (Exception $ex) {
                        $response = array("responseResult" => "false","responseError" => "ERROR AL CONSULTAR " . $ex);
                        echo json_encode($response);
                    }
                break;    
            }
        }
    } 
?>