<?php
error_reporting(0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, PATCH');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Request-With');

include('function.php');

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