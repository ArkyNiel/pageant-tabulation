<?php
error_reporting(0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Request-With');

include('function.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'POST'){
    $inputData = json_decode(file_get_contents("php://input"), true);
    
    if(empty($inputData)){
        // Use $_POST for form-data submissions
        $storeUsers = storeUsers($_POST);
    }else{
        // Use $inputData for JSON submissions
        $storeUsers = storeUsers($inputData);
    }
    
    echo $storeUsers;  // Output the response

}else {
    $data = [
        'status' => 405,
        'message' => $requestMethod. ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>