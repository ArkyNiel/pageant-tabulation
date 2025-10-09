<?php
// check session
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && isset($_SESSION['username']) && isset($_SESSION['role'])) {
    echo json_encode([
        'status' => 200,
        'loggedIn' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role']
        ]
    ]);
} else {
    echo json_encode([
        'status' => 401,
        'loggedIn' => false
    ]);
}
?>