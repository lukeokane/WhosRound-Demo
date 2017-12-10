<?php

require_once("includes/session.php");
require_once("includes/configuration.php");

$establishID = filter_var($_SESSION['establishmentID'], FILTER_VALIDATE_INT);
$custID = filter_var($_SESSION['id'], FILTER_VALIDATE_INT);
$custName = filter_var($_SESSION['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$option = filter_input(INPUT_POST, "option", FILTER_SANITIZE_NUMBER_INT);
$drinkID = filter_input(INPUT_POST, "drinkID", FILTER_SANITIZE_NUMBER_INT);
$drinkTypeID = filter_input(INPUT_POST, "drinkTypeID", FILTER_SANITIZE_NUMBER_INT);

$overwriteQuantity = filter_input(INPUT_POST, "overwriteQuantity", FILTER_VALIDATE_BOOLEAN);
$value = filter_input(INPUT_POST, "value", FILTER_SANITIZE_NUMBER_INT);
$specialInstr = filter_input(INPUT_POST, "specialInstr", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$basketJSON = filter_input(INPUT_POST, "basketJSON");

if (isset($custName, $custID, $establishID, $option)) {
    switch ($option) {
        case 1:
            if (isset($drinkID, $drinkTypeID, $value, $overwriteQuantity)) {

                $response = changeItem($db, $establishID, $drinkID, $drinkTypeID, $value, $overwriteQuantity);
                echo $response;
            } else {
                $JSON->result = "error";
                $JSON->message = "error occurred.";
                echo json_encode($JSON);
            }
            break;
        case 2:
            if (isset($drinkID, $drinkTypeID, $value)) {
                $response = decrementItem($db, $establishID, $drinkID, $drinkTypeID, $value);
                echo $response;
            } else {
                $JSON->result = "error";
                $JSON->message = "error occurred.";
                echo json_encode($JSON);
            }
            break;
        case 3:
            if (isset($drinkID, $drinkTypeID, $specialInstr)) {
               $response = insertSpecialInstr($drinkID, $drinkTypeID, $specialInstr);
               echo $reponse;
               
            } else {
                $JSON->result = "error";
                $JSON->message = "error occurred.";
                echo json_encode($JSON);
            }
            break;
        case 4:
            if (isset($basketJSON)) {
                
                //Overwrite basket
                $_SESSION["basket"] = array();
                $basketItems = json_decode($basketJSON, true);
                            
                foreach ($basketItems as $item) {
                    
                 $response = changeItem($db, $establishID, $item["drinkID"], $item["drinkTypeID"], $item["quantity"], true);
				 insertSpecialInstr($item["drinkID"], $item["drinkTypeID"], $item["speciInstr"]);
				}
                
               echo $response;
                
            } else {
                $JSON->result = "error";
                $JSON->message = "error occurred.";
                echo json_encode($JSON);
            }
            break;
        default:
            $JSON->result = "error";
            $JSON->message = "error occurred.";
            echo json_encode($JSON);
            break;
    }
} else {
    $JSON->result = "error";
    $JSON->message = "error occurred.";
}

function changeItem($db, $establishID, $drinkID, $drinkTypeID, $value, $overwriteQuantity) {

    foreach ($_SESSION['basket'] as &$item) {

        if ($item['drinkID'] == $drinkID && $item['drinkTypeID'] == $drinkTypeID) {
            if ($overwriteQuantity) {
                $item['quantity'] = $value;
            } else {
                $item['quantity'] += $value;
            }

            $price = getPrice($db, $establishID, $drinkID, $drinkTypeID, $item['quantity']);
            $item["price"] = $price;

            if ($price === null) {
                $JSON->result = "error";
                $JSON->message = "error updating order.";
            } else {
                $JSON->result = "success";
                $JSON->quantity = $item['quantity'];
                $JSON->price = getPrice($db, $establishID, $drinkID, $drinkTypeID, $item['quantity']);
                $JSON->subtotal = basketSubTotal();
                $JSON->total = basketTotal();
            }

            return json_encode($JSON);
        }
    }

    // If reaching here then basket did not already contain the order.

    if (getPrice($db, $establishID, $drinkID, $drinkTypeID, $value) != null) {

        array_push($_SESSION['basket'], array("drinkID" => $drinkID,
            "drinkTypeID" => $drinkTypeID,
            "quantity" => $value,
            "price" => getPrice($db, $establishID, $drinkID, $drinkTypeID, $value),
            "speciInstr" => ""));

        $JSON->result = "success";
        $JSON->quantity = $value;
        $JSON->price = getPrice($db, $establishID, $drinkID, $drinkTypeID, (doubleval($value)));
        $JSON->subtotal = basketSubTotal();
        $JSON->total = basketTotal();

        return json_encode($JSON);
    } else {
        $JSON->result = "error";
        $JSON->message = "error updating order.";

        return json_encode($JSON);
    }
}

function decrementItem($db, $estabID, $drinkID, $drinkTypeID, $value) {

    foreach ($_SESSION['basket'] as $index => &$item) {
        if ($item['drinkID'] == $drinkID && $item['drinkTypeID'] == $drinkTypeID) {


            $item['quantity'] -= $value;

            if ($item['quantity'] <= 0) {
                unset($_SESSION['basket'][$index]);
                $JSON->result = "success";
                $JSON->quantity = $item['quantity'];
                $JSON->price = getPrice($db, $estabID, $drinkID, $drinkTypeID, $item['quantity']);
                $JSON->subtotal = basketSubTotal();
                $JSON->total = basketTotal();

                return json_encode($JSON);
                return;
            } else {
                $price = getPrice($db, $estabID, $drinkID, $drinkTypeID, $item['quantity']);
                $item['price'] = $price;

                if ($price === null) {
                    $JSON->result = "error";
                    $JSON->message = "error updating order.";
                } else {
                    $JSON->result = "success";
                    $JSON->quantity = $item['quantity'];
                    $JSON->price = $item['price'];
                    $JSON->subtotal = basketSubTotal();
                    $JSON->total = basketTotal();
                }
                return json_encode($JSON);
                return;
            }
        }
    }
    // Called when basket item not found, return standard error message.
    $JSON->result = "error";
    $JSON->message = "error occurred.";

    return json_encode($JSON);
}

function insertSpecialInstr($drinkID, $drinkTypeID, $request) {
    foreach ($_SESSION['basket'] as &$item) {
        if ($item['drinkID'] == $drinkID && $item['drinkTypeID'] == $drinkTypeID) {
            $item['speciInstr'] = $request;
            $JSON->result = "success";
            $JSON->instruction = $request;
            return json_encode($JSON);
        }
    }
    // Called when basket item not found, return standard message.
    $JSON->result = "warning";
    $JSON->message = "No basket item found on insert special instr.";
    return json_encode($JSON);
}

function getPrice($db, $estabID, $drinkID, $drinkTypeID, $quantity) {
    $query = "SELECT (price * :quantity) as total from menuitems WHERE drinkID = :drinkID AND drinkTypeID = :drinkTypeID AND establishmentID = :estabID";

    $stmt = $db->prepare($query);
    $stmt->bindValue(":quantity", $quantity, PDO::PARAM_INT);
    $stmt->bindValue(":drinkID", $drinkID, PDO::PARAM_INT);
    $stmt->bindValue(":drinkTypeID", $drinkTypeID, PDO::PARAM_INT);
    $stmt->bindValue(":estabID", $estabID, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return number_format($result['total'], 2);
}

function basketSubTotal() {
    $total = 0.00;
    foreach ($_SESSION['basket'] as &$item) {
        $total += $item['price'];
    }

    return number_format($total, 2);
}

function basketTotal() {
    $total = basketSubTotal();

    // Algorithm for service charge here...
    // For now it will be €0.10
    if ($total > 0.00) {
        $total += 0.10;
    }
    return number_format($total, 2);
}

function quantityTotal()
{
    $quantity = 0;
    foreach ($_SESSION['basket'] as $item) {
        $quantity += $item["quantity"];
    }
    
    return $quantity;
}
?>