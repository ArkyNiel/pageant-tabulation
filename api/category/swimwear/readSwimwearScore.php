<?php
error_reporting(E_ALL);
//ports
$ports = array("http://localhost:5173", "http://localhost:4173");

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $ports)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, OPTIONS');  // Added OPTIONS here
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Request-With');
header("Access-Control-Allow-Credentials: true");

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(); 
}

include('functions.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'GET'){
    
    if(isset($_GET['score_id'])){
        // Get specific score by ID
        $SwimwearScore = getSwimwearScores($_GET);
    }elseif(isset($_GET['cand_id'])){
        // Get score by candidate ID
        $SwimwearScore = getSwimwearScoreByCandId($_GET);
    }else{
        // Get all scores
        $SwimwearScore = getAllSwimwearScores();
    }
    
    echo $talentScore;  // response

}else {
    $data = [
        'status' => 405,
        'message' => $requestMethod. ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>