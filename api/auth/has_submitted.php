<?php
$ports = array("http://localhost:5173", "http://localhost:4173", "https://ic2-tabulation-frontend.vercel.app");

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $ports)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Request-With');
header("Access-Control-Allow-Credentials: true");

// options
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include('../../config/session_config.php');
require_once '../../config/database.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'PUT'){
    if (!isset($_SESSION['user_id'])) {
        $data = [
            'status' => 401,
            'message' => 'Unauthorized',
        ];
        header("HTTP/1.0 401 Unauthorized");
        echo json_encode($data);
        exit();
    }

    $user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);

    $query = "UPDATE users SET has_submitted = 1 WHERE id = '$user_id'";

    $result = mysqli_query($conn, $query);

    if($result){
        // Update session
        $_SESSION['has_submitted'] = 1;

        $data = [
            'status' => 200,
            'message' => 'Submission Status Updated Successfully',
            'has_submitted' => true
        ];
        header("HTTP/1.0 200 OK");
        echo json_encode($data);
    }else{
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }

}else {
    $data = [
        'status' => 405,
        'message' => $requestMethod. ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>
