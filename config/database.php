
<?php
$host = "127.0.0.1:3306";
$username = "u378403689_ptci_pageant";
$password = "Ic22025pageant";
$dbname = "u378403689_ptci_pageant";

$conn = mysqli_connect($host, $username, $password, $dbname);

if(!$conn) {
    die("Connection Failed!" . mysqli_connect_error());
}else

?>