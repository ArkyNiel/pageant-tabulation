<?php
session_start();
require '../../../config/database.php';

// def
function error422($message){
    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}

// store production score
function storeProductionScore($scoreInput){
    global $conn;

    // Get judge_id from session
    $judge_id = isset($_SESSION['user_id']) ? mysqli_real_escape_string($conn, $_SESSION['user_id']) : '';

    if(empty($judge_id)){
        error422('Judge not logged in');
        return;
    }

    // Extract and escape input data
    $cand_id = isset($scoreInput['cand_id']) ? mysqli_real_escape_string($conn, $scoreInput['cand_id']) : '';
    $choreography = isset($scoreInput['choreography']) ? mysqli_real_escape_string($conn, $scoreInput['choreography']) : '';
    $projection = isset($scoreInput['projection']) ? mysqli_real_escape_string($conn, $scoreInput['projection']) : '';
    $audience_impact = isset($scoreInput['audience_impact']) ? mysqli_real_escape_string($conn, $scoreInput['audience_impact']) : '';

    // Improved validation - check if value is set and not empty string
    if($cand_id === '' || $cand_id === null){
        error422('Enter candidate ID');
        return;
    }
    if($choreography === '' || $choreography === null){
        error422('Enter choreography score');
        return;
    }
    if($projection === '' || $projection === null){
        error422('Enter projection score');
        return;
    }
    if($audience_impact === '' || $audience_impact === null){
        error422('Enter audience impact score');
        return;
    }

    // Verify candidate exists
    $checkCandQuery = "SELECT cand_id FROM contestants WHERE cand_id = '$cand_id'";
    $checkCandResult = mysqli_query($conn, $checkCandQuery);

    if(!$checkCandResult || mysqli_num_rows($checkCandResult) === 0){
        error422('Candidate ID does not exist');
        return;
    }

    // Check if judge has already submitted a score for this candidate
    $checkDuplicateQuery = "SELECT score_id FROM production_score WHERE cand_id = '$cand_id' AND judge_id = '$judge_id' LIMIT 1";
    $checkDuplicateResult = mysqli_query($conn, $checkDuplicateQuery);

    if($checkDuplicateResult && mysqli_num_rows($checkDuplicateResult) > 0){
        error422('You have already submitted a score for this candidate');
        return;
    }

    // Generate unique score_id
    do {
        $score_id = rand(100000, 999999);
        $checkQuery = "SELECT score_id FROM production_score WHERE score_id = '$score_id'";
        $checkResult = mysqli_query($conn, $checkQuery);
    } while ($checkResult && mysqli_num_rows($checkResult) > 0);

    // Insert without total_score - let the database trigger/default handle it
    $query = "INSERT INTO production_score (score_id, cand_id, judge_id, choreography, projection, audience_impact)
              VALUES ('$score_id', '$cand_id', '$judge_id', '$choreography', '$projection', '$audience_impact')";
    $result = mysqli_query($conn, $query);

    if(!$result){
        $data = [
            'status' => 500,
            'message' => 'Insert Error: ' . mysqli_error($conn),
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        exit();
    }

    // Get the total_score that was just calculated by database
    $getScoreQuery = "SELECT total_score FROM production_score WHERE score_id = '$score_id'";
    $scoreResult = mysqli_query($conn, $getScoreQuery);
    if (!$scoreResult) {
        $data = [
            'status' => 500,
            'message' => 'Select Error: ' . mysqli_error($conn),
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        exit();
    }
    $scoreRow = mysqli_fetch_assoc($scoreResult);
    $current_total = $scoreRow['total_score'] ?? 0;

    // Calculate average total_score from all scores for this contestant
    $avg_query = "SELECT AVG(total_score) AS avg_total FROM production_score WHERE cand_id = '$cand_id'";
    $avg_result = mysqli_query($conn, $avg_query);
    if (!$avg_result) {
        $data = [
            'status' => 500,
            'message' => 'Average Query Error: ' . mysqli_error($conn),
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        exit();
    }

    $avg_row = mysqli_fetch_assoc($avg_result);
    $percentage = $avg_row['avg_total'] ?? 0;

    // Update or insert final_score
    $check_query = "SELECT cand_id FROM final_score WHERE cand_id = '$cand_id'";
    $check_result = mysqli_query($conn, $check_query);
    if (!$check_result) {
        $data = [
            'status' => 500,
            'message' => 'Check Final Score Error: ' . mysqli_error($conn),
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        exit();
    }

    if (mysqli_num_rows($check_result) > 0) {
        $update_query = "UPDATE final_score SET production_final_score = '$percentage' WHERE cand_id = '$cand_id'";
        $update_result = mysqli_query($conn, $update_query);
        if (!$update_result) {
            $data = [
                'status' => 500,
                'message' => 'Update Final Score Error: ' . mysqli_error($conn),
            ];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
            exit();
        }
    } else {
        $insert_query = "INSERT INTO final_score (cand_id, production_final_score) VALUES ('$cand_id', '$percentage')";
        $insert_result = mysqli_query($conn, $insert_query);
        if (!$insert_result) {
            $data = [
                'status' => 500,
                'message' => 'Insert Final Score Error: ' . mysqli_error($conn),
            ];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
            exit();
        }
    }

    $data = [
        'status' => 201,
        'message' => 'Production Score Created Successfully',
        'score_id' => $score_id,
        'total_score' => $current_total
    ];
    header("HTTP/1.0 201 Created");
    echo json_encode($data);
    exit();
}

// READ - Get All Production Scores
function getAllProductionScores(){
    global $conn;

    $query = "SELECT
                ps.score_id,
                ps.cand_id,
                ps.judge_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                ps.choreography,
                ps.projection,
                ps.audience_impact,
                ps.total_score
              FROM production_score ps
              INNER JOIN contestants c ON ps.cand_id = c.cand_id
              ORDER BY ps.total_score DESC";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) > 0){
            $res = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Production Scores Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Production Scores Found',
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }
    }else{
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}

// READ - Get Production Score by score_id
function getProductionScores($scoreParams){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreParams['score_id']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }

    $query = "SELECT
                ps.score_id,
                ps.cand_id,
                ps.judge_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                ps.choreography,
                ps.projection,
                ps.audience_impact,
                ps.total_score
              FROM production_score ps
              INNER JOIN contestants c ON ps.cand_id = c.cand_id
              WHERE ps.score_id = '$score_id' LIMIT 1";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) == 1){
            $res = mysqli_fetch_assoc($result);

            $data = [
                'status' => 200,
                'message' => 'Production Score Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Production Scores Found',
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }
    }else{
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}

// READ - Get Production Score by cand_id
function getProductionScoreByCandId($scoreParams){
    global $conn;

    $cand_id = mysqli_real_escape_string($conn, $scoreParams['cand_id']);

    if(empty(trim($cand_id))){
        return error422('Enter candidate ID');
    }

    $query = "SELECT
                ps.score_id,
                ps.cand_id,
                ps.judge_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                ps.choreography,
                ps.projection,
                ps.audience_impact,
                ps.total_score,
                ps.created_at
              FROM production_score ps
              INNER JOIN contestants c ON ps.cand_id = c.cand_id
              WHERE ps.cand_id = '$cand_id' LIMIT 1";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) == 1){
            $res = mysqli_fetch_assoc($result);

            $data = [
                'status' => 200,
                'message' => 'Production Score Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Production Score Found for this Candidate',
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }
    }else{
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}


// UPDATE PRODUCTION SCORE *ONLY THE CHAIRMAN CAN UPDATE OR EDIT
function updateProductionScore($scoreInput){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreInput['score_id']);
    $choreography = mysqli_real_escape_string($conn, $scoreInput['choreography']);
    $projection = mysqli_real_escape_string($conn, $scoreInput['projection']);
    $audience_impact = mysqli_real_escape_string($conn, $scoreInput['audience_impact']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }elseif(empty(trim($choreography))){
        return error422('Enter choreography score');
    }elseif(empty(trim($projection))){
        return error422('Enter projection score');
    }elseif(empty(trim($audience_impact))){
        return error422('Enter audience impact score');
    }else{

        $query = "UPDATE production_score SET
                    choreography = '$choreography',
                    projection = '$projection',
                    audience_impact = '$audience_impact'
                  WHERE score_id = '$score_id' LIMIT 1";

        $result = mysqli_query($conn, $query);

        if($result){
            $data = [
                'status' => 200,
                'message' => 'Production Score Updated Successfully',
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            return json_encode($data);
        }
    }
}

// DELETE
function deleteProductionScore($scoreInput){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreInput['score_id']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }

    $query = "DELETE FROM production_score WHERE score_id = '$score_id' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if($result){
        $data = [
            'status' => 200,
            'message' => 'Production Score Deleted Successfully',
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);
    }else{
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}

// READ - Get Production Scores by Judge ID
function getProductionScoresByJudge($judgeParams){
    global $conn;

    $judge_id = mysqli_real_escape_string($conn, $judgeParams['judge_id']);

    if(empty(trim($judge_id))){
        return error422('Enter judge ID');
    }

    $whereClause = "WHERE ps.judge_id = '$judge_id'";
    if (isset($judgeParams['gender']) && !empty(trim($judgeParams['gender']))) {
        $gender = mysqli_real_escape_string($conn, $judgeParams['gender']);
        $whereClause .= " AND c.cand_gender = '$gender'";
    }

    $query = "SELECT
                ps.score_id,
                ps.cand_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                ps.choreography,
                ps.projection,
                ps.audience_impact,
                ps.total_score,
                ps.created_at
              FROM production_score ps
              INNER JOIN contestants c ON ps.cand_id = c.cand_id
              $whereClause
              ORDER BY ps.created_at DESC";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) > 0){
            $res = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Production Scores for Judge Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Production Scores Found for this Judge',
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }
    }else{
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}

// Error handler function (if not already in your functions.php)
// function error422($message){
//    $data = [
//        'status' => 422,
//        'message' => $message,
//    ];
//    header("HTTP/1.0 422 Unprocessable Entity");
//   return json_encode($data);
//}
?>
