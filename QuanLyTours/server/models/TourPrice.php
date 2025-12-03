<?php
class TourPrice {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function getByTourId($tour_id) {
        $stmt = $this->conn->prepare("SELECT * FROM tour_seasonal_prices WHERE tour_id = :tid ORDER BY start_date ASC");
        $stmt->execute([':tid' => $tour_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO tour_seasonal_prices (tour_id, name, start_date, end_date, price_adult, price_child) 
                  VALUES (:tid, :name, :start, :end, :padult, :pchild)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM tour_seasonal_prices WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>