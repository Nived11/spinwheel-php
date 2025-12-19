<?php
// backend/controllers/AdminController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Spin.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class AdminController {
    private $db;
    private $userModel;
    private $spinModel;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new User($this->db);
        $this->spinModel = new Spin($this->db);
    }
    
    // Get users with spin data
    private function getUsersWithSpins($users) {
        $result = [];
        foreach ($users as $user) {
            $spin = $this->spinModel->findByUid($user['uid']);
            $result[] = [
                '_id' => $user['id'],
                'uid' => $user['uid'],
                'name' => $user['name'],
                'phone' => $user['phone'],
                'dobOrAnniversary' => $user['dob_or_anniversary'],
                'createdAt' => $user['created_at'],
                'hasSpun' => $spin ? true : false,
                'prize' => $spin ? $spin['prize'] : null,
                'spinTime' => $spin ? $spin['spin_time'] : null,
                'redeemed' => $spin ? (bool)$spin['redeemed'] : false,
                'redemptionTime' => $spin ? $spin['redemption_time'] : null
            ];
        }
        return $result;
    }
    
    // Get all users (GET /api/admin/users)
    public function getAllUsers() {
        AuthMiddleware::verifyAuth();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        $result = $this->userModel->getAll($page, $limit, $search);
        $usersWithSpins = $this->getUsersWithSpins($result['users']);
        
        Response::json([
            'users' => $usersWithSpins,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $result['totalPages'],
                'totalUsers' => $result['total'],
                'limit' => $limit,
                'hasNextPage' => $page < $result['totalPages'],
                'hasPrevPage' => $page > 1
            ]
        ], 200);
    }
    
    // Get users for redemption (GET /api/admin/users/redeem)
    public function getUsersForRedemption() {
        // Same as getAllUsers (frontend decides what to show)
        $this->getAllUsers();
    }
    
    // Get stats (GET /api/admin/stats)
    public function getFullStats() {
        AuthMiddleware::verifyAuth();
        
        $totalUsers = $this->userModel->count();
        $totalSpins = $this->spinModel->count();
        $pendingSpins = $totalUsers - $totalSpins;
        $prizeStats = $this->spinModel->getPrizeStats();
        $redeemedCount = $this->spinModel->countRedeemed();
        $pendingRedemption = $totalSpins - $redeemedCount;
        
        // Format prize stats to match Node.js format
        $formattedPrizeStats = [];
        foreach ($prizeStats as $stat) {
            $formattedPrizeStats[] = [
                '_id' => $stat['prize'],
                'count' => (int)$stat['count']
            ];
        }
        
        Response::json([
            'totalUsers' => (int)$totalUsers,
            'totalSpins' => (int)$totalSpins,
            'pendingSpins' => (int)$pendingSpins,
            'prizeStats' => $formattedPrizeStats,
            'redeemedCount' => (int)$redeemedCount,
            'pendingRedemption' => (int)$pendingRedemption
        ], 200);
    }
    
    // Mark as redeemed (POST /api/admin/redeem)
    public function markRedeemed() {
        AuthMiddleware::verifyAuth();
        
        $data = json_decode(file_get_contents("php://input"));
        $uid = isset($data->uid) ? trim($data->uid) : '';
        
        if (empty($uid)) {
            Response::error('UID is required');
        }
        
        $spin = $this->spinModel->findByUid($uid);
        if (!$spin) {
            Response::error('Spin not found', 404);
        }
        
        if ($spin['redeemed']) {
            Response::error('Already redeemed');
        }
        
        if ($this->spinModel->markRedeemed($uid)) {
            $updatedSpin = $this->spinModel->findByUid($uid);
            Response::json([
                'msg' => 'Marked as redeemed',
                'spin' => $updatedSpin
            ], 200);
        } else {
            Response::error('Failed to mark as redeemed', 500);
        }
    }
    
    // Delete user (DELETE /api/admin/users/:uid)
    public function deleteUser($uid) {
        AuthMiddleware::verifySuperAdmin();
        
        if (empty($uid)) {
            Response::error('UID is required');
        }
        
        // Delete spin first (foreign key)
        $this->spinModel->delete($uid);
        
        // Delete user
        if ($this->userModel->delete($uid)) {
            Response::json(['msg' => 'User deleted successfully'], 200);
        } else {
            Response::error('Failed to delete user', 500);
        }
    }
}
?>
