<?php
// production score
//ports
$ports = array("http://localhost:5173", "http://localhost:4173");

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $ports)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
header("Access-Control-Allow-Credentials: true");

// Start session to check authentication
session_start();

if (!isset($_SESSION['user_id'])) {
    $data = [
        'status' => 401,
        'message' => 'Unauthorized. Please log in first.',
    ];
    header("HTTP/1.0 401 Unauthorized");
    echo json_encode($data);
    exit();
}

include('functions.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'POST'){
    $inputData = json_decode(file_get_contents("php://input"), true);
    
    if(empty($inputData)){
        // form submission
        $storeProductionScore = storeProductionScore($_POST);
    }else{
        // json submission
        $storeProductionScore = storeProductionScore($inputData);
    }
    
    echo $storeProductionScore;  // response

}else {
    $data = [
        'status' => 405,
        'message' => $requestMethod. ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>
