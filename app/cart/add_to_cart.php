<?php

if(!isset($_SESSION)){
    session_start();
}

require_once(__DIR__."/../config/Directories.php");
include("../config/DatabaseConnect.php");

if(!isset($_SESSION['username'])){
    header("location: ".BASE_URL."login.php");

}

$db = new DatabaseConnect();

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $productId = htmlspecialchars($_POST["id"]);
    $quantity  = htmlspecialchars($_POST["quantity"]);
    $userId    = $_SESSION["user_id"];
    
    if (trim($productId) == "" || trim($quantity) == "" || trim($userId) == "") 
    {
        $_SESSION["error"] = "Please fill in all the fields";
        header("location: ".BASE_URL."views/product/product.php?id=".$product["id"]);
        exit();
    }
    
    try {
        $conn = $db->connectDB();
        $sql = "SELECT * FROM products WHERE products.id = :p_product_id ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':p_product_id', $productId);
    

        if(!$stmt->execute()){
            $_SESSION['error'] = 'Cannot find the product';
            header("location: ".BASE_URL."views/product/product.php");
            exit;
        }
        $product = $stmt->fetch();
        $totalPrice = (floatval($quantity)* floatval($product["unit_price"]));
        
        //INSERT INTO CART
        $sql = "INSERT INTO carts(user_id, product_id, quantity, unit_price, total_price, created_at, updated_at) VALUES (:p_user_id, :p_product_id, :p_quantity, :p_unit_price, :p_total_price, NOW(), NOW()) ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':p_user_id',$userId);
        $stmt->bindParam(':p_product_id',$productId);
        $stmt->bindParam(':p_quantity',$quantity);
        $stmt->bindParam(':p_unit_price',$product["unit_price"]);
        $stmt->bindParam(':p_total_price',$totalPrice);
        if(!$stmt->execute()){
            $_SESSION['error'] = 'Failed to add to cart';
            header("location: ".BASE_URL."views/product/product.php");
            exit;
        }
        
        $_SESSION["success"] = "Successfully added to cart";
        header("location: ".BASE_URL."views/product/product.php?id=".$product["id"]);
        exit;

    } catch(PDOException $e){
        $_SESSION["error"] = "Connection Failed: " . $e->getMessage();
        header("location: ".BASE_URL."views/product/product.php");
        exit;
    }
}