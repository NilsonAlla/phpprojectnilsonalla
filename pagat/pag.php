<?php

if (strtoupper($_SESSION['post']) != "ADMIN") {
header('Location: forbidden.php');
exit();
}


$querUser = "SELECT full_name FROM users ";
$usersResult = $con->query( $querUser);
if(!$usersResult){
    echo "users query error".mysqli_error($con) . " " . __LINE__;
    exit();

}
$userName = array();
while ($row = mysqli_fetch_assoc($usersResult)) {
    $userName[$row['full_name']]['fullname'] = $row['full_name'];
}

/** Ditet festive*/
$fest = array();
$holidays = array();
$query = "SELECT date  FROM off_days";
$result = $con->query($query);
if(!$result){
    echo "date off query error".mysqli_error($con) . " " . __LINE__;
    exit();

}
while ($row = $result->fetch_assoc()) {
    $fest[$row['date']] = $row['date'];
    $holidays[$row['date']] = true;
}


$queryJoinTables = "SELECT
                              users.id,
                              users.full_name,
                              users.total_paga,
                              working_days.date,
                              working_days.hours
                            FROM users
                            LEFT JOIN working_days
                              ON working_days.user_id = users.id  WHERE 1=1 ";

/** Filters*/
if (empty($_POST['date'])) {
    $datesFirst = date('Y-01-01') ;
    $datesLast =  date('Y-m-d');
}else {
    $dates = explode(' - ', $_POST['date']);
    $datesFirst = $dates[0];
    $datesLast = $dates[1];
}


$queryJoinTables .= " AND working_days.date >= '" . $datesFirst. "' AND working_days.date <= '" . $datesLast . "'";


if (!empty($_POST['select2name']) ) {
    if ( strtoupper($_POST['select2name']) != "ALL") {
        $queryJoinTables .= " AND users.full_name ='" . $con->escape_string($_POST['select2name']) . "' ";
    }
}


/** End Filters*/
$result = $con->query($queryJoinTables);
if(!$result){
    echo "user join query error".mysqli_error($conn) . " " . __LINE__;
    exit();
}
$hours_in = 8;
$userdata = array();
$calculatedCoefs = [];
function calcCoeficients($day, $holidays, &$calcDays)
{
    if (isset($calcDays[$day])) {
        return $calcDays[$day];
    }

    $dayNumber = date('w', strtotime($day));
    $weekStart = date("Y-m-d", strtotime('monday this week', strtotime($day)));
    $weekEnd = date("Y-m-d", strtotime('sunday this week', strtotime($day)));
    $weekKey = $weekStart . ' <> ' . $weekEnd;

    if ($holidays[$day]) {
        $coefs = ['daystatus' => 'holiday', 'regular' => 1.5, 'overtime' => 2,];
    } else if (in_array($dayNumber, [0, 6])) {
        $coefs = ['daystatus' => 'weekend', 'regular' => 1.25, 'overtime' => 1.5,];
    } else {
        $coefs = ['daystatus' => 'normal', 'regular' => 1, 'overtime' => 1.25,];
    }
    $coefs['weekKey'] = $weekKey;
    $calcDays[$day] = $coefs;

    return $coefs;

}


while ($row = mysqli_fetch_assoc($result)) {


    $payInHour = $row['total_paga'] / 22 / 8;
    $paymentPerHour = $row['total_paga'] / 22 / 8;

    /** Percaktojme ore in/out */
    $hours = $row['hours'];
    $overtime = 0;

    if ($row['hours'] > 8) {
        $hours = 8;
        $overtime = $row['hours'] - 8;
    }

    /** Calculate hour coefs */
    $coefs = calcCoeficients($row['date'], $holidays, $calculatedCoefs);

    /** Collect Users Data */
    $tempArray[$row['id']]['fullname'] = $row['full_name'];

    $tempArray[$row['id']]['hours'][$coefs['daystatus']]['hours'] += $hours;
    $tempArray[$row['id']]['hours'][$coefs['daystatus']]['overtime'] += $overtime;
    $tempArray[$row['id']]['hours'][$coefs['daystatus']]['total'] += ($hours + $overtime);

    $tempArray[$row['id']]['hours']['total']['hours'] += $hours;
    $tempArray[$row['id']]['hours']['total']['overtime'] += $overtime;
    $tempArray[$row['id']]['hours']['total']['total'] += ($hours + $overtime);

    $tempRegPayment = $hours * $coefs['regular'] * $paymentPerHour;
    $tempOverPayment = $overtime * $coefs['overtime'] * $paymentPerHour;

    $tempArray[$row['id']]['payment']['regular'] += $tempRegPayment;
    $tempArray[$row['id']]['payment']['overtime'] += $tempOverPayment;
    $tempArray[$row['id']]['payment']['total'] += ($tempRegPayment + $tempOverPayment);

    /** Coolect Users week data */
    $tempArray[$row['id']]['weeks'][$coefs['weekKey']]['hours'] += $hours;
    $tempArray[$row['id']]['weeks'][$coefs['weekKey']]['overtime'] += $overtime;
    $tempArray[$row['id']]['weeks'][$coefs['weekKey']]['payment'] += ($tempRegPayment + $tempOverPayment);

    /** Coolect Users days data */
    $tempArray[$row['id']]['weeks'][$coefs['weekKey']]['days'][$row['date']]['hours']['hours'] += $hours;
    $tempArray[$row['id']]['weeks'][$coefs['weekKey']]['days'][$row['date']]['hours']['overtime'] += $overtime;
    $tempArray[$row['id']]['weeks'][$coefs['weekKey']]['days'][$row['date']]['payment']['regular'] += $tempRegPayment;
    $tempArray[$row['id']]['weeks'][$coefs['weekKey']]['days'][$row['date']]['payment']['overtime'] += $tempOverPayment;
    $tempArray[$row['id']]['weeks'][$coefs['weekKey']]['days'][$row['date']]['payment']['total'] += ($tempRegPayment + $tempOverPayment);

//    continue;


    /*
     * kalimi i dates ne nje variabel dhe nxjerrja e javeve
     */

    $date = $row['date'];


    $week = date("YW", strtotime($date));

    $week_start = date("Y-m-d", strtotime('monday this week', strtotime($date)));
    $week_end = date("Y-m-d", strtotime('sunday this week', strtotime($date)));

    $week = $week_start . ' <> ' . $week_end;
    /*
     * llogaritja e oser ne varesi te oreve shtese , diteve te fundjaves ose diteve festive
     */

    $today = date('w', strtotime($row['date']));
    if ($row['hours'] > 8) {
        $hoursIn = 8;
        $hoursOut = $row['hours'] - 8;

    } else {
        $hoursIn = $row['hours'];
        $hoursOut = 0;
    }

    if (array_key_exists($row['date'], $fest)) {
        $payIn = 1.5;
        $payOut = 2;
        $userdata[$row['id']]['fest_in_hour'] = $hoursIn;
        $userdata[$row['id']]['fest_out_hour'] = $hoursOut;
        $userdata[$row['id']]['fest_total_hour'] = ($hoursIn + $hoursOut);

    } else if ($today == 6 || $today == 0) {


        $payIn = 1.25;
        $payOut = 1.5;
        $userdata[$row['id']]['weekend_in_hour'] += $hoursIn;
        $userdata[$row['id']]['weekend_out_hour'] += $hoursOut;
        $userdata[$row['id']]['weekend_total_hour'] += ($hoursIn + $hoursOut);

    } else {


        $payIn = 1;
        $payOut = 1.25;
        $userdata[$row['id']]['normal_in_hour'] += $hoursIn;
        $userdata[$row['id']]['normal_out_hour'] += $hoursOut;
        $userdata[$row['id']]['normal_total_hour'] += ($hoursIn + $hoursOut);

    }


    /*
     * llogaritja e totalit te pages ditore
     */
    $totalPayIn = ($payInHour * $hoursIn * $payIn);
    $totalPayOut = ($payInHour * $hoursOut * $payOut);

    /*
     * Llogaritja per User
     */
    $userdata[$row['id']]['id'] = $row['id'];
    $userdata[$row['id']]['full_name'] = $row['full_name'];
    $userdata[$row['id']]['total_paga'] = $row['total_paga'];
    $userdata[$row['id']]['total_hours_in'] += $hoursIn;
    $userdata[$row['id']]['total_hours_out'] += $hoursOut;
    $userdata[$row['id']]['total_hours'] += ($hoursIn + $hoursOut);
    $userdata[$row['id']]['total_in_hours_pay'] += $totalPayIn;
    $userdata[$row['id']]['total_out_hours_pay'] += $totalPayOut;
    $userdata[$row['id']]['total_payment'] += ($totalPayIn + $totalPayOut);
    $userdata[$row['id']]['week'][$week]['week_start'] = $week_start;
    $userdata[$row['id']]['week'][$week]['week_end'] = $week_end;
    $userdata[$row['id']]['week'][$week]['week_in_hours'] += $hoursIn;
    $userdata[$row['id']]['week'][$week]['week_out_hours'] += $hoursOut;
    $userdata[$row['id']]['week'][$week]['week_page'] += ($totalPayIn + $totalPayOut);
    $userdata[$row['id']]['week'][$week]['date'][$row['date']]['hours_in'] = $hoursIn;
    $userdata[$row['id']]['week'][$week]['date'][$row['date']]['hours_out'] = $hoursOut;
    $userdata[$row['id']]['week'][$week]['date'][$row['date']]['payment_hours_in'] = $totalPayIn;
    $userdata[$row['id']]['week'][$week]['date'][$row['date']]['payment_hours_out'] = $totalPayOut;
    $userdata[$row['id']]['week'][$week]['date'][$row['date']]['total'] = ($totalPayIn + $totalPayOut);
}

?>

<div id="" class="gray-bg dashbard-1">
    <div class="row  border-bottom white-bg dashboard-header">
        <!--code-->

        <div class="card col-lg-12" style="margin-top: 50px; margin-bottom: 30px">
            <div class=" card-body col-lg-12">
                <form id="filters" method="POST" action="index2.php?page=pagat">

                    <div class="row">

                        <div class="col-lg-3">
                            <label for="date">
                                Search by date</label>
                            <input id="date" name="date" type="text" class="form-control" placeholder="Date"
                                   autocomplete="off" value="<?php echo date('Y-01-01') . " - " . date('Y-m-d'); ?>">
                        </div>

                        <div class="col-lg-3">
                            <label for="select2name">
                                Search by fullname</label>

                            <select id="select2name" name="select2name" class="form-control"
                                    style="width: 80% " placeholder="name">
                                <option>All</option>
                                <?php foreach ($userName as $key => $value) { ?>
                                    <option><?= $value['fullname'] ?></option>
                                <?php } ?>

                            </select>
                        </div>

                        <div class="col-lg-3" style="margin-top: 30px">

                            <input id="btn" class="btn btn-outline-success my-2 my-sm-0" value="search"
                                   type="submit"/>

                        </div>
                    </div>
                </form>


            </div>

        </div>

        <!--    table-->

        <div class="">
            <div class="">
                <div class="row">


                    <table id="table" class="table ">
                        <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">Nr</th>
                            <th scope="col">Full Name</th>
                            <th scope="col">Hours In normal</th>
                            <th scope="col">Hours Out normal</th>
                            <th scope="col">Totale Hours normal</th>
                            <th scope="col">Hours in weekend</th>
                            <th scope="col">Hours out weekend</th>
                            <th scope="col">Totale Hours weekend</th>
                            <th scope="col">Hours in fest</th>
                            <th scope="col">Hours out fest</th>
                            <th scope="col">Totale Hours fest</th>
                            <th scope="col">Totale Hours in</th>
                            <th scope="col">Totale Hours out</th>
                            <th scope="col">Totale Hours work</th>
                            <th scope="col">Payment In</th>
                            <th scope="col">Payment Out</th>
                            <th scope="col">Totale Payment</th>
                        </tr>

                        </thead>
                        <tbody class="table-body">

                        <?php
                        /*
                         * marrja e te dhenave te userit grupuar ne id
                         */
                        $nr = 0;
                        foreach ($userdata as $user_id => $data) {
                            $nr++;
                            ?>
                            <!--mbushja e tabeles me te dhenat e userit-->

                            <tr style="color: red !important;">
                                <td>
                                    <button class="btn" onclick="showDetail('<?= $data['id'] ?>')"><i
                                            id="<?= $data['id'] ?>"
                                            class="iconout fas fa-plus fa-ellipsis-h text-success"></i></button>
                                </td>
                                <td><?= $nr ?></td>
                                <td><?= $data['full_name'] ?></td>
                                <td><?= $data['normal_in_hour'] ?> ore</td>
                                <td><?= $data['normal_out_hour'] ?> ore</td>
                                <td><?= $data['normal_total_hour'] ?> ore</td>
                                <td><?= $data['weekend_in_hour'] ?> ore</td>
                                <td><?= $data['weekend_out_hour'] ?> ore</td>
                                <td><?= $data['weekend_total_hour'] ?> ore</td>
                                <td><?= $data['fest_in_hour'] ?> ore</td>
                                <td><?= $data['fest_out_hour'] ?> ore</td>
                                <td><?= $data['fest_total_hour'] ?> ore</td>
                                <td><?= $data['total_hours_in'] ?> ore</td>
                                <td><?= $data['total_hours_out'] ?> ore</td>
                                <td><?= $data['total_hours'] ?> ore</td>
                                <td><?= round($data['total_in_hours_pay'], 2) ?> Lek</td>
                                <td><?= round($data['total_out_hours_pay'], 2) ?> Lek</td>
                                <td><?= round($data['total_payment'], 2) ?> Lek</td>
                            </tr>


                            <!--                krijimi i nje tabele te re ku do te tregoje te dhenat e userit ne menyre te detajuar sipas javeve-->

                            <tr>
                                <td colspan="18" id="tog<?= $data['id'] ?>" style="display: none" class="tableout">
                                    <table id="week" style="width: 100%" class="table  bg-secondary">
                                        <thead>
                                        <tr>
                                            <td></td>
                                            <td>Week start/end date</td>
                                            <td>Week work hours in</td>
                                            <td>Week work hours out</td>
                                            <td>Week total page</td>
                                        </tr>
                                        </thead>
                                        <?php
                                        $weeks = array();

                                        foreach ($data['week'] as $mainDate => $datas) {

                                            if (array_key_exists($mainDate, $weeks)) {
                                                $i = 0
                                                ?>


                                                <?php
                                                /*
                                                 * krijimi i javeve sipas dites kur fillon dhe dites kur java mbaron
                                                 */
                                            } else {
                                                ?>
                                                <tr class="table-secondary">
                                                    <td>
                                                        <button class="btn "
                                                                onclick="show('<?= str_replace(' <> ', '', $data['id'] . $mainDate) ?>')">
                                                            <i
                                                                id="secplus<?= str_replace(' <> ', '', $data['id'] . $mainDate) ?>"
                                                                class=" icon fas fa-plus fa-ellipsis-h text-success"></i>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <?= $mainDate ?></td>

                                                    <td><?= $datas["week_in_hours"] ?></td>
                                                    <td><?= $datas["week_out_hours"] ?></td>
                                                    <td><?= round($datas["week_page"], 2) ?></td>
                                                </tr>

                                                <!---------->
                                                <tr>

                                                    <!--                                --><?php //foreach ($data['week'] as $weekindex => $weekvalue){?>
                                                    <td colspan="12"
                                                        id="<?= str_replace(' <> ', '', $user_id . $mainDate) ?>"
                                                        style="display: none" class="tablein">
                                                        <table style=" width:100%"
                                                               class="table table-dark table-hover">
                                                            <thead style="color: black">
                                                            <tr>
                                                                <td>Date</td>
                                                                <td>hours_in</td>
                                                                <td>hours_out</td>
                                                                <td>payment_hours_in</td>
                                                                <td>payment_hours_out</td>
                                                                <td>payment_total</td>
                                                            </tr>
                                                            </thead>

                                                            <?php foreach ($datas['date'] as $dateindex => $datevalue) { ?>
                                                                <tr class="bg-success">
                                                                    <td><?= $dateindex ?></td>
                                                                    <td><?= $datevalue['hours_in'] ?></td>
                                                                    <td><?= $datevalue['hours_out'] ?></td>
                                                                    <td><?= round($datevalue['payment_hours_in'], 2) ?></td>
                                                                    <td><?= round($datevalue['payment_hours_out'], 2) ?></td>
                                                                    <td><?= round($datevalue['total'], 2) ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                        </table>
                                                    </td>
                                                    <!--                                --><?php //}?>

                                                </tr>

                                                <?php
                                                $weeks[$mainDate] = $mainDate;
                                                $i++;


                                            }
                                        }

                                        ?>

                                    </table>
                                </td>
                            </tr>


                        <?php } ?>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>

    </div>

</div>

<script src="js/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script><script src="js/select2.full.min.js"></script>
<script src="pagat/pagatJs.js"></script>





