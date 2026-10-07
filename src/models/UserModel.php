<?php

class UserModel
{
    private $conn;
    private $table_name = "users"; // Pastikan nama tabel Anda di database adalah 'users'

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // =========================
    // CARI USER BERDASARKAN EMAIL
    // =========================
    public function findByEmail($email)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =========================
    // REGISTRASI USER BARU
    // =========================
    public function register($nama, $email, $password, $alamat, $no_telp)
    {
        // Secara default, pendaftar baru diberi role 'user'
        $query = "INSERT INTO " . $this->table_name . " 
                  (nama, email, password, alamat, no_telp, role) 
                  VALUES (:nama, :email, :password, :alamat, :no_telp, 'user')";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash password menggunakan BCRYPT untuk keamanan standar industri
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Bind data ke query
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':alamat', $alamat);
        $stmt->bindParam(':no_telp', $no_telp);
        
        // Eksekusi query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    // =========================
    // LOGIN USER
    // =========================
    public function login($email, $password)
    {
        // Cari user berdasarkan email
        $user = $this->findByEmail($email);
        
        // Jika user ditemukan di database
        if ($user) {
            // Verifikasi kecocokan password yang diketik dengan password acak (hash) di database
            if (password_verify($password, $user['password'])) {
                // Jika cocok, kembalikan data user
                return $user;
            }
        }
        
        // Jika email tidak ada atau password salah
        return false;
    }
}
?>