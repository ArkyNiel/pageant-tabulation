<?php
error_reporting(0);
$ports = array("http://localhost:5173", "http://localhost:4173", "https://ic2-tabulation-frontend.vercel.app");

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $ports)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT, PATCH, OPTIONS'); 
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Request-With');
header("Access-Control-Allow-Credentials: true");

// options
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(); 
}

include('functions.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'PUT' || $requestMethod == 'PATCH'){
    $rawInput = file_get_contents("php://input");
    $inputData = json_decode($rawInput, true);

    if(empty($inputData)){
        // try to parse as form data
        parse_str($rawInput, $inputData);
        if(empty($inputData)){
            $data = [
                'status' => 400,
                'message' => 'No data provided',
            ];
            header("HTTP/1.0 400 Bad Request");
            echo json_encode($data);
            exit();
        }
    }

    // json or form submission
    $updateUser = updateUser($inputData);
    echo $updateUser;
} else {
    $data = [
        'status' => 405,
        'message' => $requestMethod. ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>