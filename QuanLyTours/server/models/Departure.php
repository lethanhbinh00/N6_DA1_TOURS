<?php

class Departure
{
    private $conn;
    protected $table = "tour_departures";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $sql = "SELECT d.*, t.name AS tour_name
                FROM tour_departures d
                JOIN tours t ON d.tour_id = t.id
                ORDER BY d.start_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTours()
    {
        $sql = "SELECT id, name FROM tours ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $sql = "SELECT * FROM tour_departures WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO tour_departures (tour_id, start_date, seats)
                VALUES (:tour_id, :start_date, :seats)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'tour_id' => $data['tour_id'],
            'start_date' => $data['start_date'],
            'seats' => $data['seats']
        ]);
    }

    public function updateDeparture($data)
    {
        $sql = "UPDATE tour_departures
                SET tour_id = :tour_id, start_date = :start_date, seats = :seats
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'tour_id' => $data['tour_id'],
            'start_date' => $data['start_date'],
            'seats' => $data['seats'],
            'id' => $data['id']
        ]);
    }

    public function deleteDeparture($id)
    {
        $sql = "DELETE FROM tour_departures WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
