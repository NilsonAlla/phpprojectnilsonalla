<?php
error_reporting(0);
session_start();

require_once "../DB/database.php";

if ($_SESSION['post'] != 'Admin') {

    header('Location: login.php?not-logged-in-user');
    exit();
}

function calculateOvertime($time)
{
    $response = ['hours' => round($time, 2), 'overtime' => 0,];
    if ($time > 8) {
        $response = ['hours' => 8, 'overtime' => round($time, 2) - 8,];
    }
    return $response;
}


if ($_POST['action'] == "employerlist") {
    $draw = $conn->escape_string($_POST['draw']);
    $offset = $conn->escape_string($_POST['start']);
    $limit = $conn->escape_string($_POST['length']);
    $searchValue = $conn->escape_string($_POST['search']['value']);
    $counterRecords = $offset;

    $errors=[];

    /*
     * dhenia e vleres se searchQuerit
     */

    $searchQuery = "";
    if ($searchValue != ''){

        $searchQuery = " AND (firstname LIKE '%" . $searchValue . "%' OR 	
            lastname LIKE '%" . $searchValue . "%')";
    }


    /**
     * kapja e totalit te rekordeve te userit
     */

    $queryUserRecord = "SELECT COUNT(*)  as allcount FROM user where 1=1";
    $userRecordResult = $conn->query($queryUserRecord);

    /*
    * kontrollojme nese eshte ekzekutuar query apo jo
    */
    if(!$userRecordResult){
        header('Content-Type: application/json', '', '500');
        echo json_encode(['message' => 'Database Error.',]);
        exit();
    }

    $userRecord = mysqli_fetch_assoc($userRecordResult);


    /**
     * kapja e totalit te rekordeve te userit me filter
     */

    $queryUserRecordFiltered = "SELECT COUNT(*)  as allcount FROM user WHERE 1=1 $searchQuery";

    $userRecordFilteredResult = $conn->query($queryUserRecordFiltered);

    /*
     * kontrollojme nese eshte ekzekutuar query apo jo
     */
    if(!$userRecordFilteredResult){
        header('Content-Type: application/json', '', '500');
        echo json_encode(['message' => 'Database Error.',]);
        exit();
    }

    $userRecordFiltered = mysqli_fetch_assoc($userRecordFilteredResult);



    /** Users */
    $query = "SELECT  * FROM user where 1=1 ";
    $query .=$searchQuery;
    /** Order Table Func */

    if (sizeof($_POST['order'])) {
        $query .= " order by ";
        foreach ($_POST['order'] as $key => $values) {
            $field = $_POST['columns'][$values['column']]['name'];
            $tempOrders[] = $field . " " . $values['dir'];
        }
        $query .= implode(",", $tempOrders);
    }

    $query .= " limit " . $limit . " offset " . $offset;
    $result = $conn->query($query);

    /*
    * kontrollojme nese eshte ekzekutuar query apo jo
    */
    if(!$result){
        header('Content-Type: application/json', '', '500');
        echo json_encode(['message' => 'Database Error.',]);
        exit();
    }
    $userRow =mysqli_num_rows($result);

    $users=array();
if ($userRow > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[$row['id']]['firstname'] = $row['firstname'];
        $users[$row['id']]['lastname'] = $row['lastname'];
    }
}

    $selectedUsers = array_keys($users);

    /*
     * Marrja e te dhenave nga tabela checkins
     */
    $date00 = "00:00:00";
    $date24 = "18:00:00";
    $data = array();

if ($userRow > 0) {
    $queryCheckins = "SELECT  * from  checkins where user_id in ('" . implode("','", $selectedUsers) . "')";
    $resultCheckins = $conn->query($queryCheckins);

    /*
    * kontrollojme nese eshte ekzekutuar query apo jo
    */
    if(!$resultCheckins){
        header('Content-Type: application/json', '', '500');
        echo json_encode(['message' => 'Database Error.',]);
        exit();
    }

    while ($row = $resultCheckins->fetch_assoc()) {

        if ($row['check_out_hour'] == $date00) {
            $row['check_out_hour'] = $date24;
        }
        $chechin = $row['check_in_date'] . ' ' . $row['check_in_hour'];
        $chechout = $row['check_in_date'] . ' ' . $row['check_out_hour'];

        $hours = (strtotime($chechout) - strtotime($chechin)) / 3600;
        $hours = round($hours, 2);

        $dayHours[$row['user_id']]['checkins'][$row['check_in_date']] += $hours;
    }
}

    /**
     * Perpunimi i te dhenave per secilin user
     */
    foreach ($dayHours as $userId => $userAttributes) {
        $data[$userId]['firsName'] = $userAttributes['firstName'];
        $data[$userId]['lastName'] = $userAttributes['lastName'];
        ksort($userAttributes['checkins']);
        foreach ($userAttributes['checkins'] as $date => $hours) {
            $year = date('Y', strtotime($date));
            $month = date('M', strtotime($date));

            $timeSpec = calculateOvertime($hours);

            if (!isset($data[$userId]['firstDate'])) {
                $data[$userId]['firstDate'] = $date;
            }
            $data[$userId]['lastDate'] = $date;

            /**
             * Hours for days
             */
            $data[$userId]['year'][$year]['month'][$month]['day'][$date]['hours'] += round($timeSpec['hours'],2);
            $data[$userId]['year'][$year]['month'][$month]['day'][$date]['overtime'] += round($timeSpec['overtime'],2);
            $data[$userId]['year'][$year]['month'][$month]['day'][$date]['total'] += round(($timeSpec['hours'] + $timeSpec['overtime']),2);

            /**
             * Hours for months
             */
            $data[$userId]['year'][$year]['month'][$month]['hours'] += round($timeSpec['hours'],2);
            $data[$userId]['year'][$year]['month'][$month]['overtime'] += round($timeSpec['overtime'],2);
            $data[$userId]['year'][$year]['month'][$month]['total'] += round(($timeSpec['hours'] + $timeSpec['overtime']),2);

            /**
             * Hours for y['year']ears
             */
            $data[$userId]['year'][$year]['hours'] += round($timeSpec['hours'],2);
            $data[$userId]['year'][$year]['overtime'] += round($timeSpec['overtime'],2);
            $data[$userId]['year'][$year]['total'] += round(($timeSpec['hours'] + $timeSpec['overtime']),2);

            /**
             * Hours in total
             */
            $data[$userId]['hours'] += $timeSpec['hours'];
            $data[$userId]['overtime'] += $timeSpec['overtime'];
            $data[$userId]['total'] += ($timeSpec['hours'] + $timeSpec['overtime']);
        }
    }


/*
 * kalimi i te dhename te perfituara me lart ne formen qe i kerkon datatable
 */
    foreach ($users as $userId => $userData) {

        $counterRecords++;
        $tableData[] = [
            "id" => $userId,
            "nr" => $counterRecords,
            "firstname" => $userData['firstname'],
            "lastname" => $userData['lastname'],
            "first_date" => $data[$userId]['firstDate'],
            "last_date" => $data[$userId]['lastDate'],
            "hours" => round($data[$userId]['hours'], 2),
            "overtime" => round($data[$userId]['overtime'], 2),
            "total" => round($data[$userId]['total'], 2),
            "year" => $data[$userId]['year']
        ];

    }


    $totalRecords = $userRecord['allcount'];
    $totalRecordwithFilter = $userRecordFiltered['allcount'];

    /*
     * nese kerkimi nuk ka rezultat , i kalojme te dhenat manualisht per te ms na dhene problem ne front
     */
    if ($totalRecordwithFilter == 0){

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => 0,
            "iTotalDisplayRecords" => 0,
            "aaData" => array(),
            "errors" => $errors
        );
        echo json_encode($response);
        exit;
    }

/*
 * kalimi i rezultateve frontend-it
 */
    $response = [
        "draw" => intval($draw),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalRecordwithFilter,
        "data" => $tableData,
        "errors" => $errors
    ];
    echo json_encode($response);
}

