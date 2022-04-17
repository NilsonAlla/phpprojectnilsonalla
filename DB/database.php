<?php
$host = "localhost";
$username = "root";
$password = "1499";
$dbname = "nilsi";

// Create connection
$conn = mysqli_connect($host, $username, $password, $dbname);
// Check connection
if (!$conn) {
  echo "Error ne lidhjen me databazen. ".mysqli_connect_error()." ".__LINE__;
  exit;
}
?>