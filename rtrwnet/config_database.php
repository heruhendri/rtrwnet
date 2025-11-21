<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'billingrtrwnet';
$db_port = 3306;

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
$mysqli->set_charset('utf8mb4');

if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}
?>