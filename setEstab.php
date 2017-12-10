<?php

require_once("includes/session.php");

$establishID = filter_input(INPUT_POST, "establishmentID", FILTER_SANITIZE_NUMBER_INT);

// Get old establishment ID (if any), if the establishment ID changes then empty basket.
$currentEstabID = filter_var($_SESSION['establishmentID'], FILTER_VALIDATE_INT);

if ($currentEstabID != $establishID)
{ 
$_SESSION["establishmentID"] = $establishID;
// Make new basket on establishment change.
$_SESSION["basket"] = array();  
}

$JSON = new stdClass();
$JSON->result = "success";
$JSON->message = "";

echo json_encode($JSON);