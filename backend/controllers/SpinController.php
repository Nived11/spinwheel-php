<?php
// backend/controllers/SpinController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Spin.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../config/env.php';

class SpinController {
    private $db;
    private $userModel;
    private $spinModel;
    
    // ✅ REMOVED hardcoded prizes - will read from database instead
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new User($this->db);
        $this->spinModel = new Spin($this->db);
    }
    
    // Create UID (POST /api/create-uid)
    public function createUID() {
        $data = json_decode(file_get_contents("php://input"));
        
        $name = isset($data->name) ? trim($data->name) : '';
        $phone = isset($data->phone) ? trim($data->phone) : '';
        $dobOrAnniversary = isset($data->dobOrAnniversary) ? trim($data->dobOrAnniversary) : '';
        
        // Validations
        if (empty($name) || strlen($name) < 3) {
            Response::error('Name must be at least 3 characters');
        }
        
        if (empty($phone) || !preg_match('/^\d{10}$/', $phone)) {
            Response::error('Phone must be 10 digits');
        }
        
        if (empty($dobOrAnniversary) || strpos($dobOrAnniversary, ':') === false) {
            Response::error('Date is required');
        }
        
        list($selectedType, $selectedDate) = explode(':', $dobOrAnniversary, 2);
        if (empty(trim($selectedDate))) {
            Response::error('Date is required');
        }
        
        // Check if user already exists
        $existingUser = $this->userModel->findByPhone($phone);
        if ($existingUser) {
            $existingSpin = $this->spinModel->findByUid($existingUser['uid']);
            if ($existingSpin) {
                Response::error('You have already played');
            }
        }
        
        // Generate UID
        $uid = 'EMP-' . strtoupper(substr(uniqid(), -6));
        
        // Create user
        if ($this->userModel->create($uid, $name, $phone, $dobOrAnniversary)) {
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            $link = $frontendUrl . '/spin?uid=' . $uid;
            
            Response::json(['uid' => $uid, 'link' => $link], 200);
        } else {
            Response::error('Failed to create user', 500);
        }
    }
    
    // Validate UID (GET /api/validate-uid?uid=xxx)
    public function validateUID() {
        $uid = isset($_GET['uid']) ? trim($_GET['uid']) : '';
        
        if (empty($uid)) {
            Response::error('UID is required');
        }
        
        $user = $this->userModel->findByUid($uid);
        if (!$user) {
            Response::json(['valid' => false, 'msg' => 'Invalid UID'], 400);
        }
        
        $spin = $this->spinModel->findByUid($uid);
        if ($spin) {
            Response::json(['valid' => false, 'msg' => 'UID already used'], 400);
        }
        
        Response::json(['valid' => true], 200);
    }
    
    // ✅ NEW: Read prizes from database
    private function getPrizesFromDB() {
        try {
            $query = "SELECT prize, weight FROM prize_config WHERE weight > 0 ORDER BY weight DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $prizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($prizes)) {
                // Fallback if database is empty
                return [['prize' => 'Better Luck Next Time', 'weight' => 100]];
            }
            
            return $prizes;
        } catch (Exception $e) {
            // Fallback on error
            return [['prize' => 'Better Luck Next Time', 'weight' => 100]];
        }
    }
    
    // ✅ UPDATED: Weighted random prize selection - now uses database
    private function choosePrize() {
        $prizes = $this->getPrizesFromDB(); // Read from database
        
        $total = array_sum(array_column($prizes, 'weight'));
        $random = mt_rand(1, $total * 100) / 100;
        
        $sum = 0;
        foreach ($prizes as $prize) {
            $sum += $prize['weight'];
            if ($random <= $sum) {
                return $prize;
            }
        }
        
        return $prizes[0]; // Fallback
    }
    
    // Spin wheel (POST /api/spin)
    public function spinWheel() {
        $data = json_decode(file_get_contents("php://input"));
        $uid = isset($data->uid) ? trim($data->uid) : '';
        
        if (empty($uid)) {
            Response::error('UID is required');
        }
        
        // Validate user
        $user = $this->userModel->findByUid($uid);
        if (!$user) {
            Response::error('Invalid UID');
        }
        
        // Check if already spun
        $existingSpin = $this->spinModel->findByUid($uid);
        if ($existingSpin) {
            Response::error('Already spin');
        }
        
        // Check position for 100th user special prize
        $spinCount = $this->spinModel->count();
        $currentPosition = $spinCount + 1;
        
        if ($currentPosition === 100) {
            // Force "Free Full Mandi" for 100th user
            $result = ['prize' => 'Free Full Mandi', 'weight' => 0];
        } else {
            // Regular weighted random (reads from database)
            $result = $this->choosePrize();
        }
        
        // Save spin
        if ($this->spinModel->create($uid, $result['prize'], $result['weight'])) {
            Response::json(['prize' => $result['prize']], 200);
        } else {
            Response::error('Failed to save spin', 500);
        }
    }
    
    // Get user details (GET /api/user-details?uid=xxx)
    public function getUserDetails() {
        $uid = isset($_GET['uid']) ? trim($_GET['uid']) : '';
        
        if (empty($uid)) {
            Response::error('UID is required');
        }
        
        $user = $this->userModel->findByUid($uid);
        if (!$user) {
            Response::error('User not found', 404);
        }
        
        $spin = $this->spinModel->findByUid($uid);
        
        Response::json([
            'name' => $user['name'],
            'phone' => $user['phone'],
            'prize' => $spin ? $spin['prize'] : null,
            'uid' => $user['uid']
        ], 200);
    }
}
?>
