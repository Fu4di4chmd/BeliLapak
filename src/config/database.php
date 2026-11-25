<?php
date_default_timezone_set('Asia/Jakarta');

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
            $this->conn->exec("SET time_zone = '+07:00'");
        } catch(PDOException $exception) {
            echo "Koneksi Database Gagal: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>