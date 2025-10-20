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


// store formalwear score
function storeFormalwearScore($scoreInput){
    global $conn;
    
    $cand_id = mysqli_real_escape_string($conn, $scoreInput['cand_id']);
    $poise_and_bearing = mysqli_real_escape_string($conn, $scoreInput['poise_and_bearing']);
    $personality_and_projection = mysqli_real_escape_string($conn, $scoreInput['personality_and_projection']);
    $appropriateness_and_elegance_of_attire = mysqli_real_escape_string($conn, $scoreInput['appropriateness_and_elegance_of_attire']);
    $overall_impact = mysqli_real_escape_string($conn, $scoreInput['overall_impact']);
    
    if(empty(trim($cand_id))){
        return error422('Enter candidate ID');
    }elseif(empty(trim($poise_and_bearing))){
        return error422('Enter poise and bearing score');
    }elseif(empty(trim($personality_and_projection))){
        return error422('Enter personality and projection score');
    }elseif(empty(trim($appropriateness_and_elegance_of_attire))){
        return error422('Enter appropriateness and elegance of attire score');
    }elseif(empty(trim($overall_impact))){
        return error422('Enter overall impact score');
    }else{
        
        // Generate unique score_id
        do {
            $score_id = rand(100000, 999999);
            $checkQuery = "SELECT score_id FROM formal_wear WHERE score_id = '$score_id'";
            $checkResult = mysqli_query($conn, $checkQuery);
        } while (mysqli_num_rows($checkResult) > 0);
        
        $query = "INSERT INTO formal_wear (score_id, cand_id, poise_and_bearing, personality_and_projection, appropriateness_and_elegance_of_attire, overall_impact)
                  VALUES ('$score_id', '$cand_id', '$poise_and_bearing', '$personality_and_projection', '$appropriateness_and_elegance_of_attire', '$overall_impact')";
        $result = mysqli_query($conn, $query);
        
        if($result){
            $data = [
                'status' => 201,
                'message' => 'Formalwear Score Created Successfully',
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

// READ - Get All Formalwear Scores
function getAllFormalwearScores(){
    global $conn;
    
    $query = "SELECT
                fw.score_id,
                fw.cand_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                fw.poise_and_bearing,
                fw.personality_and_projection,
                fw.appropriateness_and_elegance_of_attire,
                fw.overall_impact,
                fw.total_score
              FROM formal_wear fw
              INNER JOIN contestants c ON fw.cand_id = c.cand_id
              ORDER BY fw.total_score DESC";
    
    $result = mysqli_query($conn, $query);
    
    if($result){
        if(mysqli_num_rows($result) > 0){
            $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
            
            $data = [
                'status' => 200,
                'message' => 'Formalwear Scores Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Formalwear Scores Found',
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

// READ - Get Formalwear Score by score_id
function getFormalwearScores($scoreParams){
    global $conn;
    
    $score_id = mysqli_real_escape_string($conn, $scoreParams['score_id']);
    
    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }
    
    $query = "SELECT
                fw.score_id,
                fw.cand_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                fw.poise_and_bearing,
                fw.personality_and_projection,
                fw.appropriateness_and_elegance_of_attire,
                fw.overall_impact,
                fw.total_score
              FROM formal_wear fw
              INNER JOIN contestants c ON fw.cand_id = c.cand_id
              WHERE fw.score_id = '$score_id' LIMIT 1";
    
    $result = mysqli_query($conn, $query);
    
    if($result){
        if(mysqli_num_rows($result) == 1){
            $res = mysqli_fetch_assoc($result);
            
            $data = [
                'status' => 200,
                'message' => 'Formalwear Score Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Formalwear Scores Found',
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

// READ - Get Formalwear Score by cand_id
function getFormalwearScoreByCandId($scoreParams){
    global $conn;

    $cand_id = mysqli_real_escape_string($conn, $scoreParams['cand_id']);

    if(empty(trim($cand_id))){
        return error422('Enter candidate ID');
    }

    $query = "SELECT
                fw.score_id,
                fw.cand_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                fw.poise_and_bearing,
                fw.personality_and_projection,
                fw.appropriateness_and_elegance_of_attire,
                fw.overall_impact,
                fw.total_score,
                fw.created_at
              FROM formal_wear fw
              INNER JOIN contestants c ON fw.cand_id = c.cand_id
              WHERE fw.cand_id = '$cand_id' LIMIT 1";

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) == 1){
            $res = mysqli_fetch_assoc($result);

            $data = [
                'status' => 200,
                'message' => 'Formalwear Score Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Formalwear Score Found for this Candidate',
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


// UPDATE FORMALWEAR SCORE *ONLY THE CHAIRMAN CAN UPDATE OR EDIT
function updateFormalwearScore($scoreInput){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreInput['score_id']);
    $poise_and_bearing = mysqli_real_escape_string($conn, $scoreInput['poise_and_bearing']);
    $personality_and_projection = mysqli_real_escape_string($conn, $scoreInput['personality_and_projection']);
    $appropriateness_and_elegance_of_attire = mysqli_real_escape_string($conn, $scoreInput['appropriateness_and_elegance_of_attire']);
    $overall_impact = mysqli_real_escape_string($conn, $scoreInput['overall_impact']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }elseif(empty(trim($poise_and_bearing))){
        return error422('Enter poise and bearing score');
    }elseif(empty(trim($personality_and_projection))){
        return error422('Enter personality and projection score');
    }elseif(empty(trim($appropriateness_and_elegance_of_attire))){
        return error422('Enter appropriateness and elegance of attire score');
    }elseif(empty(trim($overall_impact))){
        return error422('Enter overall impact score');
    }else{

        $query = "UPDATE formal_wear SET
                    poise_and_bearing = '$poise_and_bearing',
                    personality_and_projection = '$personality_and_projection',
                    appropriateness_and_elegance_of_attire = '$appropriateness_and_elegance_of_attire',
                    overall_impact = '$overall_impact'
                  WHERE score_id = '$score_id' LIMIT 1";

        $result = mysqli_query($conn, $query);

        if($result){
            $data = [
                'status' => 200,
                'message' => 'Formalwear Score Updated Successfully',
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
function deleteFormalwearScore($scoreInput){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreInput['score_id']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }

    $query = "DELETE FROM formal_wear WHERE score_id = '$score_id' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if($result){
        $data = [
            'status' => 200,
            'message' => 'Formalwear Score Deleted Successfully',
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
