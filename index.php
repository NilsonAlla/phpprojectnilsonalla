<?php
session_start();
error_reporting(E_ERROR);
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}
include 'DB/dbPaga.php';
include 'DB/database.php';
?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Nilson Alla | Dashboard</title>


    <?php
    include 'includes/header.php';
    ?>
    <style>
        #datatable_wrapper{
            width: 100%;
        }
        .dataTables_wrapper .row {
            width: 100% !important;
        }

    </style>
    <!-- Mainly scripts -->
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
</head>
<body>
<div id="wrapper">
    <!--Left Menu-->
    <?php
        include 'includes/leftmenu.php'
    ?>
    <!--End Left Menu-->

    <div id="page-wrapper" class="gray-bg "   >
        <div class="row border-bottom">
            <!--Top Menu-->
            <?php
            include('includes/topmenu.php');
            ?>
            <!-- End Top Menu-->
        </div>

        <div class="row" style="margin-left: 2px">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content">
                    <!--Wraper Content-->
                    <?php
                        switch ($_GET['page']) {
                            case 'pagat':
                                include 'pagat/pag.php';
                                break;
                            case 'costumers':
                                include 'costumers/costumer.php';
                                break;
                        case 'users':
                                include 'UserData/';
                                break;
                        case 'raport':
                                include 'raport/userRaport.php';
                                break;
                        case 'charts':
                                include 'charts/charts.php';
                                break;

                            default:
                                include 'dashboard.php';
                                break;
                        }


                    ?>
                    <!--End Wraper Content-->
                </div>
            </div>
        </div>
        <!--Footer-->
        <?php
        include('includes/footer.php');
        ?>
        <!--End Footer-->
    </div>
</div>



<script src="js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>
<script src="js/plugins/dataTables/datatables.min.js"></script>
<script src="logout.js"></script>


<script>
    var urlData = `<?= $_GET['ids'] ?>`;
</script>
<?php

if ($_GET['page'] == "index") {
    ?>
    <script src="index.js"></script>
<?php
}else if ($_GET['page'] == "users") {
?>
    <script src="UserData/userDataJs.js"></script>
    <?php
}else if ($_GET['page'] == "raport") {
    ?>
    <script src="raport/raportJs.js"></script>
    <?php
}else if ($_GET['page'] == "pagat") {
    ?>
    <script src="pagat/pagatJs.js"></script>
    <?php
}else if ($_GET['page'] == "charts") {
    ?>
    <script src="charts/chartJs.js"></script>
    <?php
}else if ($_GET['page'] == "costumers") {
    ?>
    <script src="costumers/javascript.js"></script>
    <?php
}
?>
<script>

</script>
</body>
</html>
