<?php

session_start();
error_reporting(E_ERROR);
require_once "../DB/database.php";
require_once '../Helper.php';

if (!isset($_SESSION['id']) || strtoupper($_SESSION['post']) != "ADMIN") {
    header('Location: ../login.php?not-logged-in');
    exit();
}


$errors=[];
$id = $_SESSION['user'] ;

if ($_POST['action'] == 'shtimdata'){
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn , $_POST['lastname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $birthday = mysqli_real_escape_string($conn, $_POST['birthday']);
    $photopath = mysqli_real_escape_string($conn, $_POST['photo_path']);

    /**
     * formatim i dates
     */

    $birthday = date("Y-m-d",strtotime($birthday));


    /*
     * validimi i emrit dhe mbiemrit
     */

    if (!$_POST['firstname']){
        $errors['firstname']  = 'First name is required!';
    }else if (!ctype_alpha($_POST['firstname']) || strlen($_POST['firstname']) <3){
        $errors['firstname'] = 'First  name must be  3 char or longer and letters only';
    }

    if (!$_POST['lastname']){
        $errors['lastname']  = 'Last name is required!';
    }else if (!ctype_alpha($_POST['lastname']) || strlen($_POST['lastname']) <3){
        $errors['lastname'] = 'Last  name must be  3 char or longer and letters only';
    }


    //validim i dates

    $bday = strtotime($_POST['birthday']);
    $now = time();

    $d = $now - $bday;

    if ($d < 18 * 60 * 60 * 24 * 30 * 12) {
        $errors['birthday'] = "nuk lejohet rregjistrimi nen moshen 18 vjec! ";
    }else if (strlen($bday) === 0 ){
        $errors['birthday'] = "vendos daten";
    }

    /*
     * validimi i fotos
     */

    /*
     * shikojme nese ka te upload-uar nje foto me pare
     */
$queryPhoto = "SELECT photopath FROM user where id = '".$_SESSION['id']."'";
    $photoResult = $conn->query($queryPhoto);

    /*
    * kontrollojme nese eshte ekzekutuar query apo jo
    */
    if(!$photoResult){
        header('Content-Type: application/json', '', '500');
        echo json_encode(['message' => 'Database Error.',]);
        exit();
    }
    $photoDB = array();

    if (mysqli_num_rows($photoResult) != 0) {
        while ($row = mysqli_fetch_assoc($photoResult)) {
            $photoDB['path'] = $row['photopath'];
        }
    }



/*
 * kontrollojme foton
 */

    if (count($_FILES) == 0 && mysqli_num_rows($photoResult) == 0) {
    $errors['photo']  = 'Poto can not be empty!';
}else if (count($_FILES) != 0) {

        $target_dir = "C:/xampp/htdocs/nilsi/WD1/foto/";
        $photopath = $target_dir.getdate()[0] . basename($_FILES["photo"]["name"]);
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false) {

            $uploadOk = 1;
        } else {
            $errors['photopath'] = "File is not an image.";
            $uploadOk = 0;
            exit();
        }


        $imageFileType = strtolower(pathinfo($photopath, PATHINFO_EXTENSION));
        $uploadedfilepath = mysqli_real_escape_string($conn, $photopath);

// Check if file already exists


        if (file_exists($photopath)) {
            $errors['photopath'] = "Sorry, file already exists.";
            $uploadOk = 0;

        } else if ($imageFileType !== "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            $errors['photopath'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;

        } else if ($uploadOk == 0) {
            $errors['photopath'] = "Sorry, your file was not uploaded.";

        }

        if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $photopath)) {
            echo "Sorry, there was an error uploading your file.";

        }
/*
 * i kalojme $photopath  pathin e fotos per ta ruajtur ate me pas ne db
 */
        $photopathdb = basename(dirname($photopath)) . "/" . basename($photopath);
    } else {
        $photopathdb = $photoDB['path'];
    }

/*
 * kontrollojme nese kemi pasur ndonje error ne validimin e te dhenave
 */
    if (sizeof($errors)) {
        Helper::echoAndExit(Helper::response(['message' => 'The given data is invalid.', 'errors' => $errors], 422));
        exit();
    }

    $queryphotopost = "UPDATE user SET 
                firstname = '".$firstname."',
                lastname = '".$lastname."',
                email = '".$email."',
                birthday = '".$birthday."',
                photopath = '".$photopathdb."'
                WHERE id='".$_SESSION['id']."'" ;

    $photoquery = mysqli_query($conn, $queryphotopost);

/*
 * kontrollojme nese kuery eshte ekzekutuar apo jo
 */
    if (!$photoquery  ) {

        header('Content-Type: application/json', '', '422');
        echo json_encode([
            'message' => 'photo upload Error.',
        ]);
        exit();
    } else {
        header('Content-Type: application/json', '', 200);
        echo json_encode(array("status" => 200, "data" => $errors));
        exit();
    }
}
