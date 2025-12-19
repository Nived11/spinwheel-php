<?php
// backend/models/User.php

class User {
    private $conn;
    private $table = 'users';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create new user
    public function create($uid, $name, $phone, $dobOrAnniversary) {
        $query = "INSERT INTO " . $this->table . " 
                  (uid, name, phone, dob_or_anniversary) 
                  VALUES (:uid, :name, :phone, :dobOrAnniversary)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $uid);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':dobOrAnniversary', $dobOrAnniversary);
        
        return $stmt->execute();
    }
    
    // Find user by UID
    public function findByUid($uid) {
        $query = "SELECT * FROM " . $this->table . " WHERE uid = :uid LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    // Find user by phone
    public function findByPhone($phone) {
        $query = "SELECT * FROM " . $this->table . " WHERE phone = :phone LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':phone', $phone);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    // Get all users with pagination and search
    public function getAll($page = 1, $limit = 12, $search = '') {
        $offset = ($page - 1) * $limit;
        
        $whereClause = '';
        if (!empty($search)) {
            $whereClause = "WHERE uid LIKE :search OR name LIKE :search OR phone LIKE :search";
        }
        
        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM " . $this->table . " " . $whereClause;
        $countStmt = $this->conn->prepare($countQuery);
        if (!empty($search)) {
            $searchParam = "%$search%";
            $countStmt->bindParam(':search', $searchParam);
        }
        $countStmt->execute();
        $total = $countStmt->fetch()['total'];
        
        // Get users
        $query = "SELECT * FROM " . $this->table . " " . $whereClause . " 
                  ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        if (!empty($search)) {
            $searchParam = "%$search%";
            $stmt->bindParam(':search', $searchParam);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return [
            'users' => $stmt->fetchAll(),
            'total' => $total,
            'totalPages' => ceil($total / $limit)
        ];
    }
    
    // Count total users
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->query($query);
        return $stmt->fetch()['total'];
    }
    
    // Delete user
    public function delete($uid) {
        $query = "DELETE FROM " . $this->table . " WHERE uid = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $uid);
        return $stmt->execute();
    }
}
?>
