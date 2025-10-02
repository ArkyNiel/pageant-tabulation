<?php

$host = "localhost";
$username = "root";
$password = "";
$dbname = "ptci_pageant";

$conn = mysqli_connect($host, $username, $password, $dbname);

if(!$conn) {
    die("Connection Failed!" . mysqli_connect_error());
}else

?>