<?php
$localhost = "localhost";
$user = "root";
$pass = "";
$bd = "empresa";

$conn = new mysqli($localhost, $user, $pass, $bd);
if ($conn->connect_error) {
    echo json_encode("Connection failed: " . $conn->connect_error);
}

return $conn;