<?php
$host = 'MySQL-8.2';
$username = 'root';
$password = ''; 
$database = 'parkzone';


$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Ошибка соединения: " . $conn->connect_error);
}
?>