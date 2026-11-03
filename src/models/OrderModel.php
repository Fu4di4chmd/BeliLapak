
<?php

class OrderModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // =================================
    // MENYIMPAN PESANAN
    // =================================
    public function createOrder(
        $userId,
        $tanggal,
        $total,
        $ongkir,
        $kurir,
        $metodePembayaran,
        $namaPenerima,
        $noTelpPenerima,
        $alamatPengiriman,
        $details
    ) {
        try {
            $this->conn->beginTransaction();

            $query = "INSERT INTO orders
                      (
                        user_id,
                        tanggal_pesanan,
                        total_belanja,
                        ongkos_kirim,
                        kurir,
                        status_pembayaran,
                        status_pesanan,
                        nama_penerima,
                        no_telp_penerima,
                        alamat_pengiriman,
                        metode_pembayaran
                      )
                      VALUES
                      (
                        :user_id,
                        :tanggal,
                        :total,
                        :ongkir,
                        :kurir,
                        'belum dibayar',
                        'menunggu konfirmasi',
                        :nama_penerima,
                        :no_telp_penerima,
                        :alamat_pengiriman,
                        :metode_pembayaran
                      )";

            $stmt = $this->conn->prepare($query);

            $stmt->execute([
                ':user_id' => $userId,
                ':tanggal' => $tanggal,
                ':total' => $total,
                ':ongkir' => $ongkir,
                ':kurir' => $kurir,
                ':nama_penerima' => $namaPenerima,
                ':no_telp_penerima' => $noTelpPenerima,
                ':alamat_pengiriman' => $alamatPengiriman,
                ':metode_pembayaran' => $metodePembayaran
            ]);

            $orderId = $this->conn->lastInsertId();

            $detailQuery = "INSERT INTO order_details
                            (order_id, product_id, jumlah, harga_satuan)
                            VALUES
                            (:order_id, :product_id, :jumlah, :harga)";

            $detailStmt = $this->conn->prepare($detailQuery);

            foreach ($details as $item) {
                $detailStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['product_id'],
                    ':jumlah' => $item['jumlah'],
                    ':harga' => $item['harga']
                ]);
            }

            $this->conn->commit();

            return $orderId;

        } catch (Exception $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }
    }

    // =================================
    // SEMUA PESANAN ADMIN
    // =================================
    public function getAllOrders()
    {
        $query = "SELECT orders.*, users.nama, users.email
                  FROM orders
                  INNER JOIN users ON orders.user_id = users.id
                  ORDER BY orders.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =================================
    // PESANAN BERDASARKAN ID
    // =================================
    public function getOrderById($id)
    {
        $query = "SELECT orders.*, users.nama, users.email
                  FROM orders
                  INNER JOIN users ON orders.user_id = users.id
                  WHERE orders.id = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =================================
    // PESANAN MILIK PEMBELI
    // =================================
    public function getOrdersByUser($userId)
    {
        $query = "SELECT *
                  FROM orders
                  WHERE user_id = :user_id
                  ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =================================
    // DETAIL PRODUK PESANAN
    // =================================
    public function getOrderDetails($orderId)
    {
        $query = "SELECT
                    order_details.*,
                    products.nama_produk,
                    products.gambar
                  FROM order_details
                  INNER JOIN products
                    ON order_details.product_id = products.id
                  WHERE order_details.order_id = :order_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =================================
    // UPDATE STATUS PESANAN
    // =================================
    public function updateStatusPesanan($id, $status)
    {
        $query = "UPDATE orders
                  SET status_pesanan = :status
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);

        return $stmt->rowCount() >= 0;
    }

    // =================================
    // UPDATE STATUS PEMBAYARAN
    // =================================
    public function updateStatusPembayaran($id, $status)
    {
        $query = "UPDATE orders
                  SET status_pembayaran = :status
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);

        return $stmt->rowCount() >= 0;
    }
}
?>
