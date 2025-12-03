<?php

class CarBooking
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        try {
            $sql = "SELECT cb.*, s.name AS service_name
                    FROM car_bookings cb
                    LEFT JOIN services s ON cb.service_id = s.id
                    ORDER BY cb.date DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Table `services` (or `car_bookings`) might be missing — return empty dataset to avoid fatal error
            return [];
        }
    }

    public function getServices()
    {
        try {
            $sql = "SELECT id, name, price FROM services ORDER BY name ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function create($data)
    {
        try {
            $sql = "INSERT INTO car_bookings (service_id, customer_name, phone, date, quantity, note)
                    VALUES (:service_id, :customer_name, :phone, :date, :quantity, :note)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                'service_id' => $data['service_id'],
                'customer_name' => $data['customer_name'],
                'phone' => $data['phone'],
                'date' => $data['date'],
                'quantity' => $data['quantity'],
                'note' => $data['note'] ?? ''
            ]);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function find($id)
    {
        $sql = "SELECT * FROM car_bookings WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateBooking($data)
    {
        try {
            $sql = "UPDATE car_bookings
                    SET service_id = :service_id,
                        customer_name = :customer_name,
                        phone = :phone,
                        date = :date,
                        quantity = :quantity,
                        note = :note
                    WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                'service_id' => $data['service_id'],
                'customer_name' => $data['customer_name'],
                'phone' => $data['phone'],
                'date' => $data['date'],
                'quantity' => $data['quantity'],
                'note' => $data['note'] ?? '',
                'id' => $data['id']
            ]);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function deleteBooking($id)
    {
        $sql = "DELETE FROM car_bookings WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
