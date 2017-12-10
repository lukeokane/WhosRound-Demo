<?php
require_once("includes/configuration.php");
require_once("includes/session.php");
require_once("basketMethods.php");

$basketItems = array();

$_SESSION['testDelivery'] = "Collect";

$subtotal = basketSubTotal();
$total = basketTotal();

$totalQuantity = 0;

//Loop through basket, query for each item's info
foreach ($_SESSION['basket'] as $sessionBasketItem) {

    $queryForBasketItemInfo = "SELECT name, typeName, price, img FROM drinks d, drinktypes dt, menuitems mi "
            . "WHERE d.drinkID = mi.drinkID AND mi.drinkTypeID = dt.drinkTypeID AND mi.drinkID = :drinkID "
            . "AND mi.drinkTypeID = :drinkTypeID AND mi.establishmentID = :establishmentID";
    $stmt = $db->prepare($queryForBasketItemInfo);
    $stmt->bindValue(':drinkID', $sessionBasketItem["drinkID"]);
    $stmt->bindValue(':drinkTypeID', $sessionBasketItem["drinkTypeID"]);
    $stmt->bindValue(':establishmentID', $_SESSION['establishmentID']);
    $stmt->execute();
    $result = $stmt->fetch();
    $stmt->closeCursor();

    $itemInfo = array($sessionBasketItem["drinkID"], $sessionBasketItem["drinkTypeID"], $result['name'], $result['typeName'], $result['price'], $sessionBasketItem["quantity"], $sessionBasketItem["speciInstr"], $result["img"]);

    array_push($basketItems, $itemInfo);
    
    $totalQuantity += $sessionBasketItem["quantity"];
    
}

$si = (isset($_SESSION["id"]) && isset($_SESSION["username"])) ? "true" : "false";
?>

<!DOCTYPE html>
<html lang="en">
    <head> 
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">

        <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
        <link rel="stylesheet" href="css/basket.css">

        <title>Who'sRound? - Basket</title>
    </head>
    <body>

        <header>
            <div style='padding: 0px; color:#ffffff; margin: 0px;'>
                <div style="padding: 0px;" id='top-navbar' class="col-md-12 column">
                    <table  style="padding-top: 0px; margin-bottom:0px;" class="table table-bordered table-hover" id="tab_logic">
                        <thead>
                            <tr>
                                <th style="vertical-align: middle;">
                                    <i style="font-size:30px;" class="fa fa-bars " aria-hidden="true"></i>
                                </th>
                                <th id="profileSection" data-si="<?php echo $si; ?>" style='font-size: 10px; padding-left:0px; padding-right: 2px; padding-bottom: 0px;' class="text-center">
                                    <i class="fa fa-user "  aria-hidden="true"></i>
                                    <div class=' light'>
                                          <?php 
                                            if (isset($_SESSION['username']))
                                            {
                                                echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'utf-8');
                                            }
                                            else
                                            {
                                                echo "Sign In";
                                            }
                                        ?>
                                    </div>
                                </th>
                                <th style=" padding-left:0px; padding-right: 0px; vertical-align: middle;" class="text-center bold">
                                    <?php echo htmlspecialchars($_SESSION['establishment'], ENT_QUOTES, 'utf-8') ?>
                                </th>
                                <th style="text-align: right; vertical-align: middle;">
                                    <i style="font-size:30px;" class="fa fa-shopping-cart" aria-hidden="true"><span style=" font-size: 15px; vertical-align:top; background-color:#FA8E4B;" id="basketAmtIcon" class="badge"><?php echo $totalQuantity; ?></span></i>
                                </th>
                            </tr>
                        </thead>	
                    </table>
                </div>       
            </div>
            <div style='padding: 0px; margin: 0px;' id="navbar-whole" class="container-fluid">

                <table id='drink-navbar'   style=" padding-top: 0px; margin-bottom:0px;" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="padding-top:0px; padding-bottom:5px" class="bold">
                                <a id="backbutton" href="drinkCategory.php"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back</a>
                            </th>     
                        </tr>
                    </thead>	
                </table>
            </div>  
        </header>

        <!-- START ITEM CONTAINER BOX  -->
        <div class="container-fluid body-content">
            <?php foreach ($basketItems as $basketItem): ?>
                <?php
                $drinksID = $basketItem[0];
                $drinksTypeID = $basketItem[1];
                $imageName = strtolower($basketItem[2]);
                $drinkName = $basketItem[2];
                $drinkMeasurement = $basketItem[3];
                $drinkSinglePrice = $basketItem[4];
                $drinkQuantity = $basketItem[5];
                $drinkSpecialRequests = $basketItem[6];
				$drinkImage = $basketItem[7];

                echo '<div class="item row" data-drinkid="' . $drinksID . '" data-drinktypeid="' . $drinksTypeID . '" data-drinkprice="' . $drinkSinglePrice . '">';
                echo '<div class="col-3 drinkImage align-self-center text-center">';
                echo '<img src="images/' . $drinkImage . '"/>';
                echo '</div>';
                echo '<div class="col-5 drinkInfo">';
                echo '<div class="drinkName row">' . $drinkName . '</div>';
                echo '<div class="measurement row">' . $drinkMeasurement . '</div>';
                echo '<div class="singlePrice row">&euro;' . $drinkSinglePrice . '</div>';
                echo '<div class="row"><div class="input-group">';
                echo '<span class="input-group-addon  minusIcon"><i style="font-size: 15px;" class="fa fa-minus" aria-hidden="true"></i></span>';
                echo '<input type="number" value="' . $drinkQuantity . '"  class="form-control quantityInput " >';
                echo '<span class="input-group-addon addIcon"><i style="font-size: 15px;" class="fa fa-plus" aria-hidden="true"></i></span>';
                echo '</div></div>';
                echo '</div>';
                echo '<div class="col-4 price bold align-self-center">';
                echo '<div class="itemTotPrice">&euro;' . number_format(($drinkQuantity * $drinkSinglePrice), 2) . '</div><img class="deleteIcon" src="images/deletebin.png">';
                echo '</div>';
                echo '<div class="itemSpecReq col-12"><input class="form-control specInstr input-sm" placeholder="Add a special request..." value="' . $drinkSpecialRequests . '" id="inputsm" type="text"></div>';
                echo '</div>';
                echo '<hr>';
                ?>
            <?php endforeach; ?>
        </div>
        <!-- END ITEM CONTAINER BOX  -->


        <footer>
            <div class="footercontent">
                <div class="row">
                    <div class="col-6 collectbtndiv">
                        <button type="button" class="btn collectbtn float-right round ">Collect at Bar</button>
                    </div>
                    <div class="col-6 tablebtndiv">
                        <button type="button" class="btn tablebtn float-left round">Table Service</button>
                    </div>
                </div>
                <div class="basketSubtotal row">
                    <div class="col-6 float-right">
                        Basket Subtotal
                    </div>
                    <div class="basketSubTot col-3 float-right">
                        &euro;<?php echo $subtotal; ?>
                    </div>
                </div>
                <div class="transactionFee row">
                    <div class="col-6 float-right">
                        Transaction Fee
                    </div>
                    <div class="transFee col-3 float-right">
                        &euro;0.10
                    </div>
                </div>
                <div class="total row">
                    <div class="col-6 float-right">
                        Total
                    </div>
                    <div class="basketTot col-3 bold float-right">
                        &euro;<?php echo $total; ?>
                    </div>
                </div>

                <div style="z-index:2; padding: 10px; text-align: center; background-color: #FA8E4B; font-size: 17px; color: white; bottom:0px; text-decoration: none;" class="bold col-12">
                    Add or Select New Card
                </div>
                <a id="completeOrder">
                    <div style="z-index:2; padding: 10px; text-align: center; background-color: #424C58; font-size: 17px; color: white; bottom:0px; text-decoration: none;" class="bold col-12">
                        <?php if ($si === "true") { echo "Pay with Debit Card ending in 1234"; } else { echo "Pay with Credit/Debit Card"; } ?>    
                    </div>
                </a></div></footer>

        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script type="text/javascript">var itemBasket = $.parseJSON('<?php echo json_encode($_SESSION["basket"]); ?>');</script>
        <script src="js/basket.js"></script>
        
    </body>
</html>