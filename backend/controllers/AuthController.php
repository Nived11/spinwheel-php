<?php
// backend/controllers/AuthController.php

require_once __DIR__ . '/../utils/JWT.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../config/env.php';

class AuthController {
    // Admin login (POST /api/admin/login)
    public function login() {
        $data = json_decode(file_get_contents("php://input"));
        
        $username = isset($data->username) ? trim($data->username) : '';
        $password = isset($data->password) ? trim($data->password) : '';
        
        // Check Super Admin
        if ($username === env('SUPERADMIN_USERNAME') && 
            $password === env('SUPERADMIN_PASSWORD')) {
            
            $token = JWT::encode([
                'username' => $username,
                'role' => 'superadmin'
            ], 604800); // 7 days
            
            Response::json([
                'token' => $token,
                'role' => 'superadmin',
                'msg' => 'Login successful'
            ], 200);
        }
        
        // Check Regular Admin
        if ($username === env('ADMIN_USERNAME') && 
            $password === env('ADMIN_PASSWORD')) {
            
            $token = JWT::encode([
                'username' => $username,
                'role' => 'admin'
            ], 604800); // 7 days
            
            Response::json([
                'token' => $token,
                'role' => 'admin',
                'msg' => 'Login successful'
            ], 200);
        }
        
        // Invalid credentials
        Response::error('Invalid credentials', 401);
    }
}
?>
