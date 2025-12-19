<?php
// backend/routes/api.php

require_once __DIR__ . '/../controllers/SpinController.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AdminController.php';

function handleRequest($method, $uri) {
    // Remove leading/trailing slashes and /api prefix
    $uri = trim($uri, '/');
    $uri = preg_replace('#^api/#', '', $uri);
    $parts = explode('/', $uri);
    
    // Spin/User routes
    if ($uri === 'create-uid' && $method === 'POST') {
        $controller = new SpinController();
        $controller->createUID();
    }
    elseif ($uri === 'validate-uid' && $method === 'GET') {
        $controller = new SpinController();
        $controller->validateUID();
    }
    elseif ($uri === 'spin' && $method === 'POST') {
        $controller = new SpinController();
        $controller->spinWheel();
    }
    elseif ($uri === 'user-details' && $method === 'GET') {
        $controller = new SpinController();
        $controller->getUserDetails();
    }
    
    // Admin routes
    elseif ($uri === 'admin/login' && $method === 'POST') {
        $controller = new AuthController();
        $controller->login();
    }
    elseif ($uri === 'admin/stats' && $method === 'GET') {
        $controller = new AdminController();
        $controller->getFullStats();
    }
    elseif ($uri === 'admin/users' && $method === 'GET') {
        $controller = new AdminController();
        $controller->getAllUsers();
    }
    elseif ($uri === 'admin/users/redeem' && $method === 'GET') {
        $controller = new AdminController();
        $controller->getUsersForRedemption();
    }
    elseif ($uri === 'admin/redeem' && $method === 'POST') {
        $controller = new AdminController();
        $controller->markRedeemed();
    }
    elseif (preg_match('#^admin/users/([^/]+)$#', $uri, $matches) && $method === 'DELETE') {
        $controller = new AdminController();
        $controller->deleteUser($matches[1]);
    }
    
    // 404
    else {
        http_response_code(404);
        echo json_encode(['msg' => 'Endpoint not found']);
    }
}
?>
