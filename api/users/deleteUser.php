<?php
error_reporting(0);
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Origin: http://localhost:4173");
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Request-With');
header("Access-Control-Allow-Credentials: true");

include('functions.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'DELETE'){
    $inputData = json_decode(file_get_contents("php://input"), true);
    
    error_log(print_r($inputData, true));
    var_dump($inputData);
    
    if(empty($inputData)){
        $deleteUser = deleteUser($_GET);
    }else{
        $deleteUser = deleteUser($inputData);
    }
    
    echo $deleteUser;
}else {
    $data = [
        'status' => 405,
        'message' => $requestMethod. ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>