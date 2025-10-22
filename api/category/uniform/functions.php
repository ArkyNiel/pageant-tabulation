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


<<<<<<< HEAD
// store uniform score
function storeUniformScore($scoreInput){
    global $conn;
    
    $cand_id = mysqli_real_escape_string($conn, $scoreInput['cand_id']);
    $poise_and_bearing = mysqli_real_escape_string($conn, $scoreInput['poise_and_bearing']);
    $personality_and_projection = mysqli_real_escape_string($conn, $scoreInput['personality_and_projection']);
    $neatness = mysqli_real_escape_string($conn, $scoreInput['neatness']);
    $overall_impact = mysqli_real_escape_string($conn, $scoreInput['overall_impact']);
    
    if(empty(trim($cand_id))){
        return error422('Enter candidate ID');
    }elseif(empty(trim($poise_and_bearing))){
        return error422('Enter poise and bearing score');
    }elseif(empty(trim($personality_and_projection))){
        return error422('Enter personality and projection score');
    }elseif(empty(trim($neatness))){
        return error422('Enter neatness score');
    }elseif(empty(trim($overall_impact))){
        return error422('Enter overall impact score');
=======
// store talent store
function storeUniformScore($scoreInput){
    global $conn;
    
    $cand_id = mysqli_real_escape_string($conn, $scoreInput['cand_id']);
    $poise_and_bearings = mysqli_real_escape_string($conn, $scoreInput['poise_and_bearings']);
    $figure_and_fitness = mysqli_real_escape_string($conn, $scoreInput['figure_and_fitness']);
    $poise_ = mysqli_real_escape_string($conn, $scoreInput['overall_impression']);
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
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
    }else{
        
        // Generate unique score_id
        do {
            $score_id = rand(100000, 999999);
<<<<<<< HEAD
            $checkQuery = "SELECT score_id FROM uniform_Score WHERE score_id = '$score_id'";
            $checkResult = mysqli_query($conn, $checkQuery);
        } while (mysqli_num_rows($checkResult) > 0);
        
        $query = "INSERT INTO uniform_Score (score_id, cand_id, poise_and_bearing, personality_and_projection, neatness, overall_impact) 
                  VALUES ('$score_id', '$cand_id', '$poise_and_bearing', '$personality_and_projection', '$neatness', '$overall_impact')";
=======
            $checkQuery = "SELECT score_id FROM uniform_score WHERE score_id = '$score_id'";
            $checkResult = mysqli_query($conn, $checkQuery);
        } while (mysqli_num_rows($checkResult) > 0);
        
        $query = "INSERT INTO talent_score (score_id, cand_id, mastery, performance_choreography, overall_impression, audience_impact) 
                  VALUES ('$score_id', '$cand_id', '$mastery', '$performance_choreography', '$overall_impression', '$audience_impact')";
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
        $result = mysqli_query($conn, $query);
        
        if($result){
            $data = [
                'status' => 201,
<<<<<<< HEAD
                'message' => 'Uniform Score Created Successfully',
=======
                'message' => 'Talent Score Created Successfully',
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
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

<<<<<<< HEAD
// READ - Get All Uniform Scores
function getAllUniformScores(){
    global $conn;
    
    $query = "SELECT
                us.score_id,
                us.cand_id,
=======
// READ - Get All Talent Scores
function getAllTalentScores(){
    global $conn;
    
    $query = "SELECT
                ts.score_id,
                ts.cand_id,
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
<<<<<<< HEAD
                us.poise_and_bearing,
                us.personality_and_projection,
                us.neatness,
                us.overall_impact,
                us.total_score
              FROM uniform_Score us
              INNER JOIN contestants c ON us.cand_id = c.cand_id
              ORDER BY us.total_score DESC";
=======
                ts.mastery,
                ts.performance_choreography,
                ts.overall_impression,
                ts.audience_impact,
                ts.total_score
              FROM talent_score ts
              INNER JOIN contestants c ON ts.cand_id = c.cand_id
              ORDER BY ts.total_score DESC";
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
    
    $result = mysqli_query($conn, $query);
    
    if($result){
        if(mysqli_num_rows($result) > 0){
            $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
            
            $data = [
                'status' => 200,
<<<<<<< HEAD
                'message' => 'Uniform Scores Fetched Successfully',
=======
                'message' => 'Talent Scores Fetched Successfully',
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
<<<<<<< HEAD
                'message' => 'No Uniform Scores Found',
=======
                'message' => 'No Talent Scores Found',
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
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

<<<<<<< HEAD
// READ - Get Uniform Score by score_id
function getUniformScores($scoreParams){
=======
// READ - Get Talent Score by score_id
function getTalentScores($scoreParams){
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
    global $conn;
    
    $score_id = mysqli_real_escape_string($conn, $scoreParams['score_id']);
    
    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }
    
    $query = "SELECT
<<<<<<< HEAD
                us.score_id,
                us.cand_id,
=======
                ts.score_id,
                ts.cand_id,
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
<<<<<<< HEAD
                us.poise_and_bearing,
                us.personality_and_projection,
                us.neatness,
                us.overall_impact,
                us.total_score
              FROM uniform_Score us
              INNER JOIN contestants c ON us.cand_id = c.cand_id
              WHERE us.score_id = '$score_id' LIMIT 1";
=======
                ts.mastery,
                ts.performance_choreography,
                ts.overall_impression,
                ts.audience_impact,
                ts.total_score
              FROM talent_score ts
              INNER JOIN contestants c ON ts.cand_id = c.cand_id
              WHERE ts.score_id = '$score_id' LIMIT 1";
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
    
    $result = mysqli_query($conn, $query);
    
    if($result){
        if(mysqli_num_rows($result) == 1){
            $res = mysqli_fetch_assoc($result);
            
            $data = [
                'status' => 200,
<<<<<<< HEAD
                'message' => 'Uniform Score Fetched Successfully',
=======
                'message' => 'Talent Score Fetched Successfully',
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
<<<<<<< HEAD
                'message' => 'No Uniform Scores Found',
=======
                'message' => 'No Talent Scores Found',
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
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

<<<<<<< HEAD
// READ - Get Uniform Score by cand_id
function getUniformScoreByCandId($scoreParams){
=======
// READ - Get Talent Score by cand_id
function getTalentScoreByCandId($scoreParams){
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
    global $conn;

    $cand_id = mysqli_real_escape_string($conn, $scoreParams['cand_id']);

    if(empty(trim($cand_id))){
        return error422('Enter candidate ID');
    }

    $query = "SELECT
<<<<<<< HEAD
                us.score_id,
                us.cand_id,
=======
                ts.score_id,
                ts.cand_id,
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
                c.cand_number,
                c.cand_name,
                c.cand_team,
                c.cand_gender,
<<<<<<< HEAD
                us.poise_and_bearing,
                us.personality_and_projection,
                us.neatness,
                us.overall_impact,
                us.total_score,
                us.created_at
              FROM uniform_Score us
              INNER JOIN contestants c ON us.cand_id = c.cand_id
              WHERE us.cand_id = '$cand_id' LIMIT 1";
=======
                ts.mastery,
                ts.performance_choreography,
                ts.overall_impression,
                ts.audience_impact,
                ts.total_score,
                ts.created_at
              FROM talent_score ts
              INNER JOIN contestants c ON ts.cand_id = c.cand_id
              WHERE ts.cand_id = '$cand_id' LIMIT 1";
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697

    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) == 1){
            $res = mysqli_fetch_assoc($result);

            $data = [
                'status' => 200,
<<<<<<< HEAD
                'message' => 'Uniform Score Fetched Successfully',
=======
                'message' => 'Talent Score Fetched Successfully',
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 404,
<<<<<<< HEAD
                'message' => 'No Uniform Score Found for this Candidate',
=======
                'message' => 'No Talent Score Found for this Candidate',
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
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


<<<<<<< HEAD
// UPDATE UNIFORM SCORE *ONLY THE CHAIRMAN CAN UPDATE OR EDIT
function updateUniformScore($scoreInput){
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreInput['score_id']);
    $poise_and_bearing = mysqli_real_escape_string($conn, $scoreInput['poise_and_bearing']);
    $personality_and_projection = mysqli_real_escape_string($conn, $scoreInput['personality_and_projection']);
    $neatness = mysqli_real_escape_string($conn, $scoreInput['neatness']);
    $overall_impact = mysqli_real_escape_string($conn, $scoreInput['overall_impact']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }elseif(empty(trim($poise_and_bearing))){
        return error422('Enter poise and bearing score');
    }elseif(empty(trim($personality_and_projection))){
        return error422('Enter personality and projection score');
    }elseif(empty(trim($neatness))){
        return error422('Enter neatness score');
    }elseif(empty(trim($overall_impact))){
        return error422('Enter overall impact score');
    }else{

        $query = "UPDATE uniform_Score SET
                    poise_and_bearing = '$poise_and_bearing',
                    personality_and_projection = '$personality_and_projection',
                    neatness = '$neatness',
                    overall_impact = '$overall_impact'
=======
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
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
                  WHERE score_id = '$score_id' LIMIT 1";

        $result = mysqli_query($conn, $query);

        if($result){
            $data = [
                'status' => 200,
<<<<<<< HEAD
                'message' => 'Uniform Score Updated Successfully',
=======
                'message' => 'Talent Score Updated Successfully',
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
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
<<<<<<< HEAD
function deleteUniformScore($scoreInput){
=======
function deleteTalentScore($scoreInput){
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
    global $conn;

    $score_id = mysqli_real_escape_string($conn, $scoreInput['score_id']);

    if(empty(trim($score_id))){
        return error422('Enter score ID');
    }

<<<<<<< HEAD
    $query = "DELETE FROM uniform_Score WHERE score_id = '$score_id' LIMIT 1";
=======
    $query = "DELETE FROM talent_score WHERE score_id = '$score_id' LIMIT 1";
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
    $result = mysqli_query($conn, $query);

    if($result){
        $data = [
            'status' => 200,
<<<<<<< HEAD
            'message' => 'Uniform Score Deleted Successfully',
=======
            'message' => 'Talent Score Deleted Successfully',
>>>>>>> a799517880cc3e552f03663ae6ea119f21f15697
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
