<?php

class DatabaseConnect
{
    // private $host = "localhost";
    // private $database = "ecommerce_cabinion";
    // private $dbusername = "cabinion";
    // private $dbpassword = "C4b1n!0N_2024";
    // private $conn = null;

    private $host = "localhost";
    private $database = "ecommerce";
    private $dbusername = "root";
    private $dbpassword = "";
    private $conn = null;

    //$this->$host;


    public function connectDB()
    {

        /*$host = "localhost";
        $database = "ecommerce";
        $dbusername = "root";
        $dbpassword = "";*/

        $dsn = "mysql: host=$this->host;dbname=$this->database;";
        try {
            $this->conn = new PDO($dsn, $this->dbusername, $this->dbpassword);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $this->conn;
        } catch (Exception $e) {
            echo "Connection Failed: " . $e->getMessage();
            return null;
        }
    }
}
