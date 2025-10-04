<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
    http_response_code(200);
    exit();
}

try {
    require '../../config/database.php';
    
    if(!isset($conn) || !$conn){
        throw new Exception("Database connection failed");
    }
} catch(Exception $e){
    http_response_code(500);
    echo json_encode(['status' => 500, 'message' => 'Database connection error: ' . $e->getMessage()]);
    exit();
}

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
    
    // based on the role, set redirect path
    if(password_verify($input['password'], $user['password'])){
        $redirect = $user['role'] === 'judge' ? 'judge/dashboard.php' : 'admin/dashboard.php';
        
        http_response_code(200);
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