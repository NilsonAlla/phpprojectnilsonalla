<?php
session_start();
error_reporting(E_ERROR);
require_once "DB/database.php";
require_once 'Helper.php';


$errors = [];
$loginerrors = [];
$userRole = "user";

if ($_POST['action'] == "register") {
    $firstname = $conn->escape_string($_POST['firstname']);
    $lastname = $conn->escape_string($_POST['lastname']);
    $email = $conn->escape_string($_POST['email']);
    $birthday = $conn->escape_string($_POST['birthday']);
    $password = $conn->escape_string($_POST['password']);
    $confirmPassword = $conn->escape_string($_POST['confirm_password']);
    $terms = $conn->escape_string($_POST['terms']);
    $bday = strtotime($birthday);
    $hashpass = password_hash($password, PASSWORD_BCRYPT);

    $photo = 'foto/default.jpg';
    $photopath = mysqli_real_escape_string($conn, $photo);


    //validim i emrit

    if (!ctype_alpha($firstname) || strlen($firstname) < 3) {
        $errors['firstname'] = "Emri duhet te permbaje vetem karaktere dhe nuk duhet te jete me i vogel se 3 karaktere! ";
    }


    if (!ctype_alpha($lastname) || strlen($lastname) < 3) {
        $errors['lastname'] = "Mbiemri duhet te permbaje vetem karaktere dhe nuk duhet te jete me i vogel se 3 karaktere! ";
    }

    //validim i password

    if (!preg_match('/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{8,20}$/', $password)) {
        $errors['password'] = "Password nuk permban nje nga keto kushte : Te pakten : 8 karaktere ,nje shkronje e madhe , nje numer dhe nje karakter special (!@#$%^&*)";
    }


    if ($password != $confirmPassword) {
        $errors['confirm_password'] = "Password nuk perkon me confirmimin e password! ";
    }
    //validimi i terms

    if (!(bool)$terms) {
        $errors['terms'] = "Ju lutem pranoni Terms and Policy";
    }


    //validim i dates


    $now = time();


    $d = $now - $bday;

    if ($d < 18 * 60 * 60 * 24 * 30 * 12) {
        $errors['birthday'] = "nuk lejohet rregjistrimi nen moshen 18 vjec! ";
    } else if (strlen($birthday) === 0) {
        $errors['birthday'] = "vendos daten";
    }

    //validimi i email

    $sqlRead = "SELECT email FROM user WHERE email='" . $email . "'";
    $result = $conn->query($sqlRead);
    if(!$result){
        header('Content-Type: application/json', '', '500');
        echo json_encode(['message' => 'Server Error.',]);
        exit();
    }
    $count = $result->num_rows;

    // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    //       $errors[]= "Invalid email format";
    // }

    if ($count > 0) {
        $errors['email'] = "Eshte e hapur nje llogari me kete email";
    } else if (strlen($email) === 0) {
        $errors['email'] = "email muk duhet te jete i pashkruajtur";
    }

    if (sizeof($errors)) {
        Helper::echoAndExit(Helper::response(['message' => 'The given data is invalid.', 'errors' => $errors], 422));
        exit();
    }

    $birthday = date('Y-m-d', $bday);
    $sqlInsert = "INSERT INTO user (firstname, lastname, email,birthday,password,post,photopath)
    VALUES ('$firstname', '$lastname', '$email','$birthday','$hashpass','$userRole','$photopath')";

    if ($conn->query($sqlInsert) === TRUE) {
        header('Content-Type: application/json', '', 200);
        echo json_encode(['message' => 'Success.',]);
        exit();
    } else {
        header('Content-Type: application/json', '', '500');
        echo json_encode(['message' => 'Server Error.',]);
        exit();
    }
} /**
 * Shermbimi i login
 */

else if ($_POST['action'] == "login") {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $hashuserpass = password_hash($password, PASSWORD_BCRYPT);

    $query = "SELECT id,firstname,lastname,email,birthday,password,photopath, post FROM user WHERE email ='" . $email . "'";
    $result = mysqli_query($conn, $query);
    if(!$result){
        header('Content-Type: application/json', '', '500');
        echo json_encode(['message' => 'Server Error.',]);
        exit();
    }


    if (mysqli_num_rows($result) < 1) {
        header('Content-Type: application/json', '', '422');
        $errors['email'] = "Ky email nuk eshte i regjistruar me pare!";
        echo json_encode(array('message' => 'The given data is invalid.', 'errors' => $errors));
        exit;
    }

    $user = mysqli_fetch_assoc($result);
//    print_r($user);
//    exit;

    if (!password_verify($password, $user['password'])) {
        $errors['password'] = "password i vendosur nuk eshte i sakte";

    } else if (sizeof($errors)) {
        header('Content-Type: application/json', '', '422');
        echo json_encode(['message' => 'The given data is invalid.', 'errors' => $errors]);
        exit;
    }

    /** Set Session Variables */
    $_SESSION['id'] = $user['id'];
    $_SESSION['post'] = $user['post'];
    $_SESSION['firstname'] = $user['firstname'];
    $_SESSION['lastname'] = $user['lastname'];
    $_SESSION['lastname'] = $user['lastname'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['birthday'] = $user['birthday'];
    $_SESSION['photopath'] = $user['photopath'];
    header('Content-Type: application/json', '', '200');
    echo json_encode(array("status" => 200, "data" => $errors));

}

