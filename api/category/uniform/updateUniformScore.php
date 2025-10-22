<?php
$ports = array("http://localhost:5173", "http://localhost:4173");

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

if($requestMethod == 'PUT'){
    $inputData = json_decode(file_get_contents("php://input"), true);
    
    if(empty($inputData)){
        $data = [
            'status' => 204,
            'message' => 'No Data Found',
        ];
        header("HTTP/1.0 204 No Content");
        echo json_encode($data);
    }else{
        $updateScore = updateUniformScore($inputData);
        echo $updateScore;
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
