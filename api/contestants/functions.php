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


// create
function storeContestant($contestantInput) {
    global $conn;

    $cand_number = mysqli_real_escape_string($conn, $contestantInput['cand_number']);
    $cand_name = mysqli_real_escape_string($conn, $contestantInput['cand_name']);
    $cand_team = mysqli_real_escape_string($conn, $contestantInput['cand_team']);
    $cand_gender = mysqli_real_escape_string($conn, $contestantInput['cand_gender']);

    // Validate team color
    $validTeams = ['red', 'yellow', 'green', 'purple', 'blue'];
    if(!in_array($cand_team, $validTeams)){
        $data = [
            'status' => 400,
            'message' => 'Invalid team color. Must be: red, yellow, green, purple, or blue',
        ];
        header("HTTP/1.0 400 Bad Request");
        return json_encode($data);
    }

    // random id
    do {
        $cand_id = rand(100000, 999999);
        $checkQuery = "SELECT cand_id FROM contestants WHERE cand_id = '$cand_id'";
        $checkResult = mysqli_query($conn, $checkQuery);
    } while (mysqli_num_rows($checkResult) > 0);

    $query = "INSERT INTO contestants (cand_id, cand_number, cand_name, cand_team, cand_gender)
              VALUES ('$cand_id', '$cand_number', '$cand_name', '$cand_team', '$cand_gender')";

    $result = mysqli_query($conn, $query);

    if($result){
        $data = [
            'status' => 201,
            'message' => 'Contestant created successfully',
            'cand_id' => $cand_id
        ];
        header("HTTP/1.0 201 Created");
        return json_encode($data);
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}

//read 
function getContestantsList() {
    global $conn;

    $query = "SELECT * FROM contestants ORDER BY cand_number ASC";
    $query_run = mysqli_query($conn, $query);

    if($query_run){
        if(mysqli_num_rows($query_run) > 0){
             $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

             $data = [
                'status' => 200,
                'message' => 'contestants List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
             
        }else {
            $data = [
                'status' => 404,
                'message' => 'No Contestants Found', 
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

// update 
function updateContestant($contestantInput){
    global $conn;
    
    $cand_id = mysqli_real_escape_string($conn, $contestantInput['cand_id']);
    $cand_number = mysqli_real_escape_string($conn, $contestantInput['cand_number']);
    $cand_name = mysqli_real_escape_string($conn, $contestantInput['cand_name']);
    $cand_team = mysqli_real_escape_string($conn, $contestantInput['cand_team']);
    $cand_gender = mysqli_real_escape_string($conn, $contestantInput['cand_gender']);

    // Validation 
    if(empty(trim($cand_id))){
        return error422('Enter contestant ID');
    }elseif(empty(trim($cand_number))){
        return error422('Enter contestant number');
    }elseif(empty(trim($cand_name))){
        return error422('Enter contestant name');
    }elseif(empty(trim($cand_team))){
        return error422('Enter contestant team');
    }elseif(empty(trim($cand_gender))){
        return error422('Enter contestant gender');
    }else {
        // Validate team color
        $validTeams = ['red', 'yellow', 'green', 'purple', 'blue'];
        if(!in_array(strtolower($cand_team), $validTeams)){
            return error422('Invalid team color. Must be: red, yellow, green, purple, or blue');
        }
        
        // Check
        $checkQuery = "SELECT * FROM contestants WHERE cand_id='$cand_id'";
        $checkResult = mysqli_query($conn, $checkQuery);
        
        if(mysqli_num_rows($checkResult) == 0){
            $data = [
                'status' => 404,
                'message' => 'Contestant not found',
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }
        
        $query = "UPDATE contestants SET 
                  cand_number='$cand_number', 
                  cand_name='$cand_name', 
                  cand_team='$cand_team', 
                  cand_gender='$cand_gender' 
                  WHERE cand_id='$cand_id'";
        $result = mysqli_query($conn, $query);

        if($result){ 
            $data = [
                'status' => 200,
                'message' => 'Contestant Updated Successfully',
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

// delete
function deleteContestant($candInput){
    global $conn;

    if(empty($candInput['cand_id'])){
        return error422('Contestant ID is required');
    }

    $candId = $candInput['cand_id'];
    
    // Check if contestant exists
    $checkStmt = $conn->prepare("SELECT cand_id FROM contestants WHERE cand_id = ?");
    $checkStmt->bind_param("i", $candId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if($checkResult->num_rows == 0){
        $data = [
            'status' => 404,
            'message' => 'Contestant Not Found',
        ];
        header("HTTP/1.0 404 Not Found");
        return json_encode($data);
    }
    
    // Delete the contestant
    $stmt = $conn->prepare("DELETE FROM contestants WHERE cand_id = ?");
    $stmt->bind_param("i", $candId);
    $result = $stmt->execute();
    
    if($result){
        $data = [
            'status' => 200,
            'message' => 'Contestant Deleted Successfully',
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
?>