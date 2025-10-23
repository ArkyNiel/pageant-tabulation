<?php
$ports = array("http://localhost:5173", "http://localhost:4173", "https://ic2-tabulation-frontend.vercel.app");

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $ports)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Request-With');
header("Access-Control-Allow-Credentials: true");

// options
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include('../../../config/session_config.php');
include('functions.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'GET'){
    $judgeParams = $_GET;

<<<<<<< HEAD
    $getJudgeScores = getFormalwearScoresByJudge($judgeParams);
=======
    $judge_id = isset($judgeParams['judge_id']) ? trim($judgeParams['judge_id']) : '';

    if(!empty($judge_id)){
        $getJudgeScores = getFormalwearScoresByJudge($judgeParams);
    }else{
        $getJudgeScores = getAllFormalwearScores($judgeParams);
    }
>>>>>>> 8f726a59764978dfdb8cb3213280f8be72eafb30

    echo $getJudgeScores;

}else {
    $data = [
        'status' => 405,
        'message' => $requestMethod. ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>
