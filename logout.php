<?php

// starto session
// session destroy
// header location tek login.php
session_start();
if (!isset($_SESSION['id'])) {


    header('Location: login.php?not-logged-in');
    exit();
}


if ($_POST['action'] === 'logout') {

    session_destroy();
    header('Content-Type: application/json', '', '200');

    echo json_encode(array("status" => 200));

}