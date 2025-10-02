<?php
require '../../config/database.php';

function error422($message){
    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity"); 
    echo json_encode($data);
    exit();
}

function storeUsers($userInput){
    global $conn;
    
    $username = mysqli_real_escape_string($conn, $userInput['username']);
    $password = mysqli_real_escape_string($conn, $userInput['password']);
    $role = mysqli_real_escape_string($conn, $userInput['role']);

    // Validation with specific error messages
    if(empty(trim($username))){
        return error422('Enter your username');
    }elseif(empty(trim($password))){
        return error422('Enter your password');
    }elseif(empty(trim($role))){
        return error422('Enter your role');
    }else {
        //hashed
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (username,password,role) VALUES ('$username','$hashedPassword','$role')";
        $result = mysqli_query($conn, $query);

        if($result){ 
            $data = [
                'status' => 201,
                'message' => 'User Created Successfully',
            ];
            header("HTTP/1.0 201 Created");  // Fixed
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

function getUsersList() {
    global $conn;

    $query = "SELECT * FROM users";
    $query_run = mysqli_query($conn, $query);

    if($query_run){
        if(mysqli_num_rows($query_run) > 0){
             $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

             $data = [
                'status' => 200,
                'message' => 'Users List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
             
        }else {
            $data = [
                'status' => 404,
                'message' => 'No Users Found', 
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