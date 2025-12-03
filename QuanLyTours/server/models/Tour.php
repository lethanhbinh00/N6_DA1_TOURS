<?php
class Tour {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function create($data, $itinerary) {
        try {
            $this->conn->beginTransaction(); 
            $query = "INSERT INTO tours (code, name, type, highlight, price_adult, price_child, image) 
                      VALUES (:code, :name, :type, :highlight, :p_adult, :p_child, :image)";
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
            $query_it = "INSERT INTO tour_itineraries (tour_id, day_number, title, description, meals) 
                         VALUES (:tid, :day, :title, :desc, :meals)";
            $stmt_it = $this->conn->prepare($query_it);
            for ($i = 0; $i < count($itinerary['titles']); $i++) {
                $stmt_it->execute([
                    ':tid' => $tour_id,
                    ':day' => $i + 1,
                    ':title' => $itinerary['titles'][$i],
                    ':desc' => $itinerary['descs'][$i],
                    ':meals' => $itinerary['meals'][$i] ?? ''
                ]);
            }

            $this->conn->commit(); 
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            echo "<div style='background:red; color:white; padding:20px;'>";
            echo "<h3>LỖI SQL:</h3>";
            echo $e->getMessage(); 
            echo "</div>";
            die(); 
            return false;
        }
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM tours ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM tours WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getItinerary($tour_id) {
        $query = "SELECT * FROM tour_itineraries WHERE tour_id = :tid ORDER BY day_number ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tid', $tour_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $data, $itinerary) {
        try {
            $this->conn->beginTransaction();
            $imageSQL = "";
            if (!empty($data['image'])) {
                $imageSQL = ", image = :image";
            }

            $query = "UPDATE tours SET 
                      code = :code, name = :name, type = :type, 
                      highlight = :highlight, price_adult = :p_adult, price_child = :p_child 
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

            $stmtDel = $this->conn->prepare("DELETE FROM tour_itineraries WHERE tour_id = :id");
            $stmtDel->execute([':id' => $id]);

            $query_it = "INSERT INTO tour_itineraries (tour_id, day_number, title, description, meals, accommodation, spot) 
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
        } catch (PDOException $e) {
            $this->conn->rollBack();
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) return "duplicate";
            echo "<div style='background:red; color:white; padding:20px;'>";
            echo "<h3>LỖI SQL KHI UPDATE:</h3>";
            echo $e->getMessage(); 
            echo "</div>";
            die(); 
            return false;
        } catch (Exception $e) {
            $this->conn->rollBack();
            echo "<div style='background:red; color:white; padding:20px;'>";
            echo "<h3>LỖI:</h3>";
            echo $e->getMessage(); 
            echo "</div>";
            die();
            return false;
        }
    }

    public function delete($id) {
        $query = "DELETE FROM tours WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) return true;
        return false;
    }

    public function getGallery($tour_id) {
        $stmt = $this->conn->prepare("SELECT * FROM tour_gallery WHERE tour_id = :id");
        $stmt->execute([':id' => $tour_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>