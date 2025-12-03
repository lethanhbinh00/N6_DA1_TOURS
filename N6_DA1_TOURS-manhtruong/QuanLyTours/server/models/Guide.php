<?php
class Guide {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM guides ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        try {
            $query = "INSERT INTO guides (full_name, gender, dob, phone, email, address, license_number, languages, experience_years, image) 
                      VALUES (:name, :gender, :dob, :phone, :email, :addr, :license, :lang, :exp, :img)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':name'    => $data['full_name'],
                ':gender'  => $data['gender'],
                ':dob'     => $data['dob'],
                ':phone'   => $data['phone'],
                ':email'   => $data['email'],
                ':addr'    => $data['address'],
                ':license' => $data['license_number'],
                ':lang'    => $data['languages'],
                ':exp'     => $data['experience_years'],
                ':img'     => $data['image']
            ]);
            return "success";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM guides WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>