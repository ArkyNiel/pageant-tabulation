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

// store swimwear score
function storeSwimwearScore($scoreInput){
    global $conn;
    
    $cand_id = mysqli_real_escape_string($conn, $scoreInput['cand_id']);
    $stage_presence = mysqli_real_escape_string($conn, $scoreInput['stage_presence']);
    $figure_and_fitness = mysqli_real_escape_string($conn, $scoreInput['figure_and_fitness']);
    $poise_and_bearing = mysqli_real_escape_string($conn, $scoreInput['poise_and_bearing']);
    $overall_impact = mysqli_real_escape_string($conn, $scoreInput['overall_impact']);
    
    if(empty(trim($cand_id))){
        return error422('Enter candidate ID');
    }elseif(empty(trim($stage_presence))){
        return error422('Enter stage presence score');
    }elseif(empty(trim($figure_and_fitness))){
        return error422('Enter figure and fitness score');
    }elseif(empty(trim($poise_and_bearing))){
        return error422('Enter poise and bearing score');
    }elseif(empty(trim($overall_impact))){
        return error422('Enter overall impact score');
    }else{
        // Generate unique score_id
        do {
            $score_id = rand(100000, 999999);
            $checkQuery = "SELECT score_id FROM simwear_score WHERE score_id = '$score_id'";
            $checkResult = mysqli_query($conn, $checkQuery);
        } while (mysqli_num_rows($checkResult) > 0);
        
        $total_score = $stage_presence + $figure_and_fitness + $poise_and_bearing + $overall_impact;
        
        $query = "INSERT INTO simwear_score (score_id, cand_id, stage_presence, figure_and_fitness, poise_and_bearing, overall_impact, total_score, created_at)
                  VALUES ('$score_id', '$cand_id', '$stage_presence', '$figure_and_fitness', '$poise_and_bearing', '$overall_impact', '$total_score', NOW())";
        $result = mysqli_query($conn, $query);

        if($result){
            // Calculate average total_score from all scores for this contestant
            $avg_query = "SELECT AVG(total_score) AS avg_total FROM simwear_score WHERE cand_id = '$cand_id'";
            $avg_result = mysqli_query($conn, $avg_query);
            if ($avg_result) {
                $avg_row = mysqli_fetch_assoc($avg_result);
                $avg_total = $avg_row['avg_total'];

                $percentage = $avg_total;

                // Check if final_score row exists for this cand_id
                $check_query = "SELECT cand_id FROM final_score WHERE cand_id = '$cand_id'";
                $check_result = mysqli_query($conn, $check_query);
                if ($check_result && mysqli_num_rows($check_result) > 0) {
                    // Update existing row
                    $update_query = "UPDATE final_score SET swimwear_final_score = '$percentage' WHERE cand_id = '$cand_id'";
                    mysqli_query($conn, $update_query);
                } else {
                    // Insert new row
                    $insert_query = "INSERT INTO final_score (cand_id, swimwear_final_score) VALUES ('$cand_id', '$percentage')";
                    mysqli_query($conn, $insert_query);
                }
            }

            $data = [
                'status' => 201,
                'message' => 'Swimwear Score Created Successfully'
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

// READ - Get All Swimwear Scores
function getAllSwimwearScores($params = []){
    global $conn;

    $whereClause = "";
    if (isset($params['gender']) && !empty(trim($params['gender']))) {
        $gender = mysqli_real_escape_string($conn, $params['gender']);
        $whereClause = "WHERE c.cand_gender = '$gender'";
    }

    $query = "SELECT
                ss.score_id,
                ss.cand_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                ss.stage_presence,
                ss.figure_and_fitness,
                ss.poise_and_bearing,
                ss.overall_impact,
                ss.total_score,
                ss.created_at
              FROM simwear_score ss
              INNER JOIN contestants c ON ss.cand_id = c.cand_id
              $whereClause
              ORDER BY ss.total_score DESC";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) > 0){
            $res = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Swimwear Scores Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Swimwear Scores Found',
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

// READ - Get Swimwear Score by score_id
function getSwimwearScores($scoreParams){
    global $conn;
    
    $score_id = mysqli_real_escape_string($conn, $scoreParams['score_id']);
    
    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }
    
    $query = "SELECT
                ss.score_id,
                ss.cand_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                ss.stage_presence,
                ss.figure_and_fitness,
                ss.poise_and_bearing,
                ss.overall_impact,
                ss.total_score,
                ss.created_at
              FROM simwear_score ss
              INNER JOIN contestants c ON ss.cand_id = c.cand_id
              WHERE ss.score_id = '$score_id' LIMIT 1";
    
    $result = mysqli_query($conn, $query);
    
    if($result){
        if(mysqli_num_rows($result) == 1){
            $res = mysqli_fetch_assoc($result);
            
            $data = [
                'status' => 200,
                'message' => 'Swimwear Score Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Swimwear Scores Found',
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

// READ - Get Swimwear Scores by cand_id
function getSwimwearScoreByCandId($scoreParams){
    global $conn;

    $cand_id = mysqli_real_escape_string($conn, $scoreParams['cand_id']);

    if(empty(trim($cand_id))){
        return error422('Enter candidate ID');
    }

    $query = "SELECT
                ss.score_id,
                ss.cand_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                ss.stage_presence,
                ss.figure_and_fitness,
                ss.poise_and_bearing,
                ss.overall_impact,
                ss.total_score,
                ss.created_at
              FROM simwear_score ss
              INNER JOIN contestants c ON ss.cand_id = c.cand_id
              WHERE ss.cand_id = '$cand_id'";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) > 0){
            $res = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Swimwear Scores Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Swimwear Score Found for this Candidate',
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


// UPDATE SWIMWEAR SCORE
function updateSwimwearScore($scoreInput){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreInput['score_id']);
    $stage_presence = mysqli_real_escape_string($conn, $scoreInput['stage_presence']);
    $figure_and_fitness = mysqli_real_escape_string($conn, $scoreInput['figure_and_fitness']);
    $poise_and_bearing = mysqli_real_escape_string($conn, $scoreInput['poise_and_bearing']);
    $overall_impact = mysqli_real_escape_string($conn, $scoreInput['overall_impact']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }elseif(empty(trim($stage_presence))){
        return error422('Enter stage presence score');
    }elseif(empty(trim($figure_and_fitness))){
        return error422('Enter figure and fitness score');
    }elseif(empty(trim($poise_and_bearing))){
        return error422('Enter poise and bearing score');
    }elseif(empty(trim($overall_impact))){
        return error422('Enter overall impact score');
    }else{

        $total_score = $stage_presence + $figure_and_fitness + $poise_and_bearing + $overall_impact;

        $query = "UPDATE simwear_score SET
                    stage_presence = '$stage_presence',
                    figure_and_fitness = '$figure_and_fitness',
                    poise_and_bearing = '$poise_and_bearing',
                    overall_impact = '$overall_impact',
                    total_score = '$total_score'
                  WHERE score_id = '$score_id' LIMIT 1";

        $result = mysqli_query($conn, $query);

        if($result){
            $data = [
                'status' => 200,
                'message' => 'Swimwear Score Updated Successfully',
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
function deleteSwimwearScore($scoreInput){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreInput['score_id']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }

    // First, check if the score exists and get the data
    $selectQuery = "SELECT * FROM simwear_score WHERE score_id = '$score_id' LIMIT 1";
    $selectResult = mysqli_query($conn, $selectQuery);

    if($selectResult && mysqli_num_rows($selectResult) == 1){
        $deletedData = mysqli_fetch_assoc($selectResult);

        // Now delete the score
        $deleteQuery = "DELETE FROM simwear_score WHERE score_id = '$score_id' LIMIT 1";
        $deleteResult = mysqli_query($conn, $deleteQuery);

        if($deleteResult && mysqli_affected_rows($conn) > 0){
            $data = [
                'status' => 200,
                'message' => 'Swimwear Score Deleted Successfully',
                'data' => $deletedData
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
    }else{
        $data = [
            'status' => 404,
            'message' => 'Swimwear Score Not Found',
        ];
        header("HTTP/1.0 404 Not Found");
        return json_encode($data);
    }
}

// READ - Get Swimwear Scores by Gender
function getSwimwearScoresByGender($genderParams){
    global $conn;

    $gender = mysqli_real_escape_string($conn, $genderParams['gender']);

    if(empty(trim($gender))){
        return error422('Enter gender');
    }

    $whereClause = "WHERE c.cand_gender = '$gender'";

    $query = "SELECT
                ss.score_id,
                ss.cand_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                ss.stage_presence,
                ss.figure_and_fitness,
                ss.poise_and_bearing,
                ss.overall_impact,
                ss.total_score,
                ss.created_at
              FROM simwear_score ss
              INNER JOIN contestants c ON ss.cand_id = c.cand_id
              $whereClause
              ORDER BY ss.created_at DESC";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) > 0){
            $res = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Swimwear Scores for Gender Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Swimwear Scores Found for this Gender',
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

?>
