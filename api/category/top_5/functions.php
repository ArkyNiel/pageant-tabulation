<?php
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

// store top 5 score
function storeTop5Score($scoreInput){
    global $conn;

    $cand_id = mysqli_real_escape_string($conn, $scoreInput['cand_id']);
    $judge_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
    $qna = mysqli_real_escape_string($conn, $scoreInput['qna']);
    $beauty = mysqli_real_escape_string($conn, $scoreInput['beauty']);

    if(empty(trim($cand_id))){
        return error422('Enter candidate ID');
    }elseif(empty(trim($qna))){
        return error422('Enter QnA score');
    }elseif(empty(trim($beauty))){
        return error422('Enter beauty score');
    }else{
        // Calculate total_score
        $total_score = $qna + $beauty;

        // Generate unique score_id
        do {
            $score_id = rand(100000, 999999);
            $checkQuery = "SELECT score_id FROM top_5_qna_score WHERE score_id = '$score_id'";
            $checkResult = mysqli_query($conn, $checkQuery);
        } while (mysqli_num_rows($checkResult) > 0);

        $query = "INSERT INTO top_5_qna_score (score_id, cand_id, judge_id, qna, beauty, total_score)
                  VALUES ('$score_id', '$cand_id', '$judge_id', '$qna', '$beauty', '$total_score')";
        $result = mysqli_query($conn, $query);

        if($result){
            // Calculate average total_score from all judges submitted so far for this contestant
            $avg_query = "SELECT AVG(total_score) AS avg_total FROM top_5_qna_score WHERE cand_id = '$cand_id'";
            $avg_result = mysqli_query($conn, $avg_query);
            $avg_row = mysqli_fetch_assoc($avg_result);
            $avg_total = $avg_row['avg_total'];

            // The final score is the average total_score (sum of all judges' total_scores divided by number of judges)
            $percentage = $avg_total;

            // Check if top_5_final row exists for this cand_id
            $check_query = "SELECT cand_id FROM top_5_final WHERE cand_id = '$cand_id'";
            $check_result = mysqli_query($conn, $check_query);
            if (mysqli_num_rows($check_result) > 0) {
                // Update existing row
                $update_query = "UPDATE top_5_final SET final_score = '$percentage' WHERE cand_id = '$cand_id'";
                mysqli_query($conn, $update_query);
            } else {
                // Insert new row
                $insert_query = "INSERT INTO top_5_final (cand_id, final_score) VALUES ('$cand_id', '$percentage')";
                mysqli_query($conn, $insert_query);
            }

            $data = [
                'status' => 201,
                'message' => 'Top 5 Score Created Successfully',
                'has_submitted' => isset($_SESSION['has_submitted']) ? (bool)$_SESSION['has_submitted'] : false
            ];
            header("HTTP/1.0 201 Created");
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

// READ - Get All Top 5 Scores
function getAllTop5Scores($params = []){
    global $conn;

    $whereClause = "";
    if (isset($params['gender']) && !empty(trim($params['gender']))) {
        $gender = mysqli_real_escape_string($conn, $params['gender']);
        $whereClause = "WHERE c.cand_gender = '$gender'";
    }

    $query = "SELECT
                ts.score_id,
                ts.cand_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                ts.judge_id,
                ts.qna,
                ts.beauty,
                ts.total_score
              FROM top_5_qna_score ts
              INNER JOIN contestants c ON ts.cand_id = c.cand_id
              $whereClause
              ORDER BY ts.judge_id, ts.total_score DESC";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) > 0){
            $res = mysqli_fetch_all($result, MYSQLI_ASSOC);

            // Group scores by judge_id
            $groupedScores = [];
            foreach ($res as $score) {
                $judge_id = $score['judge_id'];
                if (!isset($groupedScores['judge_' . $judge_id])) {
                    $groupedScores['judge_' . $judge_id] = [];
                }
                $groupedScores['judge_' . $judge_id][] = $score;
            }

            $data = [
                'status' => 200,
                'message' => 'Top 5 Scores for All Judges Fetched Successfully',
                'data' => $groupedScores
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Top 5 Scores Found',
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

// READ - Get Top 5 Score by score_id
function getTop5Scores($scoreParams){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreParams['score_id']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }

    $query = "SELECT
                ts.score_id,
                ts.cand_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                ts.qna,
                ts.beauty,
                ts.total_score
              FROM top_5_qna_score ts
              INNER JOIN contestants c ON ts.cand_id = c.cand_id
              WHERE ts.score_id = '$score_id' LIMIT 1";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) == 1){
            $res = mysqli_fetch_assoc($result);

            $data = [
                'status' => 200,
                'message' => 'Top 5 Score Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Top 5 Scores Found',
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

// READ - Get Top 5 Score by cand_id
function getTop5ScoreByCandId($scoreParams){
    global $conn;

    $cand_id = mysqli_real_escape_string($conn, $scoreParams['cand_id']);

    if(empty(trim($cand_id))){
        return error422('Enter candidate ID');
    }

    $query = "SELECT
                ts.score_id,
                ts.cand_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                ts.qna,
                ts.beauty,
                ts.total_score,
                ts.created_at
              FROM top_5_qna_score ts
              INNER JOIN contestants c ON ts.cand_id = c.cand_id
              WHERE ts.cand_id = '$cand_id' LIMIT 1";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) == 1){
            $res = mysqli_fetch_assoc($result);

            $data = [
                'status' => 200,
                'message' => 'Top 5 Score Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Top 5 Score Found for this Candidate',
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


// UPDATE TOP 5 SCORE *ONLY THE CHAIRMAN CAN UPDATE OR EDIT
function updateTop5Score($scoreInput){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreInput['score_id']);
    $qna = mysqli_real_escape_string($conn, $scoreInput['qna']);
    $beauty = mysqli_real_escape_string($conn, $scoreInput['beauty']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }elseif(empty(trim($qna))){
        return error422('Enter QnA score');
    }elseif(empty(trim($beauty))){
        return error422('Enter beauty score');
    }else{
        // Calculate total_score
        $total_score = $qna + $beauty;

        $query = "UPDATE top_5_qna_score SET
                    qna = '$qna',
                    beauty = '$beauty',
                    total_score = '$total_score'
                  WHERE score_id = '$score_id' LIMIT 1";

        $result = mysqli_query($conn, $query);

        if($result){
            $data = [
                'status' => 200,
                'message' => 'Top 5 Score Updated Successfully',
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

// DELETE TOP 5 SCORE
function deleteTop5Score($scoreInput){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreInput['score_id']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }

    $query = "DELETE FROM top_5_qna_score WHERE score_id = '$score_id' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if($result){
        $data = [
            'status' => 200,
            'message' => 'Top 5 Score Deleted Successfully',
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

// READ - Get Talent Scores by Judge ID
function getTalentScoresByJudge($judgeParams){
    global $conn;

    $judge_id = mysqli_real_escape_string($conn, $judgeParams['judge_id']);

    if(empty(trim($judge_id))){
        return error422('Enter judge ID');
    }

    $whereClause = "WHERE ts.judge_id = '$judge_id'";
    if (isset($judgeParams['gender']) && !empty(trim($judgeParams['gender']))) {
        $gender = mysqli_real_escape_string($conn, $judgeParams['gender']);
        $whereClause .= " AND c.cand_gender = '$gender'";
    }

    $query = "SELECT
                ts.score_id,
                ts.cand_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                ts.mastery,
                ts.performance_choreography,
                ts.overall_impression,
                ts.audience_impact,
                ts.total_score,
                ts.created_at
              FROM talent_score ts
              INNER JOIN contestants c ON ts.cand_id = c.cand_id
              $whereClause
              ORDER BY ts.created_at DESC";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) > 0){
            $res = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Talent Scores for Judge Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Talent Scores Found for this Judge',
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
