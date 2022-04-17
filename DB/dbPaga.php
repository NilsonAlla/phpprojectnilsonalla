<?php
error_reporting(0);
$host = "localhost";
$username = "root";
$password = "1499";
$dbname = "test_paga";

// Create connection
$con = mysqli_connect($host, $username, $password, $dbname);
// Check connection
if (!$con) {
    echo "Error ne lidhjen me databazen. " . mysqli_connect_error() . " " . __LINE__;
    exit;
}
