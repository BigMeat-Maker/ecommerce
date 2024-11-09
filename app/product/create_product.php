<?php

if(!isset($_SESSION)){
    session_start();
}

require_once(__DIR__."/../config/Directories.php"); //to handle folder specific path
include("../config/DatabaseConnection.php"); //to access database connection

$db = new DatabaseConnection(); //make a new database instance

if($_SERVER["REQUEST_METHOD"] == "POST"){


    // retrieve user input
    $productName = htmlspecialchars($_POST["productName"]);
    $productDesc = htmlspecialchars($_POST["productDesc"]);
    $category = htmlspecialchars($_POST["category"]);
    $basePrice = htmlspecialchars($_POST["basePrice"]);
    $numberOfStocks = htmlspecialchars($_POST["numberOfStocks"]);
    $unitPrice = htmlspecialchars($_POST["unitPrice"]);
    $totalPrice = htmlspecialchars($_POST["totalPrice"]);


    // validate user input
    if(trim($productName) == "" || empty($productName)){
        $_SESSION["error"] = "Product Name field is empty";
        header("location: ".BASE_URL."views/admin/product/add.php"); }

   if(trim($productDesc) == "" || empty($productDesc)){
    $_SESSION["error"] = "Product Description field is empty";
    header("location: ".BASE_URL."views/admin/product/add.php");
   }

    if(trim($category) == "" || empty($category)){
    $_SESSION["error"] = "Product's Category field is empty";
    header("location: ".BASE_URL."views/admin/product/add.php");
    }

    if(trim($basePrice) == "" || empty($basePrice)){
        $_SESSION["error"] = "Product's Base Price field is empty";
        header("location: ".BASE_URL."views/admin/product/add.php");
    }

   if(trim($numberOfStocks) == "" || empty($numberOfStocks)){
    $_SESSION["error"] = "Product's Number of Stocks field is empty";
    header("location: ".BASE_URL."views/admin/product/add.php");
    }

    if(trim($unitPrice) == "" || empty($unitPrice)){
        $_SESSION["error"] = "Product's Unit Price field is empty";
        header("location: ".BASE_URL."views/admin/product/add.php");
        }

    if(trim($totalPrice) == "" || empty($totalPrice)){
        $_SESSION["error"] = "Product's Total Price field is empty";
        header("location: ".BASE_URL."views/admin/product/add.php");
         }

    if(!isset($_FILES['productImage']) || $_FILES['productImage']['error'] !== 0){
        $_SESSION["error"] = "No image attached";
        header("location: ".BASE_URL."views/admin/product/add.php");
         }
         
    
    // insert record to database
try {
     $conn = $db->connectDB();
    $sql ="INSERT INTO products (product_name, product_description, category_id, 
    base_price, stocks, unit_price, total_price, created_at, updated_at) values (:p_product_name, 
    :p_product_description, :p_category_id, :p_base_price, :p_stocks, :p_unit_price, :p_total_price, NOW(), NOW())";
    $stmt = $conn->prepare($sql);

    $data = [':p_product_name'        => $productName,
         ':p_product_description' => $productDesc,
         ':p_category_id'         => $category,
         ':p_base_price'          => $basePrice,
         ':p_stocks'              => $numberOfStocks,
         ':p_unit_price'          => $unitPrice,
         ':p_total_price'         => $totalPrice ];
        
        if((!$stmt->execute($data)) ){
            $_SESSION["error"] = "Failed to add new record";
            header("location: ".BASE_URL."views/admin/product/add.php");
        }

        $lastID = $conn->lastInsertId();
        processImage($lastID);

        $error = processImage($lastID);
        if($error){
            $_SESSION["error"] = $error;
            header("location: ".BASE_URL."views/admin/product/add.php");
            exit;
         } else {$_SESSION["success"] = "Product added successfully";
        header("location: ".BASE_URL."views/admin/product/index.php");
        exit;};
        
             

} catch(PDOException $e) {
    echo "Connection Failed: ".$e->getMessage();
    $db=null;
}
       

    // process image
    // redirect back to product/index.php once succ

};

function processImage($id){
    global $db;


//retrieve $_FILES
    $path         = $_FILES['productImage']['tmp_name']; //actual file on tmp path
    $fileName     = $_FILES['productImage']['name']; //file name
    $fileType     = mime_content_type($path);

    if ($fileType!='image/jpeg' && $fileType!= 'image/png') {
        return "File is not a .jpeg or .png file";
    }

    // rename img
    $newFileName=md5(uniqid($fileName,true));
    $fileExt=pathinfo($fileName, PATHINFO_EXTENSION);
    $hashedName=$newFileName.'.'.$fileExt;


    // move img 2 proj folder
    $destination = ROOT_DIR.'public/uploads/products/'.$hashedName;
    if(!move_uploaded_file($path,$destination)){
        return "Transferring of image returns an error";
    }


    // ud img_url field
    $imageUrl='public/uploads/products/'.$hashedName;

    $conn=$db->connectDB();
    $sql="UPDATE products  SET image_url = :p_image_url WHERE id = :p_id;";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':p_image_url',$imageUrl);
    $stmt->bindParam(':p_id',$id);
    if($stmt->execute()){
        return "Failed to update the image URL field";
    }



    // return null if no error
    return null;
}