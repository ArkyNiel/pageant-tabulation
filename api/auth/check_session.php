<?php
require_once "../../config/session_config.php"; 

try {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (
        isset($_SESSION['user_id']) &&
        isset($_SESSION['username']) &&
        isset($_SESSION['role'])
    ) {
        echo json_encode([
            'status' => 200,
            'loggedIn' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role']
            ]
        ]);
        exit;
    }

    http_response_code(401);
    echo json_encode([
        'status' => 401,
        'loggedIn' => false,
        'message' => 'No active session found.'
    ]);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 500,
        'loggedIn' => false,
        'message' => 'Internal server error.',
        'error' => $e->getMessage()
    ]);
}
