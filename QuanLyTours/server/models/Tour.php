<?php
class Tour {
    private $conn;

    public function __construct($db) { 
        $this->conn = $db; 
    }

    /* ============================
       TẠO TOUR MỚI
    ============================ */
    public function create($data, $itinerary) {
        try {
            /* CHECK TRÙNG CODE */
            $check = $this->conn->prepare("SELECT id FROM tours WHERE code = :code LIMIT 1");
            $check->execute([':code' => $data['code']]);
            if ($check->fetch()) {
                return "duplicate";
            }

            $this->conn->beginTransaction();

            /* INSERT TOUR */
            $query = "INSERT INTO tours 
                        (code, name, type, highlight, price_adult, price_child, image) 
                      VALUES 
                        (:code, :name, :type, :highlight, :p_adult, :p_child, :image)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':code' => $data['code'],
                ':name' => $data['name'],
                ':type' => $data['type'],
                ':highlight' => $data['highlight'],
                ':p_adult' => $data['price_adult'],
                ':p_child' => $data['price_child'],
                ':image' => $data['image']
            ]);

            $tour_id = $this->conn->lastInsertId();

            /* INSERT LỊCH TRÌNH */
            $query_it = "INSERT INTO tour_itineraries 
                (tour_id, day_number, title, description, meals, spot, accommodation) 
                VALUES 
                (:tid, :day, :title, :desc, :meals, :spot, :hotel)";

            $stmt_it = $this->conn->prepare($query_it);

            for ($i = 0; $i < count($itinerary['titles']); $i++) {
                $stmt_it->execute([
                    ':tid' => $tour_id,
                    ':day' => $i + 1,
                    ':title' => $itinerary['titles'][$i],
                    ':desc' => $itinerary['descs'][$i],
                    ':meals' => $itinerary['meals'][$i] ?? '',
                    ':spot' => $itinerary['spots'][$i] ?? '',
                    ':hotel' => $itinerary['hotels'][$i] ?? ''
                ]);
            }

            $this->conn->commit();
            return "success";

        } catch (Exception $e) {
            $this->conn->rollBack();
            return "error";
        }
    }

    /* ============================
       LẤY TẤT CẢ TOUR
    ============================ */
    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM tours ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================
       LẤY TOUR THEO ID
    ============================ */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM tours WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ============================
       LẤY LỊCH TRÌNH
    ============================ */
    public function getItinerary($tour_id) {
        $stmt = $this->conn->prepare("SELECT * FROM tour_itineraries WHERE tour_id = :tid ORDER BY day_number ASC");
        $stmt->execute([':tid' => $tour_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================
       UPDATE TOUR
    ============================ */
    public function update($id, $data, $itinerary) {
        try {
            $this->conn->beginTransaction();

            $imageSQL = !empty($data['image']) ? ", image = :image" : "";

            $query = "UPDATE tours SET 
                        code = :code,
                        name = :name,
                        type = :type,
                        highlight = :highlight,
                        price_adult = :p_adult,
                        price_child = :p_child
                        $imageSQL
                      WHERE id = :id";

            $stmt = $this->conn->prepare($query);

            $params = [
                ':code' => $data['code'],
                ':name' => $data['name'],
                ':type' => $data['type'],
                ':highlight' => $data['highlight'],
                ':p_adult' => $data['price_adult'],
                ':p_child' => $data['price_child'],
                ':id' => $id
            ];

            if (!empty($data['image'])) {
                $params[':image'] = $data['image'];
            }

            $stmt->execute($params);

            /* XÓA LỊCH TRÌNH CŨ */
            $del = $this->conn->prepare("DELETE FROM tour_itineraries WHERE tour_id = :id");
            $del->execute([':id' => $id]);

            /* INSERT LỊCH TRÌNH MỚI */
            $query_it = "INSERT INTO tour_itineraries 
                (tour_id, day_number, title, description, meals, accommodation, spot) 
                VALUES (:tid, :day, :title, :desc, :meals, :hotel, :spot)";
            $stmt_it = $this->conn->prepare($query_it);

            for ($i = 0; $i < count($itinerary['titles']); $i++) {
                $stmt_it->execute([
                    ':tid' => $id,
                    ':day' => $i + 1,
                    ':title' => $itinerary['titles'][$i],
                    ':desc' => $itinerary['descs'][$i],
                    ':meals' => $itinerary['meals'][$i] ?? '',
                    ':hotel' => $itinerary['hotels'][$i] ?? '',
                    ':spot' => $itinerary['spots'][$i] ?? ''
                ]);
            }

            $this->conn->commit();
            return "success";

        } catch (Exception $e) {
            $this->conn->rollBack();
            return "error";
        }
    }

    /* ============================
       XÓA TOUR
    ============================ */
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM tours WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /* ============================
       LẤY GALLERY
    ============================ */
    public function getGallery($tour_id) {
        $stmt = $this->conn->prepare("SELECT * FROM tour_gallery WHERE tour_id = :id");
        $stmt->execute([':id' => $tour_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
