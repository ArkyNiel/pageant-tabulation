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

// store uniform score
function storeUniformScore($scoreInput){
    global $conn;

    // Extract and escape input data
    $cand_id = isset($scoreInput['cand_id']) ? mysqli_real_escape_string($conn, $scoreInput['cand_id']) : '';
    $poise_and_bearings = isset($scoreInput['poise_and_bearings']) ? mysqli_real_escape_string($conn, $scoreInput['poise_and_bearings']) : '';
    $personality_and_projection = isset($scoreInput['personality_and_projection']) ? mysqli_real_escape_string($conn, $scoreInput['personality_and_projection']) : '';
    $neatness = isset($scoreInput['neatness']) ? mysqli_real_escape_string($conn, $scoreInput['neatness']) : '';
    $overall_impact = isset($scoreInput['overall_impact']) ? mysqli_real_escape_string($conn, $scoreInput['overall_impact']) : '';

    // Improved validation - check if value is set and not empty string
    if($cand_id === '' || $cand_id === null){
        error422('Enter candidate ID');
        return; // This won't execute due to exit() in error422
    }
    if($poise_and_bearings === '' || $poise_and_bearings === null){
        error422('Enter poise and bearings score');
        return;
    }
    if($personality_and_projection === '' || $personality_and_projection === null){
        error422('Enter personality and projection score');
        return;
    }
    if($neatness === '' || $neatness === null){
        error422('Enter neatness score');
        return;
    }
    if($overall_impact === '' || $overall_impact === null){
        error422('Enter overall impact score');
        return;
    }

    // Verify candidate exists
    $checkCandQuery = "SELECT cand_id FROM contestants WHERE cand_id = '$cand_id'";
    $checkCandResult = mysqli_query($conn, $checkCandQuery);
    
    if(!$checkCandResult || mysqli_num_rows($checkCandResult) === 0){
        error422('Candidate ID does not exist');
        return;
    }

    // Generate unique score_id
    do {
        $score_id = rand(100000, 999999);
        $checkQuery = "SELECT score_id FROM uniform_score WHERE score_id = '$score_id'";
        $checkResult = mysqli_query($conn, $checkQuery);
    } while ($checkResult && mysqli_num_rows($checkResult) > 0);

    // Insert without total_score - let the database trigger/default handle it
    $query = "INSERT INTO uniform_score (score_id, cand_id, poise_and_bearings, personality_and_projection, neatness, overall_impact)
              VALUES ('$score_id', '$cand_id', '$poise_and_bearings', '$personality_and_projection', '$neatness', '$overall_impact')";
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
    $getScoreQuery = "SELECT total_score FROM uniform_score WHERE score_id = '$score_id'";
    $scoreResult = mysqli_query($conn, $getScoreQuery);
    $scoreRow = mysqli_fetch_assoc($scoreResult);
    $current_total = $scoreRow['total_score'] ?? 0;

    // Calculate average total_score from all scores for this contestant
    $avg_query = "SELECT AVG(total_score) AS avg_total FROM uniform_score WHERE cand_id = '$cand_id'";
    $avg_result = mysqli_query($conn, $avg_query);
    
    if($avg_result){
        $avg_row = mysqli_fetch_assoc($avg_result);
        $percentage = $avg_row['avg_total'] ?? 0;

        // Update or insert final_score
        $check_query = "SELECT cand_id FROM final_score WHERE cand_id = '$cand_id'";
        $check_result = mysqli_query($conn, $check_query);
        
        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $update_query = "UPDATE final_score SET uniform_final_score = '$percentage' WHERE cand_id = '$cand_id'";
            mysqli_query($conn, $update_query);
        } else {
            $insert_query = "INSERT INTO final_score (cand_id, uniform_final_score) VALUES ('$cand_id', '$percentage')";
            mysqli_query($conn, $insert_query);
        }
    }

    $data = [
        'status' => 201,
        'message' => 'Uniform Score Created Successfully',
        'score_id' => $score_id,
        'total_score' => $current_total
    ];
    header("HTTP/1.0 201 Created");
    echo json_encode($data);
    exit();
}

// READ - Get All Uniform Scores
function getAllUniformScores(){
    global $conn;

    $query = "SELECT
                us.score_id,
                us.cand_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                us.poise_and_bearings,
                us.personality_and_projection,
                us.neatness,
                us.overall_impact,
                us.total_score
              FROM uniform_score us
              INNER JOIN contestants c ON us.cand_id = c.cand_id
              ORDER BY us.total_score DESC";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) > 0){
            $res = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Uniform Scores Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Uniform Scores Found',
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

// READ - Get Uniform Score by score_id
function getUniformScores($scoreParams){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreParams['score_id']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }

    $query = "SELECT
                us.score_id,
                us.cand_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                us.poise_and_bearings,
                us.personality_and_projection,
                us.neatness,
                us.overall_impact,
                us.total_score
              FROM uniform_score us
              INNER JOIN contestants c ON us.cand_id = c.cand_id
              WHERE us.score_id = '$score_id' LIMIT 1";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) == 1){
            $res = mysqli_fetch_assoc($result);

            $data = [
                'status' => 200,
                'message' => 'Uniform Score Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Uniform Scores Found',
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

// READ - Get Uniform Score by cand_id
function getUniformScoreByCandId($scoreParams){
    global $conn;

    $cand_id = mysqli_real_escape_string($conn, $scoreParams['cand_id']);

    if(empty(trim($cand_id))){
        return error422('Enter candidate ID');
    }

    $query = "SELECT
                us.score_id,
                us.cand_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                us.poise_and_bearings,
                us.personality_and_projection,
                us.neatness,
                us.overall_impact,
                us.total_score,
                us.created_at
              FROM uniform_score us
              INNER JOIN contestants c ON us.cand_id = c.cand_id
              WHERE us.cand_id = '$cand_id' LIMIT 1";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) == 1){
            $res = mysqli_fetch_assoc($result);

            $data = [
                'status' => 200,
                'message' => 'Uniform Score Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Uniform Score Found for this Candidate',
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


// UPDATE UNIFORM SCORE *ONLY THE CHAIRMAN CAN UPDATE OR EDIT
function updateUniformScore($scoreInput){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreInput['score_id']);
    $poise_and_bearings = mysqli_real_escape_string($conn, $scoreInput['poise_and_bearings']);
    $personality_and_projection = mysqli_real_escape_string($conn, $scoreInput['personality_and_projection']);
    $neatness = mysqli_real_escape_string($conn, $scoreInput['neatness']);
    $overall_impact = mysqli_real_escape_string($conn, $scoreInput['overall_impact']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }elseif(empty(trim($poise_and_bearings))){
        return error422('Enter poise and bearings score');
    }elseif(empty(trim($personality_and_projection))){
        return error422('Enter personality and projection score');
    }elseif(empty(trim($neatness))){
        return error422('Enter neatness score');
    }elseif(empty(trim($overall_impact))){
        return error422('Enter overall impact score');
    }else{

        $query = "UPDATE uniform_score SET
                    poise_and_bearings = '$poise_and_bearings',
                    personality_and_projection = '$personality_and_projection',
                    neatness = '$neatness',
                    overall_impact = '$overall_impact'
                  WHERE score_id = '$score_id' LIMIT 1";

        $result = mysqli_query($conn, $query);

        if($result){
            $data = [
                'status' => 200,
                'message' => 'Uniform Score Updated Successfully',
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
function deleteUniformScore($scoreInput){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreInput['score_id']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }

    $query = "DELETE FROM uniform_score WHERE score_id = '$score_id' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if($result){
        $data = [
            'status' => 200,
            'message' => 'Uniform Score Deleted Successfully',
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
