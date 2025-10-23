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
require '../../../config/database.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];

if($requestMethod == 'GET'){
    $params = $_GET;

    $whereClause = "";
    if (isset($params['gender']) && !empty(trim($params['gender']))) {
        $gender = mysqli_real_escape_string($conn, $params['gender']);
        $whereClause = "WHERE c.cand_gender = '$gender'";
    }

    $query = "SELECT
                fs.cand_id,
                c.cand_number,
                c.cand_name,
                fs.total_score
              FROM final_score fs
              INNER JOIN contestants c ON fs.cand_id = c.cand_id
              $whereClause
              ORDER BY fs.total_score DESC";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) > 0){
            $res = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Total Scores Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            echo json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Total Scores Found',
            ];
            header("HTTP/1.0 404 Not Found");
            echo json_encode($data);
        }
    }else{
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error: ' . mysqli_error($conn),
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
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