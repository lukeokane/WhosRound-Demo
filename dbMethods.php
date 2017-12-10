<?php

require_once("includes/session.php");
require_once("includes/configuration.php");

$option = filter_input(INPUT_POST, "option", FILTER_SANITIZE_NUMBER_INT);


// Default error message.
$JSON->result = "error";
$JSON->message = "an error occurred";

// NOTE: $result should be an object to be json encoded.
// 
// e.g  $object->result = "resulthere"
//      $object->message = "messagehere"

switch ($option) {
    case 1:
        $orderID = filter_input(INPUT_POST, "orderID", FILTER_SANITIZE_NUMBER_INT);

        if (isset($orderID)) {
            $result = setOrderCompleted($db, $orderID);
            echo json_encode($result);
        } else {
            echo json_encode($JSON);
        }
        break;
    case 2:
        
        
        $establishID = filter_var($_SESSION['establishmentID'], FILTER_SANITIZE_NUMBER_INT);
        $categoryID = filter_input(INPUT_POST, "categoryID", FILTER_SANITIZE_NUMBER_INT);
        $drinkName = filter_input(INPUT_POST, "drinkName", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        if (isset($establishID, $categoryID, $drinkName)) {
          $results = getDrinkByNameAndCategory($db, $drinkName, $establishID, $categoryID);
          echo json_encode($results);     
        } else {
            echo json_encode($JSON);
        }
       break;
    default:
        echo json_encode($JSON);
        break;
}

function setOrderCompleted(&$db, $orderID) {
    $query = "UPDATE orders set completed = 1 WHERE orderID = :orderID";

    $stmt = $db->prepare($query);
    $stmt->bindValue(":orderID", $orderID, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $JSON->result = "success";
        $JSON->message = "";
    } else {
        $JSON->result = "error";
        $JSON->message = "Order not set as completed.";
    }

    return $JSON;
}

function getDrinkByNameAndCategory($db, $drinkName, $establishID, $categoryID) {
    // Query drinks by establishment, category and beginning of drink name
    $query = 'SELECT d.drinkID, name, alcoholPercentage, img, calories '
            . 'FROM drinks d, drinkcategories dc, menuitems mi '
            . 'WHERE mi.establishmentID = :establishID '
            . 'AND dc.categoryID = :categoryID '
            . 'AND d.categoryID = :categoryID '
            . 'AND d.name like CONCAT("%", :drinkName, "%") '
            . 'GROUP BY d.drinkID, name, alcoholPercentage, calories';
    
    $stmt = $db->prepare($query);
    $stmt->bindValue(':establishID', $establishID);
    $stmt->bindValue(':categoryID', $categoryID);
    $stmt->bindValue(':drinkName', $drinkName);
    $stmt->execute();
    $results->result = "success";
    $results->results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    return $results;
}

function getDrinkCategories($db, $establishID)
{
    
}