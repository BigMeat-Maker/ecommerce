<?php
include(ROOT_DIR."/app/config/DatabaseConnection.php");
    $db = new DatabaseConnection();
    $conn = $db->connectDB();

    $productList=[];

    try {
        $sql  = "SELECT * FROM `products`"; //select statement here
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $productList=$stmt->fetchAll();
        
    } catch (PDOException $e){
       echo "Connection Failed: " . $e->getMessage();
       $db = null;
    }
?>