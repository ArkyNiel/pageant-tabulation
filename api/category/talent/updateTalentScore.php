<?php
error_reporting(0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Request-With');
header("Access-Control-Allow-Credentials: true");

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
        $updateScore = updateTalentScore($inputData);
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
