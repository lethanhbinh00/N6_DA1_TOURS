<?php

class CarBooking
{
    private $conn;

    public function __construct($db = null)
    {
        if ($db instanceof PDO) {
            $this->conn = $db;
        } else {
            global $conn;
            $this->conn = $conn;
        }
    }

    /* =======================
       LẤY DANH SÁCH + SEARCH
    ======================= */
    public function getAll($keyword = '')
    {
        $sql = "SELECT cb.*, 
               s.name AS service_name,
               s.price AS service_price,
               (cb.quantity * s.price) AS total_price
        FROM car_bookings cb
        LEFT JOIN services s ON cb.service_id = s.id
        WHERE 1";


        $params = [];

        if ($keyword != '') {
            $sql .= " AND (cb.customer_name LIKE ? OR cb.phone LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        $sql .= " ORDER BY cb.date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =======================
       DỊCH VỤ XE
    ======================= */
    public function getServices()
    {
        // Bảng tên trong DB là `services` (theo SQL dump), không phải `car_services`.
        $stmt = $this->conn->prepare(
            "SELECT id, name, price FROM services ORDER BY name"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =======================
       THÊM BOOKING
    ======================= */
    public function create($data)
    {
        if ($this->isDuplicate($data['service_id'], $data['date'])) {
            return "Dịch vụ đã được đặt vào ngày này!";
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO car_bookings
             (service_id, customer_name, phone, date, quantity, note)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        return $stmt->execute([
            $data['service_id'],
            $data['customer_name'],
            $data['phone'],
            $data['date'],
            $data['quantity'],
            $data['note'] ?? ''
        ]);
    }

    /* =======================
       KIỂM TRA TRÙNG
    ======================= */
    private function isDuplicate($service_id, $date)
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM car_bookings 
             WHERE service_id = ? AND date = ?"
        );
        $stmt->execute([$service_id, $date]);
        return $stmt->fetchColumn() > 0;
    }

    /* =======================
       FIND
    ======================= */
    public function find($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM car_bookings WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =======================
       UPDATE
    ======================= */
    public function update($data)
    {
        $stmt = $this->conn->prepare(
            "UPDATE car_bookings SET
             service_id=?, customer_name=?, phone=?, date=?, quantity=?, note=?
             WHERE id=?"
        );

        return $stmt->execute([
            $data['service_id'],
            $data['customer_name'],
            $data['phone'],
            $data['date'],
            $data['quantity'],
            $data['note'] ?? '',
            $data['id']
        ]);
    }

    // Backwards-compatible wrapper used by controller
    public function updateBooking($data)
    {
        return $this->update($data);
    }

    /* =======================
       DELETE
    ======================= */
    public function delete($id)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM car_bookings WHERE id=?"
        );
        return $stmt->execute([$id]);
    }

    // Backwards-compatible wrapper used by controller
    public function deleteBooking($id)
    {
        return $this->delete($id);
    }
}
