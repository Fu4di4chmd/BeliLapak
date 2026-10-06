<?php
class Database {
    private $host = "localhost";
    private $db_name = "belilapak_db";
    private $username = "root"; // Sesuaikan dengan user database Anda
    private $password = "";     // Sesuaikan dengan password database Anda
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Set error mode ke exception agar error SQL mudah dilacak
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Koneksi Database Gagal: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>