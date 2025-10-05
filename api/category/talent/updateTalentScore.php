<?php
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
?>