<?php

$host = "localhost";
$username = "root";
$password = "";
$dbname = "pageant_nimisis";

$conn = mysqli_connect($host, $username, $password, $dbname);

if(!$conn) {
    die("Connection Failed!" . mysqli_connect_error());
}else

?>