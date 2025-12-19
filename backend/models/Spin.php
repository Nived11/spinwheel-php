<?php
// backend/models/Spin.php

class Spin {
    private $conn;
    private $table = 'spins';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create new spin
    public function create($uid, $prize, $probability) {
        $query = "INSERT INTO " . $this->table . " 
                  (uid, prize, probability) 
                  VALUES (:uid, :prize, :probability)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $uid);
        $stmt->bindParam(':prize', $prize);
        $stmt->bindParam(':probability', $probability);
        
        return $stmt->execute();
    }
    
    // Find spin by UID
    public function findByUid($uid) {
        $query = "SELECT * FROM " . $this->table . " WHERE uid = :uid LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    // Count total spins
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->query($query);
        return $stmt->fetch()['total'];
    }
    
    // Get prize statistics
    public function getPrizeStats() {
        $query = "SELECT prize, COUNT(*) as count 
                  FROM " . $this->table . " 
                  GROUP BY prize 
                  ORDER BY count DESC, prize ASC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll();
    }
    
    // Count redeemed spins
    public function countRedeemed() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE redeemed = 1";
        $stmt = $this->conn->query($query);
        return $stmt->fetch()['total'];
    }
    
    // Mark as redeemed
    public function markRedeemed($uid) {
        $query = "UPDATE " . $this->table . " 
                  SET redeemed = 1, redemption_time = NOW() 
                  WHERE uid = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $uid);
        return $stmt->execute();
    }
    
    // Delete spin
    public function delete($uid) {
        $query = "DELETE FROM " . $this->table . " WHERE uid = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $uid);
        return $stmt->execute();
    }
}
?>
