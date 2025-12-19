<?php
// backend/middleware/AuthMiddleware.php

require_once __DIR__ . '/../utils/JWT.php';
require_once __DIR__ . '/../utils/Response.php';

class AuthMiddleware {
    // Verify any authenticated user (admin or superadmin)
    public static function verifyAuth() {
        $headers = getallheaders();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        
        if (empty($authHeader)) {
            Response::error('No token, authorization denied', 401);
        }
        
        $token = str_replace('Bearer ', '', $authHeader);
        $decoded = JWT::decode($token);
        
        if (!$decoded) {
            Response::error('Token is not valid', 401);
        }
        
        return $decoded; // Return user data
    }
    
    // Verify super admin only
    public static function verifySuperAdmin() {
        $user = self::verifyAuth();
        
        if (!isset($user['role']) || $user['role'] !== 'superadmin') {
            Response::error('Access denied. Super admin only.', 403);
        }
        
        return $user;
    }
}
?>
