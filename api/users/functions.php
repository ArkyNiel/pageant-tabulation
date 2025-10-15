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
function storeUsers($userInput){
    global $conn;

    $username = mysqli_real_escape_string($conn, $userInput['username']);
    $password = mysqli_real_escape_string($conn, $userInput['password']);
    $role = mysqli_real_escape_string($conn, $userInput['role']);

    // Validation
    if(empty(trim($username))){
        return error422('Enter your username');
    }elseif(empty(trim($password))){
        return error422('Enter your password');
    }elseif(empty(trim($role))){
        return error422('Enter your role');
    }else {
        do {
            $id = rand(100000, 999999);
            $checkQuery = "SELECT id FROM users WHERE id = '$id'";
            $checkResult = mysqli_query($conn, $checkQuery);
        } while (mysqli_num_rows($checkResult) > 0);

        //hashed
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (id, username,password,role) VALUES ('$id', '$username','$hashedPassword','$role')";
        $result = mysqli_query($conn, $query);

        if($result){
            $data = [
                'status' => 201,
                'message' => 'User Created Successfully',
                'id' => $id
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

// read
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

// delete
function deleteUser($userInput){
    global $conn;
    
    if(empty($userInput['id'])){
        return error422('User ID is required');
    }
    
    $userId = $userInput['id'];
    
    // Check if user exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $checkStmt->bind_param("i", $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if($checkResult->num_rows == 0){
        $data = [
            'status' => 404,
            'message' => 'User Not Found',
        ];
        header("HTTP/1.0 404 Not Found");
        return json_encode($data);
    }
    
    // Delete the user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $result = $stmt->execute();
    
    if($result){
        $data = [
            'status' => 200,
            'message' => 'User Deleted Successfully',
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

// uodate
function updateUser($userInput){
    global $conn;
    
    if(empty($userInput['id'])){
        return error422('User ID is required');
    }
    
    $userId = mysqli_real_escape_string($conn, $userInput['id']);
    
    // if the users exists
    $checkQuery = "SELECT id FROM users WHERE id='$userId'";
    $checkResult = mysqli_query($conn, $checkQuery);
    
    if(mysqli_num_rows($checkResult) == 0){
        $data = [
            'status' => 404,
            'message' => 'User Not Found',
        ];
        header("HTTP/1.0 404 Not Found");
        return json_encode($data);
    }
    
    // Build update query
    $updateFields = [];
    
    if(isset($userInput['username']) && !empty(trim($userInput['username']))){
        $username = mysqli_real_escape_string($conn, $userInput['username']);
        $updateFields[] = "username='$username'";
    }
    
    if(isset($userInput['password']) && !empty(trim($userInput['password']))){
        $password = mysqli_real_escape_string($conn, $userInput['password']);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateFields[] = "password='$hashedPassword'";
    }
    
    if(isset($userInput['role']) && !empty(trim($userInput['role']))){
        $role = mysqli_real_escape_string($conn, $userInput['role']);
        $updateFields[] = "role='$role'";
    }
    
    if(empty($updateFields)){
        return error422('No valid fields to update');
    }
    
    $query = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id='$userId'";
    $result = mysqli_query($conn, $query);
    
    if($result){
        $data = [
            'status' => 200,
            'message' => 'User Updated Successfully',
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