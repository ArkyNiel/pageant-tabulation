<?php
// talent_score_read.php
error_reporting(0);
// front-end ports
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Origin: http://localhost:4173");
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Request-With');
header("Access-Control-Allow-Credentials: true");

include('functions.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'GET'){
    
    if(isset($_GET['score_id'])){
        // Get specific score by ID
        $productionScore = getProductionScore($_GET);
    }elseif(isset($_GET['cand_id'])){
        // Get score by candidate ID
        $productionScore = getProductionScoreByCandId($_GET);
    }else{
        // Get all scores
        $productionScore = getAllProductionScores();
    }
    
    echo $productionScore;  // response

}else {
    $data = [
        'status' => 405,
        'message' => $requestMethod. ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>