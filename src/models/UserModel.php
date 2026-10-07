<?php
class UserModel {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }
public function register($nama, $email, $password, $alamat, $no_telp)
{
    $query = "INSERT INTO users
              (nama, email, password, alamat, no_telp)
              VALUES
              (:nama, :email, :password, :alamat, :no_telp)";

    $stmt = $this->conn->prepare($query);

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt->bindValue(':nama', $nama);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':password', $hashedPassword);
    $stmt->bindValue(':alamat', $alamat);
    $stmt->bindValue(':no_telp', $no_telp);

    return $stmt->execute();
}
}
?>