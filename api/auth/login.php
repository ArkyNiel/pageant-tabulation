<?php
require '../../config/database.php';

header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);

if(empty($input['username']) || empty($input['password'])){
    http_response_code(422);
    echo json_encode(['status' => 422, 'message' => 'Username and password required']);
    exit();
}

$username = mysqli_real_escape_string($conn, $input['username']);
$query = "SELECT id, username, password, role FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if($result && mysqli_num_rows($result) === 1){
    $user = mysqli_fetch_assoc($result);
    
    // role based redirect path
    if(password_verify($input['password'], $user['password'])){
        $redirect = $user['role'] === 'judge' ? 'judge/dashboard.php' : 'admin/dashboard.php';
        
        echo json_encode([
            'status' => 200,
            'message' => 'Login successful',
            'data' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'redirect' => $redirect
            ]
        ]);
        exit();
    }
}

http_response_code(401);
echo json_encode(['status' => 401, 'message' => 'Invalid credentials']);
?>