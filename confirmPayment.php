<?php
require_once("includes/configuration.php");
require_once("includes/session.php");

date_default_timezone_set('Europe/London');

$currentDateTime = new DateTime();

if (!isset($_SESSION['username']) && !isset($_SESSION['id']))
{
    // nsi (not signed in) is true.
    $_SESSION['nsi'] = true;
    // The URL the person should be redirected to
    $_SESSION['nsiRelocation'] = "basket.php";
    header('Location: signinPage.php');
    die();
}

if (sizeof($_SESSION["basket"]) === 0)
{
    header('Location: drinkCategory.php');
    die();
}

//Insert the order into the order table, this will have the orderID that was generated above, due to the auto-incrementation of the orderID in the db
$queryInsertOrder = "INSERT INTO orders(establishmentID, accountID, delivery, orderedAt) VALUES (:establishmentID, :accountID, :delivery, :orderedAt)";
$stmt2 = $db->prepare($queryInsertOrder);
$stmt2->bindValue(':establishmentID', $_SESSION['establishmentID']);
$stmt2->bindValue(':accountID', $_SESSION['id']);
$stmt2->bindValue(':delivery', "Collect");
$stmt2->bindValue(':orderedAt', $currentDateTime->format('Y-m-d H:i:s'));
$stmt2->execute();
$newOrderNumber = $db->lastInsertId();
$stmt2->closeCursor();


//Insert the individual orderItems into the orderItems table, using the orderID generated in the previous query for each row
foreach ($_SESSION['basket'] as $basketItem) {
    $queryInsertOrderItem = "INSERT INTO orderitems(orderID, drinkID, drinkTypeID, quantity, specialRequests) VALUES (:orderID, :drinkID, :drinkTypeID, :quantity, :specialRequests)";
    $stmt3 = $db->prepare($queryInsertOrderItem);
    $stmt3->bindValue(':orderID', $newOrderNumber);
    $stmt3->bindValue(':drinkID', $basketItem["drinkID"]);
    $stmt3->bindValue(':drinkTypeID', $basketItem["drinkTypeID"]);
    $stmt3->bindValue(':quantity', $basketItem["quantity"]);
    $stmt3->bindValue(':specialRequests', $basketItem["speciInstr"]);
    $stmt3->execute();
    $stmt3->closeCursor();
}

//Empty basket
$_SESSION["basket"] = array();
?>


<?php
require_once("includes/session.php");
require_once("includes/configuration.php");
require_once("basketMethods.php");

$drinkType = filter_input(INPUT_GET, "drinkType", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$establishID = $_SESSION['establishmentID'];

$queryCategoryID = 'SELECT categoryID FROM drinkcategories WHERE categoryName = :drinkType';
$stmt1 = $db->prepare($queryCategoryID);
$stmt1->bindValue(':drinkType', $drinkType);
$stmt1->execute();
$result = $stmt1->fetch();
$stmt1->closeCursor();

$categoryID = filter_var($result['categoryID'], FILTER_VALIDATE_INT);

$queryEstablishmentDrinkType = 'SELECT d.drinkID, name, alcoholPercentage, calories '
        . 'FROM drinks d, drinkcategories dc, menuItems mi '
        . 'WHERE mi.establishmentID = :establishID '
        . 'AND dc.categoryID = :categoryID '
        . 'AND d.categoryID = :categoryID '
        . 'GROUP BY d.drinkID, name, alcoholPercentage, calories';

//$queryEstablishmentDrinkType = 'SELECT drinkID, name, alcoholPercentage, calories, categoryName '
//        . 'FROM drinks d, drinkcategories dc, menuItems mi '
//        . 'WHERE d.drinkID = mi.drinkID '
//        . 'AND mi.establishmentID = :establishID '
//        . 'AND dc.categoryID = :categoryID '
//        . 'AND d.categoryID = :categoryID';
//. 'GROUP BY drinkID, name, alcoholPercentage, calories, categoryName';
$stmt2 = $db->prepare($queryEstablishmentDrinkType);
$stmt2->bindValue(':establishID', $establishID);
$stmt2->bindValue(':categoryID', $categoryID);
$stmt2->execute();
$results = $stmt2->fetchAll();
$stmt2->closeCursor();
?>

<!DOCTYPE html>
<html lang="en">
    <head> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
        <link rel="stylesheet" href="css/confirmPayment.css">
        <title>Who'sRound? - Drink Menu</title>

    </head>
    <body>
        <nav class="navbar fixed-top">
            <div style='padding: 0px; height:500px; margin: 0px;' id="navbar-whole" class="container-fluid">
                <div style="padding: 0px;" id='top-navbar' class="col-md-12 column">
                    <table  style="padding-top: 0px; margin-bottom:0px;" class="table table-bordered table-hover" id="tab_logic">
                        <thead>
                            <tr>
                                <th style="vertical-align: middle;">
                                    <i style="font-size:30px;" class="fa fa-bars " aria-hidden="true"></i>
                                </th>
                                <th style='padding-left:0px; padding-right: 2px;' class="text-center">
                                    <i class="fa fa-user "  aria-hidden="true"></i>
                                    <div class=' light'>
<?php
if (isset($_SESSION['username'])) {
    echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'utf-8');
} else {
    echo "Sign In";
}
?>
                                    </div>
                                </th>
                                <th style=" padding-left:0px; padding-right: 0px; vertical-align: middle;" class="text-center bold">
                                        <?php echo htmlspecialchars($_SESSION['establishment'], ENT_QUOTES, 'utf-8'); ?>
                                </th>
                                <th style=" width: 35%;padding-left:0px; padding-right: 0px; vertical-align: middle;" class="text-center bold">

                                </th>
                            </tr>
                        </thead>	
                    </table>
                </div>       
            </div>
        </nav>

        <div id="pageCenter">
            <div id="orderNumberMsg" class="row">
                <div class="col-12">
                    Order No. <?php echo $newOrderNumber ?>  
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-12">
                    <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                </div>
            </div>
            <br>
            <div class="row">
                <div id="completedSuccessfully" class="col-12">
                    Completed Successfully
                </div>
            </div>
            <div class="row">
                <div id="orderOnWayMSG" class="col-12">
                    Your order is on its way!!
                </div>
            </div>
            <div  id="emailMSG" class="row">
                <div class="col-12">
                    Your order receipt will be <br>
                    forwarded to your email
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                   <a href="drinkCategory.php" id="menubtn" class="btn btn-lg center-block">Back to Menu</a>
                </div>
            </div>

        </div>



        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>        
    </body>
</html>