<?php


if (strtoupper($_SESSION['post']) != "ADMIN") {
    header('Location: forbidden.php');
    exit();
}

/*
 * query per te marr te dhenat nga tabelat costumers dhe product
 */

$queryCostumers = "SELECT * FROM costumers WHERE 1=1 ";
$resultCostumers = $conn->query($queryCostumers);
if(!$resultCostumers){
    echo "costumers query error".mysqli_error($conn) . " " . __LINE__;
}
$costumers = array();
$productSalary = array();
while ($row = $resultCostumers->fetch_assoc()) {
    $costumers[$row['costumer_id']]['firstname'] = $row['costumer_name'];
    $costumers[$row['costumer_id']]['lastname'] = $row['costumer_surname'];
    $costumers[$row['costumer_id']]['buyDate'] = $row['costumer_date_buy'];
    $costumers[$row['costumer_id']]['quantity'] = $row['costumer_quantity'];
    $costumers[$row['costumer_id']]['productId'] = $row['product_id'];

    $productSalary[$row['product_id']]['sell'] += $row['costumer_quantity'];
    $productSalary[$row['product_id']][$row['costumer_id']]['costumerFirstName'] = $row['costumer_name'];
    $productSalary[$row['product_id']][$row['costumer_id']]['costumerLastName'] = $row['costumer_surname'];
    $productSalary[$row['product_id']][$row['costumer_id']]['totalQuantity'] += $row['costumer_quantity'];
    $productSalary[$row['product_id']][$row['costumer_id']]['date'][$row['costumer_date_buy']]['quantity'] += $row['costumer_quantity'];
}


$queryProduct = "SELECT * FROM products WHERE 1=1 ";
$resultCProduct = $conn->query($queryProduct);
if(!$resultCProduct){
    echo "products query error".mysqli_error($conn) . " " . __LINE__;
}
$product = array();
$originCount = [];
while ($row = $resultCProduct->fetch_assoc()) {
    $product[$row['id']]['productCategory'] = $row['product_name'];
    $product[$row['id']]['brand'] = $row['product_brand'];
    $product[$row['id']]['price'] = $row['product_cost'];
    $product[$row['id']]['Descript'] = $row['product_desc'];
    $product[$row['id']]['warranty'] = $row['product_warranty'];
    $product[$row['id']]['productBuy'] = $row['product_buy_date'];
    $product[$row['id']]['productStoc'] = $row['product_stoc'];
    $product[$row['id']]['productOrigin'] = $row['product_origin'];

    if (!isset($originCount[$row['product_origin']])) {
        $originCount[$row['product_origin']]['orgn'] = $row['product_origin'];
    }
}

$categoryCount = [];
$randCount = [];
foreach ($product as $key => $val) {
    if (!isset($categoryCount[$val['productCategory']])) {
        $categoryCount[$val['productCategory']]['category'] = $val['productCategory'];
    }
    if (!isset($randCount[$val['brand']])) {
        $randCount[$val['brand']]['brand'] = $val['brand'];
    }

}


$QueryProdSell = "SELECT id ,product_cost , costumer_id , costumer_quantity FROM products LEFT JOIN costumers on id=product_id ";
$resultProdSell = $conn->query($QueryProdSell);
if(!$resultProdSell){
    echo "products Sells query error".mysqli_error($conn) . " " . __LINE__;
}
$prodSell = array();
$count = 0;
$sum = 0;
while ($row = $resultProdSell->fetch_assoc()) {
    $prodSell[$row["id"]]['quantity'] += $row['costumer_quantity'];
    $prodSell[$row["id"]]['cost'] = $row['product_cost'];
    if ($row['costumer_quantity'] != 0) {
        $sum += $row['product_cost'];
        $count++;
    }
    $prodSell[$row["id"]]['totalCost'] = $sum;

    $prodSell[$row["id"]]['count'] = $count;
}

?>

<div id="" class="gray-bg">
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Charts</h2>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-6">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Shitjet e produkteve
                            <small>total i shitjeve ne baze te dates se shitjes</small>
                        </h5>
                    </div>
                    <div class="ibox-content">
                        <div>
                            <canvas id="lineChart" height="140"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Stoku ne baze te kategorive</h5>
                    </div>
                    <div class="ibox-content">
                        <div>
                            <canvas id="barChart" height="140"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Sasia e produkteve ne baze te shtetin te importuar </h5>

                    </div>
                    <div class="ibox-content">
                        <div class="text-center">
                            <canvas id="polarChart" height="140"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>klientet ne baze te blerjeve </h5>

                    </div>
                    <div class="ibox-content">
                        <div>
                            <canvas id="doughnutChart" height="140"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="card col-lg-12">
                <!--                    table-->

                <table class="table table-sm">
                    <thead>
                    <tr>
                        <!--                            <th scope="col">#</th>-->
                        <th scope="col"></th>
                        <th scope="col">nr</th>
                        <th scope="col">product Kategory</th>
                        <th scope="col">product Brand</th>
                        <th scope="col">product Price</th>
                        <th scope="col">product Warranty</th>
                        <th scope="col">product Selled</th>
                        <th scope="col">product Total Win</th>

                        <th scope="col">product Stoc</th>
                        <th scope="col">product Origin</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 1;
                    foreach ($product as $key => $value) {
                        $countStoc += $value['productStoc'];

                        ?>
                        <tr>
                            <td>
                                <button class="btn" onclick="show('<?= $key ?>')"><i
                                        id="secplus<?= $key ?>"
                                        class="iconout fas fa-plus fa-ellipsis-h text-success"></i></button>
                            </td>
                            <th scope="row"><?php echo $i ?></th>
                            <td><?php echo $value['productCategory'] ?></td>
                            <td><?php echo $value['brand'] ?></td>
                            <td><?php echo $value['price'] ?> leke</td>
                            <td><?php echo $value['warranty'] ?></td>
                            <?php

                            foreach ($prodSell as $prod => $prodVal) {
                                if ($prod === $key) {
                                    $totalSell += $prodVal['quantity'];
                                    $totalWin += $prodVal['quantity'] * $value['price'];

                                    ?>
                                    <td><?php echo $prodVal['quantity'] ?> </td>
                                    <td><?php echo $tot = $prodVal['quantity'] * $value['price'] ?> leke</td>

                                    <?php
                                }
                            }
                            ?>
                            <td><?php echo $value['productStoc'] ?></td>
                            <td><?php echo $value['productOrigin'] ?></td>
                        </tr>
                        <tr>

                            <td colspan="12"
                                id="<?= $key ?>"
                                style="display: none" class="tablein">
                                <table style=" width:100%" class="table ">
                                    <thead style="color: black ; background: #dee1e3">
                                    <tr>
                                        <td></td>
                                        <td>firstname</td>
                                        <td>lastname</td>
                                        <td>Quantity</td>

                                    </tr>
                                    </thead>

                                    <?php
                                    foreach ($productSalary as $data => $datavalue) {
                                        foreach ($datavalue as $datas => $datasvalue) {

                                            if ($data === $key && is_numeric($datas)) {
                                                ?>
                                                <tr style="background: #ffffff">
                                                    <td>
                                                        <button class="btn "
                                                                onclick="showDetail('<?= $key . $datas ?>')">
                                                            <i
                                                                id="<?= $key . $datas ?>"
                                                                class=" icon fas fa-plus fa-ellipsis-h text-success"></i>
                                                        </button>
                                                    </td>
                                                    <td><?php echo $datasvalue['costumerFirstName'] ?></td>
                                                    <td><?php echo $datasvalue['costumerLastName'] ?></td>
                                                    <td><?php echo $datasvalue['totalQuantity'] ?></td>
                                                </tr>
                                                <tr>

                                                    <td colspan="12"
                                                        id="tab<?= $key . $datas ?>"
                                                        style="display: none" class="tablein">
                                                        <table style=" width:100% ; color:black"
                                                               class="table table-dark table-hover">
                                                            <thead style="background: #dee1e3">
                                                            <tr>
                                                                <td>Date</td>
                                                                <td>Quantity</td>

                                                            </tr>
                                                            </thead>

                                                            <?php foreach ($datasvalue['date'] as $dateindex => $dateval) { ?>
                                                                <tr>
                                                                    <td><?= $dateindex ?></td>
                                                                    <td><?= $dateval['quantity'] ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                        </table>
                                                    </td>

                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                </table>
                            </td>

                        </tr>
                        <?php $i++;
                    } ?>
                    <?php
                    $averageSum = 0;
                    $countAverage = 0;
                    foreach ($product as $key => $value) {
                        $averageSum += $value['price'];
                        $countAverage++;
                    }
                    $average = $averageSum / $countAverage;
                    ?>
                    <tr>
                        <td></td>
                        <td style="font-weight:bold">Total :</td>
                        <td style="font-weight:bold"><?php echo $count = count($categoryCount); ?></td>
                        <td style="font-weight:bold"><?php echo $count = count($randCount); ?></td>
                        <td style="font-weight:bold"><?php echo round($average, 2); ?></td>
                        <td style="font-weight:bold"></td>
                        <td style="font-weight:bold"> <?php echo $totalSell; ?></td>
                        <td style="font-weight:bold"> <?php echo $totalWin; ?></td>
                        <td style="font-weight:bold"> <?php echo $countStoc; ?></td>
                        <td style="font-weight:bold"> <?php echo $count = count($originCount); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


