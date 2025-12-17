<?php

class Departure
{
    private $conn;
    private $lastError = null;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function getAll()
    {
        // Deprecated: use getAll with filters. Keep compatibility.
        return $this->getAllFiltered('', '');
    }

    // New: fetch departures with optional keyword and status filters
    public function getAllFiltered($keyword = '', $status = '')
    {
        // Detect whether `guide_id` column exists to avoid SQL errors when migration not applied
        $hasGuideCol = false;
        try {
            $stmtCheck = $this->conn->prepare("SHOW COLUMNS FROM `departures` LIKE 'guide_id'");
            $stmtCheck->execute();
            $hasGuideCol = ($stmtCheck->fetch() !== false);
        } catch (Exception $e) {
            $hasGuideCol = false;
        }

        if ($hasGuideCol) {
            $sql = "SELECT d.*, t.name AS tour_name, g.full_name AS guide_name
                    FROM departures d
                    JOIN tours t ON d.tour_id = t.id
                    LEFT JOIN guides g ON d.guide_id = g.id
                    WHERE 1";
        } else {
            $sql = "SELECT d.*, t.name AS tour_name, NULL AS guide_name
                    FROM departures d
                    JOIN tours t ON d.tour_id = t.id
                    WHERE 1";
        }

        $params = [];

        if ($keyword !== '') {
            if ($hasGuideCol) {
                $sql .= " AND (t.name LIKE ? OR g.full_name LIKE ?)";
                $params[] = "%$keyword%";
                $params[] = "%$keyword%";
            } else {
                $sql .= " AND (t.name LIKE ?)";
                $params[] = "%$keyword%";
            }
        }

        // status mapping based on start_date relative to today
        if ($status !== '') {
            $today = date('Y-m-d');
            if ($status == 'upcoming') {
                $sql .= " AND d.start_date > ?";
                $params[] = $today;
            } elseif ($status == 'running') {
                $sql .= " AND d.start_date = ?";
                $params[] = $today;
            } elseif ($status == 'completed') {
                $sql .= " AND d.start_date < ?";
                $params[] = $today;
            }
        }

        $sql .= " ORDER BY d.start_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTours()
    {
        $stmt = $this->conn->prepare(
            "SELECT id, name FROM tours"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGuides()
    {
        $stmt = $this->conn->prepare("SELECT id, full_name FROM guides ORDER BY full_name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        // check if guide_id column exists
        $hasGuideCol = false;
        try {
            $stmtCheck = $this->conn->prepare("SHOW COLUMNS FROM `departures` LIKE 'guide_id'");
            $stmtCheck->execute();
            $hasGuideCol = ($stmtCheck->fetch() !== false);
        } catch (Exception $e) {
            $hasGuideCol = false;
        }

        if ($hasGuideCol) {
            $stmt = $this->conn->prepare(
                "INSERT INTO departures (tour_id, start_date, seats, guide_id, note)
                 VALUES (?, ?, ?, ?, ?)"
            );
            return $stmt->execute([
                $data['tour_id'],
                $data['start_date'],
                $data['seats'],
                $data['guide_id'] ?? null,
                $data['note'] ?? null
            ]);
        } else {
            $stmt = $this->conn->prepare(
                "INSERT INTO departures (tour_id, start_date, seats)
                 VALUES (?, ?, ?)"
            );
            return $stmt->execute([
                $data['tour_id'],
                $data['start_date'],
                $data['seats']
            ]);
        }
    }

    public function find($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM departures WHERE id=?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy lịch trình chi tiết: itinerary của tour
    public function getItinerary($tour_id)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM tour_itineraries WHERE tour_id = ? ORDER BY day_number"
        );
        $stmt->execute([$tour_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách khách (bookings) theo tour và ngày khởi hành
    public function getPassengers($tour_id, $date)
    {
        $stmt = $this->conn->prepare(
            "SELECT b.id, b.booking_code, b.customer_name, b.customer_phone, b.customer_email, b.customer_id_card,
                    b.pickup_location, b.adults, b.children, b.total_price, b.status, b.note, b.travel_date, b.return_date
                 FROM bookings b
                 WHERE b.tour_id = ? AND DATE(b.travel_date) = DATE(?)"
        );
        $stmt->execute([$tour_id, $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy bản ghi điểm danh cho một departure (booking_id => [status,note])
    public function getAttendance($departure_id)
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT booking_id, status, note FROM departure_attendance WHERE departure_id = ?"
            );
            $stmt->execute([$departure_id]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $out = [];
            foreach ($rows as $r) {
                $out[$r['booking_id']] = $r;
            }
            return $out;
        } catch (Exception $e) {
            return [];
        }
    }


    public function saveReport($data)
    {
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO guide_reports (departure_id, guide_id, report_date, content, photos)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $res = $stmt->execute([
                $data['departure_id'],
                $data['guide_id'] ?? null,
                $data['report_date'] ?? date('Y-m-d'),
                $data['content'] ?? '',
                $data['photos'] ?? ''
            ]);
            if (!$res) {
                $err = $stmt->errorInfo();
                $this->lastError = implode(' | ', $err);
            }
            return $res;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }


    public function saveAttendance($departure_id, $attendance)
    {
        $this->conn->beginTransaction();
        try {
            $stmtDel = $this->conn->prepare("DELETE FROM departure_attendance WHERE departure_id = ?");
            $stmtDel->execute([$departure_id]);

            $stmt = $this->conn->prepare(
                "INSERT INTO departure_attendance (departure_id, booking_id, status, note) VALUES (?, ?, ?, ?)"
            );
            foreach ($attendance as $booking_id => $item) {
                $status = $item['status'] ?? 'absent';
                $note = $item['note'] ?? '';
                $res = $stmt->execute([$departure_id, $booking_id, $status, $note]);
                if (!$res) {
                    $err = $stmt->errorInfo();
                    $this->lastError = implode(' | ', $err);
                    throw new Exception('Insert attendance failed');
                }
            }

            $this->conn->commit();


            try {
                $stmtDep = $this->conn->prepare("SELECT tour_id, start_date FROM departures WHERE id = ? LIMIT 1");
                $stmtDep->execute([$departure_id]);
                $dep = $stmtDep->fetch(PDO::FETCH_ASSOC);
                if ($dep) {
                    $tourId = $dep['tour_id'];
                    $start = $dep['start_date'];
                    $stmtP = $this->conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE tour_id = ? AND DATE(travel_date) = DATE(?)");
                    $stmtP->execute([$tourId, $start]);
                    $total = (int)($stmtP->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

                    $stmtA = $this->conn->prepare("SELECT status, COUNT(*) as cnt FROM departure_attendance WHERE departure_id = ? GROUP BY status");
                    $stmtA->execute([$departure_id]);
                    $rows = $stmtA->fetchAll(PDO::FETCH_ASSOC);
                    $present = 0;
                    $late = 0;
                    foreach ($rows as $r) {
                        $s = strtolower(trim($r['status'] ?? ''));
                        if ($s === 'present') $present += (int)$r['cnt'];
                        elseif ($s === 'late') $late += (int)$r['cnt'];
                    }
                    $complete = ($total > 0 && ($present + $late) === $total) ? 1 : 0;


                    try {
                        $stmtCheck = $this->conn->prepare("SHOW COLUMNS FROM `departures` LIKE 'attendance_complete'");
                        $stmtCheck->execute();
                        if ($stmtCheck->fetch() !== false) {
                            $stmtUpd = $this->conn->prepare("UPDATE departures SET attendance_complete = ? WHERE id = ?");
                            $stmtUpd->execute([$complete, $departure_id]);
                        }
                    } catch (Exception $e) {
                    }
                }
            } catch (Exception $e) {
            }

            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            if (empty($this->lastError)) $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    public function update($data)
    {
        $hasGuideCol = false;
        try {
            $stmtCheck = $this->conn->prepare("SHOW COLUMNS FROM `departures` LIKE 'guide_id'");
            $stmtCheck->execute();
            $hasGuideCol = ($stmtCheck->fetch() !== false);
        } catch (Exception $e) {
            $hasGuideCol = false;
        }

        if ($hasGuideCol) {
            $stmt = $this->conn->prepare(
                "UPDATE departures SET tour_id=?, start_date=?, seats=?, guide_id=?, note=? WHERE id=?"
            );
            return $stmt->execute([
                $data['tour_id'],
                $data['start_date'],
                $data['seats'],
                $data['guide_id'] ?? null,
                $data['note'] ?? null,
                $data['id']
            ]);
        } else {
            $stmt = $this->conn->prepare(
                "UPDATE departures SET tour_id=?, start_date=?, seats=? WHERE id=?"
            );
            return $stmt->execute([
                $data['tour_id'],
                $data['start_date'],
                $data['seats'],
                $data['id']
            ]);
        }
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM departures WHERE id=?"
        );
        return $stmt->execute([$id]);
    }
}
