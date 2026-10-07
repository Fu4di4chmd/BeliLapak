<?php

class UserModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findByEmail($email)
    {
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    public function login($email, $password)
    {
        $user = $this->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }
}