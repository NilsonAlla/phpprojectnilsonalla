<?php
/**
 * Funksioni qe kthen 0 nese ka nje error ne server side ose ne rastin kur nga aplikimi i filtrave nuk ka asnje rekord ne server side
 * Si parameter i kalohet error. Nese thirret pa parameter atehere error behet 0, ne te kundert error merr vleren
 * qe i kalohet si parameter
 */
error_reporting(E_ERROR);
session_start();

require_once "../DB/database.php";

if (!isset($_SESSION['id'])) {

    header('Location: ../login.php?not-logged-in');
    exit();
}
if (strtoupper($_SESSION['post']) != "ADMIN") {


    header('Location: forbidden.php');
    exit();
}


$error = [];
function empty_data($total_records, $error = "")
{
    $response = array("draw" => intval($draw), "iTotalRecords" => $total_records, "iTotalDisplayRecords" => 0, "aaData" => array(), "error" => $error,);
    echo json_encode($response);
    exit;
}


if ($_POST['action'] == "server_side_list") {

    $draw = mysqli_real_escape_string($conn,$_POST['draw']);
    $limit_start = mysqli_real_escape_string($conn,$_POST['start']);
    $limit_end = mysqli_real_escape_string($conn,$_POST['length']);
    $columnIndex = mysqli_real_escape_string($conn,$_POST['order'][0]['column']);
    $columnName = mysqli_real_escape_string($conn,$_POST['columns'][$columnIndex]['data']);
    $columnSortOrder = mysqli_real_escape_string($conn,$_POST['order'][0]['dir']);
    $urlData = mysqli_real_escape_string($conn,$_POST['urlData']);
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $userdate = mysqli_real_escape_string($conn,$_POST['date']);


/*
 * krijimi i query me te dhenat e ids qe kapet nga $_GET
 */
    $setParametersFilter ="";
    if ($urlData != ''){
        $urlData = substr($urlData, 0, -1);
        $urlData = str_replace(";","','","'".$urlData."'");
        $setParametersFilter = " AND id in (".$urlData.")";
    }

    /*
 * krijimi i query me te dhenat e firstname ose lastname
 */
    $nameFlt = "";
    if (isset($_POST['fullname'])) {
        $names = implode("','", $fullname);
        $names = "'{$names}'";
        $nameFlt = "AND ( firstname in (" . $names . ") OR lastname in (" . $names . "))";
    }

    /*
     * krijimi i query me te dhenat e email
     */
    $emailFlt = '';
    if (isset($_POST['email'])) {
        $email = implode("','", $email);
        $email = "'{$email}'";
        $emailFlt = "And email in (" . $email . ")";

    }

    /*
    * krijimi i query me te dhenat e dates
    */
    $date_flt = '';
    if (isset($_POST['date'])) {

        $dates = explode('-', $userdate);
        $date1 = mysqli_real_escape_string($conn, date("Y-m-d", strtotime($dates[0])));
        $date2 = mysqli_real_escape_string($conn, date("Y-m-d", strtotime($dates[1])));

        $date_flt = " AND birthday >= '" . $date1 . "'  AND birthday <= '" . $date2 . "'";

    }



    $searchValue = mysqli_real_escape_string($conn, $_POST['search']['value']);
    $searchQuery = " ";
    if ($searchValue != '') {
        $searchQuery = " AND (firstname LIKE '%" . $searchValue . "%' OR 	
            lastname LIKE '%" . $searchValue . "%' OR
            birthday LIKE '%" . $searchValue . "%' OR 
            email LIKE '%" . $searchValue . "%' OR
            post LIKE '%" . $searchValue . "%')";
    }
    // Rsati kur zgjidhet All duhet te hiqen te gjitha limitimet ne pagination
    if ($limit_end == -1) {
        $pagination = "";
    } else {
        $pagination = "LIMIT " . $limit_start . ", " . $limit_end;
    }

    /**
     * Merr numrin total te rekordeve pa aplikuar filtrat. Psh kur shfaqim 10/30 rekorde,
     * numrin tital e marrim permes ketij query
     */
    $query_without_ftl = "SELECT COUNT(*) AS allcount 
                          FROM user where 1 = 1  $date_flt $nameFlt $emailFlt $setParametersFilter ";
    $reuslt_without_ftl = mysqli_query($conn, $query_without_ftl);

    /*
    * kontrollojme nese eshte ekzekutuar query apo jo
    */
    if(!$reuslt_without_ftl){
        header('Content-Type: application/json', '', '500');
        echo json_encode(['message' => 'Database Error.',]);
        exit();
    }

    $records = mysqli_fetch_assoc($reuslt_without_ftl);
    $totalRecords = $records['allcount'];

    /**
     * Numrin total te rekordeve duke aplikuar filtrin search
     */
    $query_with_ftl = "SELECT COUNT(*) AS allcount 
                       FROM  user 
                       where 1=1 $date_flt $nameFlt $emailFlt $setParametersFilter
                             and (id like '%" . $searchValue . "%' 
                             OR firstname like '%" . $searchValue . "%' 
                             OR lastname like '%" . $searchValue . "%' 
                             OR email like '%" . $searchValue . "%' 
                             OR birthday like '%" . $searchValue . "%' 
                             OR post like '%" . $searchValue . "%')";

    $result_with_ftl = mysqli_query($conn, $query_with_ftl);

    /*
    * kontrollojme nese eshte ekzekutuar query apo jo
    */
    if(!$result_with_ftl){
        header('Content-Type: application/json', '', '500');
        echo json_encode(['message' => 'Database Error.',]);
        exit();
    }

    $records_with_ftl = mysqli_fetch_assoc($result_with_ftl);
    $totalRecordwithFilter = $records_with_ftl['allcount'];

    /**
     * Merren te dhenat qe do analizohen dhe do behet llogaritja perkatese
     * Behet perllogaritja e te dhenave ne vektorin data
     */
    $queryData = "SELECT id,
                          firstname,
                          lastname,
                          email,
                          birthday,
                          post 
                   FROM user where 1 = 1  $date_flt $nameFlt $emailFlt $setParametersFilter
                   $searchQuery  ORDER BY  $columnName $columnSortOrder $pagination";
    $resultData = mysqli_query($conn, $queryData);

    /*
    * kontrollojme nese eshte ekzekutuar query apo jo
    */
    if(!$resultData){
        header('Content-Type: application/json', '', '500');
        echo json_encode(['message' => 'Database Error.',]);
        exit();
    }

/*
 * analizimi i te dhenave te marre me lart
 */
    $data = array();
    while ($row = mysqli_fetch_assoc($resultData)) {
        $data[$row['id']]['id'] = $row['id'];
        $data[$row['id']]['firstname'] = $row['firstname'];
        $data[$row['id']]['lastname'] = $row['lastname'];
        $data[$row['id']]['email'] = $row['email'];
        $data[$row['id']]['birthday'] = $row['birthday'];
        $data[$row['id']]['post'] = $row['post'];
    }
    /**
     * Pershtasim te dhenat sipas formatit qe i do datatable ne front-end
     */
    foreach ($data as $key => $row) {
        $table_data[] = array("id" => $row['id'],
            "firstname" => $row['firstname'],
            "lastname" => $row['lastname'],
            "email" => $row['email'],
            "birthday" => $row['birthday'],
            "post" => $row['post'],
            "action" => "<div class='row'>
                            <nobr>
                                <button id='usereditid' onclick='fillmodal(" . $row['id'] . ")' type='button' class='btn btn-success' data-toggle='modal' data-target='#updateModal'>EDIT</button>
                                <button id='userdeleteid' type='button' class='btn btn-danger' onclick='deleteusermodal(" . $row['id'] . ")' data-toggle='modal' data-target='#deleteModal'>DELETE</button>
                           </nobr>
                         </div> "

        );
    }


    /**
     * Dergojme te dhenat ne front nese nuk ka te dhena per te shmangur erroretne front
     */
    if ($totalRecordwithFilter == 0) {

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => 0,
            "iTotalDisplayRecords" => 0,
            "aaData" => array()

        );
        echo json_encode($response);
        exit;
    }
    /**
     * Dergojme te dhenat ne front
     */
    $response = array("draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" => $table_data);
    echo json_encode($response);

}


/**
 * marrja e infove te userit qe do na sherbejne per editim
 */
if ($_POST['action'] == 'keepuserdata') {
/*
 * kapja e id se userit qe do te marrim
 */
    $userid = mysqli_real_escape_string($conn,$_POST['id']);

/*
 * marrja e te dhenave te ketij useri
 */
    $userdataquery = "SELECT id,
                                firstname,
                                lastname,
                                email,
                                birthday,
                                post,
                                photopath
                                From user
                                WHERE id = '" . $userid . "'";

    $userdata_result = mysqli_query($conn, $userdataquery);

    /*
     * kontrollojme nese eshte ekzekutuar query apo jo
     */
    if (!$userdata_result) {
        $error = "nuk gjendet asnje user me kete id";
        echo json_encode(array("status" => 500, "data" => $error));

        exit();
    }
    /*
     * kalimi i te dhenave ne nje array
     */

    $user = array();
    while ($userdata = mysqli_fetch_assoc($userdata_result)) {
        $user['usereditid'] = $userdata['id'];
        $user['firstname'] = $userdata['firstname'];
        $user['lastname'] = $userdata['lastname'];
        $user['email'] = $userdata['email'];
        $user['birthday'] = $userdata['birthday'];
        $user['post'] = $userdata['post'];
        $user['photopath'] = $userdata['photopath'];
    }

    echo json_encode(array("status" => 200, "data" => $user));


}


/*
 * update admin loged
 *
 */


/*
 * validojme admin qe i ndryshuam te dhenat dhe i ruajme keto te dhena ne database
 */

if ($_POST['action'] == 'adminupdate') {

    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $birthday = mysqli_real_escape_string($conn, $_POST['birthday']);
    $post = mysqli_real_escape_string($conn, $_POST['post']);


    $errors = [];


    /*
     *  validimi i firstanme
     */
    if (!$firstname) {
        $errors ['firstname'] = "Firstname cant be emty!";
    } else if (!ctype_alpha($firstname) || strlen($firstname) < 3) {
        $errors['firstname'] = "Firstname required letters only and 3 char or longer!";
    }

    /*
     * validimi i lastname
     */
    if (!$lastname) {
        $errors['lastname'] = "Lastname cant be emty!";
    } else if (!ctype_alpha($lastname) || strlen($lastname) < 3) {
        $errors['lastname'] = "Lastname required letters only and 3 char or longer!";
    }

    /*
     * validimi i email
     *
     * if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
     *        $errors[]= "Invalid email format";
     * }
     */

    $query_check_email = "SELECT id  
                       FROM  user 
                       where email = '" . $email . "' 
                       and id != '" . $id . "'";

    $result_check_email = mysqli_query($conn, $query_check_email);

    /*
    * kontrollojme nese eshte ekzekutuar query apo jo
    */
    if(!$result_check_email){
        header('Content-Type: application/json', '', '500');
        echo json_encode(['message' => 'Database Error.',]);
        exit();
    }

    if (mysqli_num_rows($result_check_email) > 0) {
        $errors['email'] = "This email already exist";

    }


//    $errors['email_modal'] = "this email already egzist!";


    /*
     * validimi i dates
     */

    $bday = strtotime($_POST['birthday']);
    $now = time();


    $d = $now - $bday;

    if ($d < 18 * 60 * 60 * 24 * 30 * 12) {
        $errors['birthday'] = "nuk lejohet rregjistrimi nen moshen 18 vjec! ";
    } else if (strlen($bday) === 0) {
        $errors['birthday'] = "vendos daten";
    }

    /*
     * validimi i post
     */

    if (!$post) {
        $errors['post'] = "Role is required!";
    } else if (!ctype_alpha($post)) {
        $errors['post'] = "Rol required letters only!";
    } else if (strtoupper($post) != "ADMIN" && strtoupper($post) != "USER") {
        $errors['post'] = "you need to type admin  for administrators or user for user only";
    }

    /*
     * validimi i photo
     */

    $photomodal_array = explode('.', $_FILES['photomodal']['name']);
    $photomodal = '../foto/' . md5(time() . $id) . '.' . $photomodal_array[1];

    if (!sizeof($errors)) {
        $uploadOk = 1;
        $check = getimagesize($_FILES["photomodal"]["tmp_name"]);
        if ($check !== false) {

            $uploadOk = 1;
        } else {
            $errors['photo'] = "File is not an image.";
            $uploadOk = 0;

        }
    }

    $imageFileType = $photomodal_array[1];
// kontrollon nese foto eshte e formatit jpg , png , jpeg ose gif
    if (count($_FILES) == 0) {
        $errors['photo'] = " photo is required";
    } else if ($imageFileType !== "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $errors['photo'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;

// kontrollon nese foto eshte upload-uar apo jo
    } else if ($uploadOk == 0 && !sizeof($errors)) {
        $errors['photo'] = "Sorry, your file was not uploaded.";


    }

// kontrollon nese foto u vendosh ne direktorine e caktuar apo jo

    if (!move_uploaded_file($_FILES["photomodal"]["tmp_name"], $photomodal)) {
        $errors['erroronmove'] = "Sorry, there was an error uploading your file.";


    }

//    $photopathdb = basename(dirname($photopath)) . "/" . basename($photomodal);


// kontrollojme nese ka ndodhur ndonje errorr nga kontrollet e mesiperme
    if (sizeof($errors)) {
        header('Content-Type: application/json', '', 422);
        echo json_encode(array("status" => 422, "data" => $errors));
        exit();
    }

    // egzekutojme query per update sene nuk ka errore
    $photomodal = str_replace('../', '', $photomodal);
    $updateuserquery = "UPDATE user SET firstname = '" . $firstname . "',
                                lastname ='" . $lastname . "',
                                email = '" . $email . "',
                                birthday ='" . $birthday . "' ,
                                post = '" . $post . "',
                                photopath = '" . $photomodal . "'
                                WHERE id ='" . $id . "'";

    $update_user_result = mysqli_query($conn, $updateuserquery);

// kontrollojme nese eshte egzekutuar query dhe i kthejme prgj ne json ajax-it
    if (!$update_user_result) {

        header('Content-Type: application/json', '', '422');
        echo json_encode([
            "status" => 422,
            "errors" =>$errors,
            'message' => 'photo upload Error.',
        ]);
        exit();
    } else {
        header('Content-Type: application/json', '', 200);
        echo json_encode(array("status" => 200, "data" => $errors));
        exit();
    }


}


/*
 * validojme userin qe i ndryshuam te dhenat dhe i ruajme keto te dhena ne database
 */

if ($_POST['action'] == 'update') {

    $id = mysqli_real_escape_string($conn, $_POST['usereditid']);
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $birthday = mysqli_real_escape_string($conn, $_POST['birthday']);
    $post = mysqli_real_escape_string($conn, $_POST['post_modal']);


    $errors = [];

    /*
     * shikojme nese ekziston nje foto e upload-uar tek usersi
     */

    $queryUserPath = "SELECT photopath FROM user where id = '" . $id . "'";
    $resultUserPath = mysqli_query($conn, $queryUserPath);

    /*
    * kontrollojme nese eshte ekzekutuar query apo jo
    */
    if(!$resultUserPath){
        header('Content-Type: application/json', '', '500');
        echo json_encode(['message' => 'Database Error.',]);
        exit();
    }
    $userExistPath = mysqli_fetch_assoc($resultUserPath);


    /*
     *  validimi i firstanme
     */
    if (!$firstname) {
        $errors ['first_name_modal'] = "Firstname cant be emty!";
    } else if (!ctype_alpha($firstname) || strlen($firstname) < 3) {
        $errors['first_name_modal'] = "Firstname required letters only and 3 char or longer!";
    }

    /*
     * validimi i lastname
     */
    if (!$lastname) {
        $errors['last_name_modal'] = "Lastname cant be emty!";
    } else if (!ctype_alpha($lastname) || strlen($lastname) < 3) {
        $errors['last_name_modal'] = "Lastname required letters only and 3 char or longer!";
    }

    /*
     * validimi i email
     *
     * if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
     *        $errors[]= "Invalid email format";
     * }
     */

    $query_check_email = "SELECT id  
                       FROM  user 
                       where email = '" . $email . "' 
                       and id != '" . $id . "'";

    $result_check_email = mysqli_query($conn, $query_check_email);

    /*
    * kontrollojme nese eshte ekzekutuar query apo jo
    */
    if(!$result_check_email){
        header('Content-Type: application/json', '', '500');
        echo json_encode(['message' => 'Database Error.',]);
        exit();
    }

    if (mysqli_num_rows($result_check_email) > 0) {
        $errors['email_modal'] = "This email already exist";

    }

    /*
     * validimi i dates
     */

    $bday = strtotime($_POST['birthday']);
    $now = time();


    $d = $now - $bday;

    if ($d < 18 * 60 * 60 * 24 * 30 * 12) {
        $errors['birthday_modal'] = "nuk lejohet rregjistrimi nen moshen 18 vjec! ";
    } else if (strlen($bday) === 0) {
        $errors['birthday_modal'] = "vendos daten";
    }

    /*
     * validimi i post
     */

    if (!$post) {
        $errors['post_modal'] = "Role is required!";
    } else if (!ctype_alpha($post)) {
        $errors['post_modal'] = "Rol required letters only!";
    } else if (strtoupper($post) != "ADMIN" && strtoupper($post) != "USER") {
        $errors['post_modal'] = "you need to type admin  for administrators or user for user only";
    }

    /*
     * validimi i photo
     */;

    if (count($_FILES) ==0 && $userExistPath['photopath'] == '') {
        $errors['photo_modal'] = " photo is required";

    } else if (count($_FILES) != 0) {

        $photomodal_array = explode('.', $_FILES['photo_modal']['name']);

        $photomodal = '../foto/' . md5(time() . $id) . '.' . $photomodal_array[1];

        $uploadOk = 1;
        if (!sizeof($errors)) {

            $check = getimagesize($_FILES["photo_modal"]["tmp_name"]);

            if ($check !== false) {

                $uploadOk = 1;
            } else {
                $errors['photo_modal'] = "File is not an image.";
                $uploadOk = 0;

            }
        }


        $imageFileType = $photomodal_array[1];
// kontrollon nese foto eshte e formatit jpg , png , jpeg ose gif ose bosh

        if ($imageFileType !== "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $errors['photo_modal'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;


// kontrollon nese foto eshte upload-uar apo jo
        } else if ($uploadOk == 0) {
            $errors['photo_modal'] = "Sorry, your file was not uploaded.";


        }

// kontrollon nese foto u vendosh ne direktorine e caktuar apo jo
        if (!move_uploaded_file($_FILES["photo_modal"]["tmp_name"], $photomodal)) {
            $errors['erroronmove'] = "Sorry, there was an error uploading your file.";

        }
        $photomodal = str_replace('../', '', $photomodal);
    } else {
        $photomodal = $userExistPath['photopath'];
    }

//    $photopathdb = basename(dirname($photopath)) . "/" . basename($photomodal);


// kontrollojme nese ka ndodhur ndonje errorr nga kontrollet e mesiperme
if (sizeof($errors)) {
    header('Content-Type: application/json', '', 422);
    echo json_encode(array("status" => 422, "data" => $errors));
    exit();
}

if (!empty($photomodal)) {
    $query_update_path = " photopath = '" . $photomodal . "', ";
} else {
    $query_update_path = ' ';
}
// egzekutojme query per update sene nuk ka errore

$updateuserquery = "UPDATE user SET firstname = '" . $firstname . "',
                                lastname ='" . $lastname . "',
                                email = '" . $email . "',
                                birthday ='" . $birthday . "' ,
                                $query_update_path
                                post = '" . $post . "'
                                WHERE id ='" . $id . "'";

$update_user_result = mysqli_query($conn, $updateuserquery);

// kontrollojme nese eshte egzekutuar query dhe i kthejme prgj ne json ajax-it
if (!$update_user_result) {

    header('Content-Type: application/json', '', '422');
    echo json_encode([
        "status" => 422,
        'message' => 'photo upload Error.',
    ]);
    exit();
} else {
    header('Content-Type: application/json', '', 200);
    echo json_encode(array("status" => 200, "data" => $errors));
    exit();
}


}

/*
 * marrja e te dhenave te userit qe do fshijme te dhenat
 */
if ($_POST['action'] == "deleteuser") {

    $deleteerror = [];
    $id = mysqli_real_escape_string($conn, $_POST['id']);


    // egzekutojme query per te fshire user
    $query_delete_user = "DELETE FROM user WHERE id = '" . $id . "'";
    $delete_user_result = mysqli_query($conn, $query_delete_user);

    // kontrollojme nese query eshte egzekutuar dhe i kthejme prgj ne json ajax-it
    if (!$delete_user_result) {
        $deleteerror = "Error on delete happenned";
        header('Content-Type: application/json', '', 422);
        echo json_encode(array("status" => 422, "data" => $deleteerror));
        exit();
    } else {
        header('Content-Type: application/json', '', 200);
        echo json_encode(array("status" => 200, "data" => $deleteerror));

        exit();
    }

}

/*
 * shtimi i userit
 */

$errors = [];
$loginerrors = [];
$userRole = "User";

if ($_POST['action'] == "register") {
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = $conn->escape_string($_POST['lastname']);
    $email = $conn->escape_string($_POST['email']);
    $birthday = $conn->escape_string($_POST['birthday']);
    $password = $conn->escape_string($_POST['password']);
    $confirmPassword = $conn->escape_string($_POST['confirm_password']);
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
    /*
    * kontrollojme nese eshte ekzekutuar query apo jo
    */
    if(!$result){
        header('Content-Type: application/json', '', '500');
        echo json_encode(['message' => 'Database Error.',]);
        exit();
    }
    $count = $result->num_rows;



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
        header('Content-Type: application/json', '', 201);
        echo json_encode([
            'message' => 'Success.',
        ]);
        exit();
    } else {
        header('Content-Type: application/json', '', '500');
        echo json_encode([
            'message' => 'Server Error.',
        ]);
        exit();
    }
}




