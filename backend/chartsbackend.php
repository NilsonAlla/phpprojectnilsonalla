<?php
error_reporting(0);
session_start();
require_once "../DB/database.php";

if (!isset($_SESSION['id']) || strtoupper($_SESSION['post']) != "ADMIN") {
    header('Location: ../login.php?not-logged-in');
    exit();
}

$queryProducts = "SELECT product_name,product_brand,product_origin,product_stoc FROM products where 1=1";
$resultProducts = $conn->query($queryProducts);
/*
 * kontrollojme nese eshte ekzekutuar query apo jo
 */
if(!$resultProducts){
    header('Content-Type: application/json', '', '500');
    echo json_encode(['message' => 'Database Error.',]);
    exit();
}


$queryCostumers = "SELECT costumer_name,
                            costumer_surname,
                            product_id,
                            costumer_id,
                            costumer_date_buy , 
                            costumer_quantity,
                            product_name,
                            product_brand,
                            product_origin,
                            product_stoc,
                            id
                             FROM costumers 
                             left join products
                             ON id = costumer_id ORDER BY costumer_date_buy asc ";

$resultCostumers = $conn->query($queryCostumers);

/*
 * kontrollojme nese eshte ekzekutuar query apo jo
 */
if(!$resultCostumers){
    header('Content-Type: application/json', '', '500');
    echo json_encode(['message' => 'Database Error.',]);
    exit();
}
$products = array();
$productCategory = array();
$productsSalary = array();
$productOrigin = array();
$productQuantity = array();
$costumers = array();


/*
 * mbushja e charts me te dhenat ne baze te kategorive te produkteve
 */

if ($_POST['action'] == "product") {

    while ($row = $resultCostumers->fetch_assoc()) {

        $productsSalary [$row['costumer_date_buy']]['total_salary'] += $row['costumer_quantity'];

        $costumers[$row['costumer_id']][$row['costumer_name']]['total'] += $row['costumer_quantity'];
    }

    foreach ($productsSalary as $key => $row) {
        $salary [] = array(
            'year' => $key,
            'totalSalary' => $row['total_salary'],

        );
    }


    foreach ($costumers as $key => $row) {
        foreach ($row as $value => $val) {
            $costm [] = array(
                'name' => $value,
                'total' => $val['total'],

            );
        }
    }


    /*
     * mbushja e charts me te dhenat ne baze te brandeve
     */

    while ($row = mysqli_fetch_assoc($resultProducts)) {
        $productCategory['product_name'][$row['product_name']]['total_category'] += $row['product_stoc'];
        $productCategory['product_name'][$row['product_name']][$row['product_brand']]['total_brand'] += $row['product_stoc'];

        $productOrigin [$row['product_origin']]['total'] += $row['product_stoc'];
    }
    $product = array();
    foreach ($productCategory as $key => $value) {
        $product = $value;
    }


    /*
     * mbushja e charts me te dhenat ne baze te origjines se produktit
     */

    foreach ($productOrigin as $key => $row) {
        $origin[] = array(
            'product_origin' => $key,
            'total' => $row['total'],
        );
    }

    $Data['product'] = $product;
    $Data['salary'] = $salary;
    $Data['origin'] = $origin;
    $Data['costumers'] = $costm;

    if ($errors){
        header('Content-Type: application/json', '', '500');
        echo json_encode(['message' => $errors,]);
        exit();
    }

    echo json_encode($Data);
}


?>