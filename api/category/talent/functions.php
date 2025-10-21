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

// store talent store
function storeTalentScore($scoreInput){
    global $conn;
    
    $cand_id = mysqli_real_escape_string($conn, $scoreInput['cand_id']);
    $judge_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
    $mastery = mysqli_real_escape_string($conn, $scoreInput['mastery']);
    $performance_choreography = mysqli_real_escape_string($conn, $scoreInput['performance_choreography']);
    $overall_impression = mysqli_real_escape_string($conn, $scoreInput['overall_impression']);
    $audience_impact = mysqli_real_escape_string($conn, $scoreInput['audience_impact']);
    
    if(empty(trim($cand_id))){
        return error422('Enter candidate ID');
    }elseif(empty(trim($mastery))){
        return error422('Enter mastery score');
    }elseif(empty(trim($performance_choreography))){
        return error422('Enter performance/choreography score');
    }elseif(empty(trim($overall_impression))){
        return error422('Enter overall impression score');
    }elseif(empty(trim($audience_impact))){
        return error422('Enter audience impact score');
    }else{
        // Generate unique score_id
        do {
            $score_id = rand(100000, 999999);
            $checkQuery = "SELECT score_id FROM talent_score WHERE score_id = '$score_id'";
            $checkResult = mysqli_query($conn, $checkQuery);
        } while (mysqli_num_rows($checkResult) > 0);
        
        $query = "INSERT INTO talent_score (score_id, cand_id, judge_id, mastery, performance_choreography, overall_impression, audience_impact)
                  VALUES ('$score_id', '$cand_id', '$judge_id', '$mastery', '$performance_choreography', '$overall_impression', '$audience_impact')";
        $result = mysqli_query($conn, $query);

        if($result){
            // Calculate average total_score from all judges submitted so far for this contestant
            $avg_query = "SELECT AVG(total_score) AS avg_total FROM talent_score WHERE cand_id = '$cand_id'";
            $avg_result = mysqli_query($conn, $avg_query);
            $avg_row = mysqli_fetch_assoc($avg_result);
            $avg_total = $avg_row['avg_total'];

            // The final score is the average total_score (sum of all judges' total_scores divided by number of judges)
            $percentage = $avg_total;

            // Check if final_score row exists for this cand_id
            $check_query = "SELECT cand_id FROM final_score WHERE cand_id = '$cand_id'";
            $check_result = mysqli_query($conn, $check_query);
            if (mysqli_num_rows($check_result) > 0) {
                // Update existing row
                $update_query = "UPDATE final_score SET talent_final_score = '$percentage' WHERE cand_id = '$cand_id'";
                mysqli_query($conn, $update_query);
            } else {
                // Insert new row
                $insert_query = "INSERT INTO final_score (cand_id, talent_final_score) VALUES ('$cand_id', '$percentage')";
                mysqli_query($conn, $insert_query);
            }

            $data = [
                'status' => 201,
                'message' => 'Talent Score Created Successfully',
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

// READ - Get All Talent Scores
function getAllTalentScores($params = []){
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
                ts.mastery,
                ts.performance_choreography,
                ts.overall_impression,
                ts.audience_impact,
                ts.total_score
              FROM talent_score ts
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
                'message' => 'Talent Scores for All Judges Fetched Successfully',
                'data' => $groupedScores
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Talent Scores Found',
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

// READ - Get Talent Score by score_id
function getTalentScores($scoreParams){
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
                ts.mastery,
                ts.performance_choreography,
                ts.overall_impression,
                ts.audience_impact,
                ts.total_score
              FROM talent_score ts
              INNER JOIN contestants c ON ts.cand_id = c.cand_id
              WHERE ts.score_id = '$score_id' LIMIT 1";
    
    $result = mysqli_query($conn, $query);
    
    if($result){
        if(mysqli_num_rows($result) == 1){
            $res = mysqli_fetch_assoc($result);
            
            $data = [
                'status' => 200,
                'message' => 'Talent Score Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Talent Scores Found',
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

// READ - Get Talent Score by cand_id
function getTalentScoreByCandId($scoreParams){
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
                ts.mastery,
                ts.performance_choreography,
                ts.overall_impression,
                ts.audience_impact,
                ts.total_score,
                ts.created_at
              FROM talent_score ts
              INNER JOIN contestants c ON ts.cand_id = c.cand_id
              WHERE ts.cand_id = '$cand_id' LIMIT 1";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) == 1){
            $res = mysqli_fetch_assoc($result);

            $data = [
                'status' => 200,
                'message' => 'Talent Score Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Talent Score Found for this Candidate',
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


// UPDATE TALENT SCORE *ONLY THE CHAIRMAN CAN UPDATE OR EDIT
function updateTalentScore($scoreInput){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreInput['score_id']);
    $mastery = mysqli_real_escape_string($conn, $scoreInput['mastery']);
    $performance_choreography = mysqli_real_escape_string($conn, $scoreInput['performance_choreography']);
    $overall_impression = mysqli_real_escape_string($conn, $scoreInput['overall_impression']);
    $audience_impact = mysqli_real_escape_string($conn, $scoreInput['audience_impact']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }elseif(empty(trim($mastery))){
        return error422('Enter mastery score');
    }elseif(empty(trim($performance_choreography))){
        return error422('Enter performance/choreography score');
    }elseif(empty(trim($overall_impression))){
        return error422('Enter overall impression score');
    }elseif(empty(trim($audience_impact))){
        return error422('Enter audience impact score');
    }else{

        $query = "UPDATE talent_score SET
                    mastery = '$mastery',
                    performance_choreography = '$performance_choreography',
                    overall_impression = '$overall_impression',
                    audience_impact = '$audience_impact'
                  WHERE score_id = '$score_id' LIMIT 1";

        $result = mysqli_query($conn, $query);

        if($result){
            $data = [
                'status' => 200,
                'message' => 'Talent Score Updated Successfully',
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
function deleteTalentScore($scoreInput){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreInput['score_id']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }

    $query = "DELETE FROM talent_score WHERE score_id = '$score_id' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if($result){
        $data = [
            'status' => 200,
            'message' => 'Talent Score Deleted Successfully',
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
