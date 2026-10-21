<?php

class UserModel
{
    private $conn;
    private $table_name = "users";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // =========================
    // CARI USER BERDASARKAN EMAIL
    // =========================
    public function findByEmail($email)
    {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE email = :email
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =========================
    // REGISTRASI PELANGGAN
    // =========================
    public function register($nama, $email, $password, $alamat, $no_telp)
    {
        $query = "INSERT INTO " . $this->table_name . "
                  (nama, email, password, role, alamat, no_telp)
                  VALUES
                  (:nama, :email, :password, :role, :alamat, :no_telp)";

        $stmt = $this->conn->prepare($query);

        $hashed_password = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        // Register umum hanya untuk pelanggan
        $role = 'pelanggan';

        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':alamat', $alamat);
        $stmt->bindParam(':no_telp', $no_telp);

        return $stmt->execute();
    }

    // =========================
    // LOGIN
    // =========================
    public function login($email, $password)
    {
        $user = $this->findByEmail($email);

        if (
            $user &&
            password_verify($password, $user['password'])
        ) {
            return $user;
        }

        return false;
    }

    // =========================
    // AMBIL USER BERDASARKAN ID
    // =========================
    public function getUserById($id)
    {
        $query = "SELECT id, nama, email, alamat, no_telp, role
                  FROM " . $this->table_name . "
                  WHERE id = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(
            ':id',
            $id,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>