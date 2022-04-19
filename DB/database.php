<?php
$host = "ec2-52-18-116-67.eu-west-1.compute.amazonaws.com";
$username = "pjbpyncnrdiios";
$password = "2a52a9fe43a01c80f9497c164be1752c3bb6162b2a69778c4b85a344cbcd6e78";
$dbname = "dfpfbuc1ifm08g";

// Create connection
$conn = mysqli_connect($host, $username, $password, $dbname);
// Check connection
if (!$conn) {
  echo "Error ne lidhjen me databazen. ".mysqli_connect_error()." ".__LINE__;
  exit;
}
?>