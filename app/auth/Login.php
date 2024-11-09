<?php 

$username = $_POST["username"];
$password = $_POST["password"];

session_start();


if($_SERVER["REQUEST_METHOD"] == "POST"){

    $host = "localhost";
    $database = "ecommerce";
    $dbusername = "root";
    $dbpassword = "";

    $dsn = "mysql: host=$host;dbname=$database;";
        try { 

            $conn = new PDO($dsn, $dbusername, $dbpassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            $stmt = $conn->prepare('SELECT * FROM `users` WHERE username = :p_username');
            $stmt->bindParam(':p_username',$username);
            $stmt->execute();
            $users = $stmt->fetchAll();

            // echo "Logged in";
            
            if($users){
                if(password_verify($password,hash: $users[0]["password"])){

                    $_SESSION = [];
                    session_regenerate_id(true);
                    $_SESSION["user_id"] = $users[0]["id"];
                    $_SESSION["username"] = $users[0]["username"];
                    $_SESSION["fullname"] = $users[0]["fullname"];
                    $_SESSION["is_admin"] = $users[0]["is_admin"];

                    header("location: /index.php");
                } else {
                   $_SESSION["error"] = "Wrong Password"; 
                   header("location: /login.php");
                }
            } else {
                echo "User Not Found";
               $_SESSION["error"] = "User Not Found";
               header("location: /login.php");
                
            }

        } catch (Exception $e){
            echo "Connection Failed: " . $e->getMessage();
        }
}

?>