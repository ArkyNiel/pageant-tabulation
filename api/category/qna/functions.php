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

// store qna score
function storeQnaScore($scoreInput){
    global $conn;

    // Get judge_id from session
    $judge_id = isset($_SESSION['user_id']) ? mysqli_real_escape_string($conn, $_SESSION['user_id']) : '';

    if(empty($judge_id)){
        error422('Judge not logged in');
        return;
    }

    // Extract and escape input data
    $cand_id = isset($scoreInput['cand_id']) ? mysqli_real_escape_string($conn, $scoreInput['cand_id']) : '';
    $total_score = isset($scoreInput['total_score']) ? mysqli_real_escape_string($conn, $scoreInput['total_score']) : '';

    // Improved validation - check if value is set and not empty string
    if($cand_id === '' || $cand_id === null){
        error422('Enter candidate ID');
        return;
    }
    if($total_score === '' || $total_score === null){
        error422('Enter total score');
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
    $checkDuplicateQuery = "SELECT score_id FROM qna_score WHERE cand_id = '$cand_id' AND judge_id = '$judge_id' LIMIT 1";
    $checkDuplicateResult = mysqli_query($conn, $checkDuplicateQuery);

    if($checkDuplicateResult && mysqli_num_rows($checkDuplicateResult) > 0){
        error422('You have already submitted a score for this candidate');
        return;
    }

    // Generate unique score_id
    do {
        $score_id = rand(100000, 999999);
        $checkQuery = "SELECT score_id FROM qna_score WHERE score_id = '$score_id'";
        $checkResult = mysqli_query($conn, $checkQuery);
    } while ($checkResult && mysqli_num_rows($checkResult) > 0);

    // Insert total_score
    $query = "INSERT INTO qna_score (score_id, cand_id, judge_id, total_score)
              VALUES ('$score_id', '$cand_id', '$judge_id', '$total_score')";
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

    // Calculate average total_score from all scores for this contestant
    $avg_query = "SELECT AVG(total_score) AS avg_total FROM qna_score WHERE cand_id = '$cand_id'";
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
        $update_query = "UPDATE final_score SET qna_final_score = '$percentage' WHERE cand_id = '$cand_id'";
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
        $insert_query = "INSERT INTO final_score (cand_id, qna_final_score) VALUES ('$cand_id', '$percentage')";
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
        'message' => 'Q&A Score Created Successfully',
        'score_id' => $score_id,
        'total_score' => $total_score
    ];
    header("HTTP/1.0 201 Created");
    echo json_encode($data);
    exit();
}

// READ - Get All Qna Scores
function getAllQnaScores(){
    global $conn;

    $query = "SELECT
                qs.score_id,
                qs.cand_id,
                qs.judge_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                qs.total_score
              FROM qna_score qs
              INNER JOIN contestants c ON qs.cand_id = c.cand_id
              ORDER BY qs.total_score DESC";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) > 0){
            $res = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Q&A Scores Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Q&A Scores Found',
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

// READ - Get Qna Score by score_id
function getQnaScores($scoreParams){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreParams['score_id']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }

    $query = "SELECT
                qs.score_id,
                qs.cand_id,
                qs.judge_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                qs.total_score
              FROM qna_score qs
              INNER JOIN contestants c ON qs.cand_id = c.cand_id
              WHERE qs.score_id = '$score_id' LIMIT 1";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) == 1){
            $res = mysqli_fetch_assoc($result);

            $data = [
                'status' => 200,
                'message' => 'Q&A Score Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Q&A Scores Found',
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

// READ - Get Qna Score by cand_id
function getQnaScoreByCandId($scoreParams){
    global $conn;

    $cand_id = mysqli_real_escape_string($conn, $scoreParams['cand_id']);

    if(empty(trim($cand_id))){
        return error422('Enter candidate ID');
    }

    $query = "SELECT
                qs.score_id,
                qs.cand_id,
                qs.judge_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                qs.total_score,
                qs.created_at
              FROM qna_score qs
              INNER JOIN contestants c ON qs.cand_id = c.cand_id
              WHERE qs.cand_id = '$cand_id'";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) > 0){
            $res = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Q&A Scores Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Q&A Scores Found for this Candidate',
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


// UPDATE QNA SCORE *ONLY THE CHAIRMAN CAN UPDATE OR EDIT
function updateQnaScore($scoreInput){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreInput['score_id']);
    $total_score = mysqli_real_escape_string($conn, $scoreInput['total_score']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }elseif(empty(trim($total_score))){
        return error422('Enter total score');
    }else{

        $query = "UPDATE qna_score SET
                    total_score = '$total_score'
                  WHERE score_id = '$score_id' LIMIT 1";

        $result = mysqli_query($conn, $query);

        if($result){
            $data = [
                'status' => 200,
                'message' => 'Q&A Score Updated Successfully',
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
function deleteQnaScore($scoreInput){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreInput['score_id']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }

    $query = "DELETE FROM qna_score WHERE score_id = '$score_id' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if($result){
        $data = [
            'status' => 200,
            'message' => 'Q&A Score Deleted Successfully',
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

// READ - Get Qna Scores by Judge ID
function getQnaScoresByJudge($judgeParams){
    global $conn;

    $judge_id = mysqli_real_escape_string($conn, $judgeParams['judge_id']);

    if(empty(trim($judge_id))){
        return error422('Enter judge ID');
    }

    $whereClause = "WHERE qs.judge_id = '$judge_id'";
    if (isset($judgeParams['gender']) && !empty(trim($judgeParams['gender']))) {
        $gender = mysqli_real_escape_string($conn, $judgeParams['gender']);
        $whereClause .= " AND c.cand_gender = '$gender'";
    }

    $query = "SELECT
                qs.score_id,
                qs.cand_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                qs.total_score,
                qs.created_at
              FROM qna_score qs
              INNER JOIN contestants c ON qs.cand_id = c.cand_id
              $whereClause
              ORDER BY qs.created_at DESC";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) > 0){
            $res = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Q&A Scores for Judge Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Q&A Scores Found for this Judge',
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
