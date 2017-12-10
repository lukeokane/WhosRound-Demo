<?php
require_once("includes/session.php");
require_once("includes/configuration.php");


$categoryName = filter_input(INPUT_POST, "categoryName", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$establishID =  filter_var($_SESSION['establishmentID'], FILTER_VALIDATE_INT);

$queryEstablishmentDrinkNames = 'SELECT dc.categoryName from drinkcategories dc, menuitems mi, drinks d
WHERE  mi.establishmentID = :establishID AND mi.drinkID = d.drinkID 
AND d.categoryID = dc.categoryID AND dc.categoryName like CONCAT("%", :categoryName, "%") GROUP BY categoryName';
$stmt = $db->prepare($queryEstablishmentDrinkNames);
$stmt->bindValue(':establishID', $establishID);
$stmt->bindValue(':categoryName', $categoryName);
$stmt->execute();
$results = $stmt->fetchAll();
$stmt->closeCursor();

echo json_encode($results);
?>
