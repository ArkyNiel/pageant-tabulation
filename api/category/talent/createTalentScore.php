<?php
// talent_score
error_reporting(0);
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Origin: http://localhost:4173");
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Request-With');
header("Access-Control-Allow-Credentials: true");

include('functions.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'POST'){
    $inputData = json_decode(file_get_contents("php://input"), true);
    
    if(empty($inputData)){
        // form submission
        $storeTalentScore = storeTalentScore($_POST);
    }else{
        // json submission
        $storeTalentScore = storeTalentScore($inputData);
    }
    
    echo $storeTalentScore;  // response

}else {
    $data = [
        'status' => 405,
        'message' => $requestMethod. ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>
