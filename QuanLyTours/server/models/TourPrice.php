<?php
class TourPrice {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả giá
    public function getAll() {
        $sql = "SELECT tsp.*, t.name AS tour_name
                FROM tour_seasonal_prices tsp
                JOIN tours t ON tsp.tour_id = t.id
                ORDER BY t.name, tsp.start_date";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Lấy giá theo tour
    public function getByTourId($tour_id) {
        $stmt = $this->conn->prepare("SELECT * FROM tour_seasonal_prices WHERE tour_id = :tid ORDER BY start_date ASC");
        $stmt->execute([':tid' => $tour_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tạo giá mới
    public function create($data) {
        $query = "INSERT INTO tour_seasonal_prices 
                (tour_id, name, start_date, end_date, price_adult, price_child) 
                VALUES (:tour_id, :name, :start_date, :end_date, :price_adult, :price_child)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':tour_id'    => $data['tour_id'],
            ':name'       => $data['name'],
            ':start_date' => $data['start_date'],
            ':end_date'   => $data['end_date'],
            ':price_adult'=> $data['price_adult'],
            ':price_child'=> $data['price_child']
        ]);
    }


    // Xóa giá
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM tour_seasonal_prices WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>
