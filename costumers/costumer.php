<?php

if (strtoupper($_SESSION['post']) != "ADMIN") {
header('Location: forbidden.php');
exit();
}


///*
// * query per te marr te dhenat nga tabelat costumers dhe product
// */

$QueryProdSell = "SELECT id ,
                         product_cost,
                         product_name ,
                         product_stoc,
                         product_brand,
                         product_origin,
                         product_id,
                         product_warranty,
                         costumer_id ,
                         costumer_quantity,
                         costumer_name,
                         costumer_surname,
                         costumer_date_buy
                    FROM costumers
                    inner JOIN products on products.id= costumers.product_id";

$resultProdSell = $conn->query($QueryProdSell);
if (!$resultProdSell) {
    echo "Ndodhi nje gabim me database-n";
    exit;
}
/*
 * kalimi i te dhenave ne array
 */
$data = array();
$prod = [];
$costum = [];

while ($row = $resultProdSell->fetch_assoc()) {

    $data['total']['costumersId'][$row['costumer_id']] = $row['costumer_id'];
    $data['total']['productBrand'][$row['product_brand']] = $row['product_brand'];
    $data['total']['productOrigin'][$row['product_origin']] = $row['product_origin'];
    $data['total']['category'][$row['product_name']] = $row['product_name'];
    $data['total']['costumerQuantity'] += $row['costumer_quantity'];
    $data['total']['totSellWin'] += ($row['product_cost'] * $row['costumer_quantity']);
    if (!isset($prod[$row['id']])) {
        $data['total']['totProductPrice'] += $row['product_cost'];
        $data['total']['totProductStoc'] += $row['product_stoc'];
        $data['total']['countProd'] += 1;
        $data['total']['prodPriceMes'] = $data['total']['totProductPrice'] / $data['total']['countProd'];
        $prod[$row['id']][''] = $row['id'];
    }
    if (!isset($costum[$row['costumer_id']])) {
        $data['total']['costumId'] .= $row['costumer_id'] . ';';
        $costum[$row['costumer_id']]['costumers'] = $row['costumer_id'];
    }
    $data['total']['mesSellWin'] = ($data['total']['totSellWin'] / count($data['total']['costumersId']));

    $data['costumers']['costumerId'][$row['costumer_id']]['firstname'] = $row['costumer_name'];
    $data['costumers']['costumerId'][$row['costumer_id']]['lastname'] = $row['costumer_surname'];
    $data['costumers']['costumerId'][$row['costumer_id']]['quantity'] += $row['costumer_quantity'];
    $data['costumers']['costumerId'][$row['costumer_id']]['win'] += ($row['product_cost'] * $row['costumer_quantity']);
    $data['costumers']['costumerId'][$row['costumer_id']]['prod'][$row['id']]['category'] = $row['product_name'];
    $data['costumers']['costumerId'][$row['costumer_id']]['prod'][$row['id']]['quantity'] += $row['costumer_quantity'];
    $data['costumers']['costumerId'][$row['costumer_id']]['prod'][$row['id']]['brand'] = $row['product_brand'];
    $data['costumers']['costumerId'][$row['costumer_id']]['prod'][$row['id']]['origin'] = $row['product_origin'];
    $data['costumers']['costumerId'][$row['costumer_id']]['prod'][$row['id']]['stoc'] += $row['product_stoc'];
    $data['costumers']['costumerId'][$row['costumer_id']]['prod'][$row['id']]['warranty'] = $row['product_warranty'];
    $data['costumers']['costumerId'][$row['costumer_id']]['prod'][$row['id']]['cost'] += $row['product_cost'];
    $data['costumers']['costumerId'][$row['costumer_id']]['prod'][$row['id']]['win'] += ($row['product_cost'] * $row['costumer_quantity']);

    $data['product']['prodId'][$row['id']]['category'] = $row['product_name'];
    $data['product']['prodId'][$row['id']]['quantity'] += $row['costumer_quantity'];
    $data['product']['prodId'][$row['id']]['brand'] = $row['product_brand'];
    $data['product']['prodId'][$row['id']]['origin'] = $row['product_origin'];
    $data['product']['prodId'][$row['id']]['stoc'] = $row['product_stoc'];
    $data['product']['prodId'][$row['id']]['cost'] = $row['product_cost'];
    $data['product']['prodId'][$row['id']]['warranty'] = $row['product_warranty'];
    $data['product']['prodId'][$row['id']]['win'] += ($row['product_cost'] * $row['costumer_quantity']);

    $data['product']['prodId'][$row['id']]['costumer'][$row['costumer_id']]['firstname'] = $row['costumer_name'];
    $data['product']['prodId'][$row['id']]['costumer'][$row['costumer_id']]['lastname'] = $row['costumer_surname'];
    $data['product']['prodId'][$row['id']]['costumer'][$row['costumer_id']]['quantity'] += $row['costumer_quantity'];
    $data['product']['prodId'][$row['id']]['costumer'][$row['costumer_id']]['win'] += ($row['product_cost'] * $row['costumer_quantity']);


}

?>

<div id="" class="gray-bg">

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Costumers</h2>

        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="card col-lg-12">
                <!--                    table-->

                <table class="table table-sm">
                    <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">nr</th>
                        <th scope="col">Costumer FirstName</th>
                        <th scope="col">Costumer LastName</th>
                        <th scope="col">Costumer Quantity</th>
                        <th scope="col">Costumer Total Pay</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 1;
                    foreach ($data as $keys => $val) {

                        foreach ($val['costumerId'] as $key => $value) {
                            ?>
                            <tr>
                                <td>
                                    <button class="btn" onclick="show('<?= $key ?>')">
                                        <i id="secplus<?= $key ?>"
                                           class="iconout fas fa-plus fa-ellipsis-h text-success"></i></button>
                                </td>
                                <th scope="row"><?php echo $i ?></th>
                                <td><?php echo $value['firstname'] ?></td>
                                <td><?php echo $value['lastname'] ?></td>
                                <td><?php echo $value['quantity'] ?> </td>
                                <?php

                                ?>
                                <td><?php echo $value['win'] ?> </td>
                                <?php

                                ?>

                            </tr>
                            <tr>

                                <td colspan="12"
                                    id="<?= $key ?>"
                                    style="display: none" class="tablein">
                                    <table style=" width:100%" class="table ">
                                        <thead style="color: black ; background: #dee1e3">
                                        <tr>
                                            <td></td>
                                            <td>Category</td>
                                            <td>Brand</td>
                                            <td>Quantity sell</td>
                                            <td>origin</td>
                                            <td>stoc</td>
                                            <td>cost</td>
                                            <td>win</td>


                                        </tr>
                                        </thead>

                                        <?php
                                        foreach ($value['prod'] as $item => $val) {
                                            ?>
                                            <tr style="background: #ffffff">
                                                <td>

                                                </td>


                                                <td><?php echo $val['category'] ?></td>
                                                <td><?php echo $val['brand'] ?></td>
                                                <td><?php echo $val['quantity'] ?></td>
                                                <td><?php echo $val['origin'] ?></td>
                                                <td><?php echo $val['stoc'] ?></td>
                                                <td><?php echo $val['cost'] ?></td>
                                                <td><?php echo $val['win'] ?></td>

                                            </tr>

                                            <?php


                                        }
                                        ?>
                                    </table>
                                </td>

                            </tr>
                            <?php $i++;
                        }
                    } ?>
                    <?php

                    foreach ($data as $dataKey => $dataVal) {


                        ?>
                        <tr>
                            <td></td>
                            <td style="font-weight:bold">Total:</td>
                            <td style="font-weight:bold"><a
                                    href="http://localhost/nilsi/WD1/index2.php?page=users&ids=<?php echo $dataVal['costumId'] ?>"
                                    target="_blank"><?php echo count($dataVal['costumersId']); ?></td>
                            <td style="font-weight:bold"></td>
                            <td style="font-weight:bold"><?php echo $dataVal['costumerQuantity']; ?> </td>
                            <td style="font-weight:bold"> <?php echo $dataVal['totSellWin']; ?> leke
                                (Avg: <?= $dataVal['mesSellWin'] ?> leke)
                            </td>

                        </tr>
                        <?php
                        break;
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="card col-lg-12">
            <table class="table table-sm">
                <thead>
                <tr>
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
                foreach ($data as $dataKey => $dataValue) {
                    foreach ($dataValue['prodId'] as $key => $value) {

                        ?>
                        <tr>
                            <td>
                                <button class="btn" onclick="showProductTab('<?= $key ?>')"><i
                                        id="secplustab<?= $key ?>"
                                        class="iconout fas fa-plus fa-ellipsis-h text-success"></i></button>
                            </td>
                            <th scope="row"><?php echo $i ?></th>
                            <td><?php echo $value['category'] ?></td>
                            <td><?php echo $value['brand'] ?></td>
                            <td><?php echo $value['cost'] ?> leke</td>
                            <td><?php echo $value['warranty'] ?></td>
                            <td><?php echo $value['quantity'] ?></td>
                            <td><?php echo $value['win'] ?> leke</td>
                            <td><?php echo $value['stoc'] ?></td>
                            <td><?php echo $value['origin'] ?></td>
                        </tr>
                        <tr>

                            <td colspan="12"
                                id="prodTab<?= $key ?>"
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

                                    foreach ($value['costumer'] as $cost => $costumer) {


                                        ?>
                                        <tr style="background: #ffffff">
                                            <td>

                                            </td>
                                            <td><?php echo $costumer['firstname'] ?></td>
                                            <td><?php echo $costumer['lastname'] ?></td>
                                            <td><?php echo $costumer['quantity'] ?></td>
                                        </tr>

                                        <?php
                                    }

                                    ?>
                                </table>
                            </td>

                        </tr>
                        <?php $i++;
                    }
                } ?>
                <?php

                foreach ($data as $DKey => $DVal) {

                    ?>
                    <tr>
                        <td></td>
                        <td style="font-weight:bold">Total:</td>
                        <td style="font-weight:bold"><?php echo count($DVal['category']); ?></td>
                        <td style="font-weight:bold"><?= count($DVal['productBrand']) ?></td>
                        <td style="font-weight:bold"><?php echo $DVal['prodPriceMes'] ?>
                            leke
                        </td>
                        <td style="font-weight:bold"></td>
                        <td style="font-weight:bold"><?php echo $DVal['costumerQuantity'] ?></td>
                        <td style="font-weight:bold"> <?php echo $DVal['totSellWin'] ?> leke
                            (Avg: <?= $dataVal['mesSellWin'] ?> leke)
                        </td>
                        <td style="font-weight:bold"> <?php echo $DVal['totProductStoc']; ?></td>
                        <td style="font-weight:bold"> <?php echo count($DVal['productOrigin']); ?></td>
                    </tr>
                    <?php break;
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <div style="height: 50px">
    </div>
    <?php
//    include 'includes/footer.php';
    ?>
</div>

<?php
//include 'includes/javascript.js';
include 'javascript.js';
?>



