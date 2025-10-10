<?php
require '../../config/database.php';

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
function storeSwimwareScore($scoreInput){
    global $conn;
    
    $cand_id = mysqli_real_escape_string($conn, $scoreInput['cand_id']);
    $stage_presence = mysqli_real_escape_string($conn, $scoreInput['stage_presence']);
    $figure_fitness = mysqli_real_escape_string($conn, $scoreInput['figure_fitness']);
    $poise_bearing = mysqli_real_escape_string($conn, $scoreInput['poise_bearing']);
    $overall_impact = mysqli_real_escape_string($conn, $scoreInput['overall_impact']);

    if(empty(trim($cand_id))){
        return error422('Enter candidate ID');
    }elseif(empty(trim($stage_presence))){
        return error422('Enter choreography score');
    }elseif(empty(trim($figure_fitness))){
        return error422('Enter figure and fitness score');
    }elseif(empty(trim($poise_bearing))){
        return error422('Enter poise and bearing score');
    }elseif(empty(trim($overall_impact))){
        return error422('Enter overall impact impact score');
    }else{
        
        $query = "INSERT INTO swimware_score (cand_id, stage_presence, figure_fitness, poise_bearing, overall_impact) 
                  VALUES ('$cand_id', '$stage_presence', '$figure_fitness', '$poise_bearing', '$overall_impact')";
        $result = mysqli_query($conn, $query);
        
        if($result){
            $data = [
                'status' => 201,
                'message' => 'Production Score Created Successfully',
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
function getAllProductionScores(){
    global $conn;
    
    $query = "SELECT 
                ts.score_id,
                ts.cand_id,
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
                ts.choreography,
                ts.projection,
                ts.audience_impact
              FROM production_score ts
              INNER JOIN contestants c ON ts.cand_id = c.cand_id
              ORDER BY ts.total_score DESC";
    
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

// READ - Get Talent Score by score_id
function getProductionScore($scoreParams){
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
                ts.choreography,
                ts.projection,
                ts.audience_impact,
                ts.total_score,
                ts.created_at
              FROM production_score ts
              INNER JOIN contestants c ON ts.cand_id = c.cand_id
              WHERE ts.score_id = '$score_id' LIMIT 1";
    
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
                'message' => 'No Production Score Found',
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
function getProductionScoreByCandId($scoreParams){
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
                ts.choreography,
                ts.projection,
                ts.audience_impact
                ts.total_score,
                ts.created_at
              FROM production_score ts
              INNER JOIN contestants c ON ts.cand_id = c.cand_id
              WHERE ts.cand_id = '$cand_id' LIMIT 1";
    
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


// UPDATE TALENT SCORE *ONLY THE CHAIRMAN CAN UPDATE OR EDIT 
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
    }elseif(empty(trim($projectiony))){
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