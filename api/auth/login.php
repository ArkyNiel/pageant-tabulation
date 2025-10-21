<?php
require_once '../../config/session_config.php';
require_once '../../config/database.php';


if(!isset($conn) || !$conn){
    http_response_code(500);
    echo json_encode(['status' => 500, 'message' => 'Database connection failed']);
    exit();
}

$input = json_decode(file_get_contents("php://input"), true);

if (empty($input['username']) || empty($input['password'])) {
    http_response_code(422);
    echo json_encode(['status' => 422, 'message' => 'Username and password required']);
    exit();
}

$username = mysqli_real_escape_string($conn, $input['username']);
$query = "SELECT id, username, password, role, has_agreed, has_submitted FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);

    if (password_verify($input['password'], $user['password'])) {
        // Store user data in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['has_agreed'] = $user['has_agreed'];
        $_SESSION['has_submitted'] = $user['has_submitted'];

        $redirect = $user['role'] === 'judge' ? 'judge/dashboard.php' : 'admin/dashboard.php';

        echo json_encode([
            'status' => 200,
            'loggedIn' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'has_agreed' => (bool)$user['has_agreed'],
                'has_submitted' => (bool)$user['has_submitted']
            ],
            'redirect' => $redirect
        ]);
        exit();
    }
}

http_response_code(401);
echo json_encode(['status' => 401, 'message' => 'Invalid credentials']);
