<?php

require_once("includes/session.php");
require_once("includes/configuration.php");

$estabID = $_SESSION['establishID'] = 1;
$drinkID = filter_input(INPUT_POST, "drinkID", FILTER_SANITIZE_NUMBER_INT);

$query = "SELECT mi.drinkID, mi.drinkTypeID, dt.typeName, mi.price"
        . ", 0 as quantity FROM menuitems mi, drinktypes dt "
        . "WHERE mi.drinkTypeID = dt.drinkTypeID AND "
        . "mi.establishmentID = :establishID AND mi.drinkID = :drinkID";

$stmt = $db->prepare($query);
$stmt->bindValue(":establishID", $estabID, PDO::PARAM_INT);
$stmt->bindValue(":drinkID", $drinkID, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);
