<?php
class UserModel {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email, $password) {
        $query = "SELECT id, nama, email, password, role FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // Untuk tahap awal (password belum di-hash), pakai === 
            // Nanti ubah ke password_verify() jika sudah pakai hashing
            if($password === $user['password']) {
                return $user;
            }
        }
        return false;
    }
}
?>