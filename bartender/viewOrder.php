<?php
require_once("../includes/configuration.php");
require_once("../includes/session.php");

$establishID = filter_var($_SESSION['staffEstablishmentID'], FILTER_SANITIZE_FULL_SPECIAL_CHARS); // get from session when pub picker is setup
$establishName = filter_var($_SESSION['staffEstablishment'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$staffUsername = filter_var($_SESSION['staffUsername'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$orderID = filter_input(INPUT_GET, "orderID", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$querySingleOrder = 'SELECT name, typeName, quantity, specialRequests, img '
        . 'FROM drinks d, drinktypes dt, orderitems oi '
        . 'WHERE oi.orderID = :orderID '
        . 'AND oi.drinkID = d.drinkID '
        . 'AND oi.drinkTypeID = dt.drinkTypeID';
$stmt = $db->prepare($querySingleOrder);
$stmt->bindValue(':orderID', $orderID);
$stmt->execute();
$order = $stmt->fetchAll();
$stmt->closeCursor();

$queryOrderDetails = 'SELECT username, delivery, orderID '
        . 'FROM accounts a, orders o '
        . 'WHERE a.accountID = o.accountID '
        . 'AND o.orderID = :orderID';
$stmt2 = $db->prepare($queryOrderDetails);
$stmt2->bindValue(':orderID', $orderID);
$stmt2->execute();
$details = $stmt2->fetch();
$stmt2->closeCursor();
?>

<!DOCTYPE html>
<html lang="en">
    <head> 
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <link rel="manifest" href="../json/manifest.json">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">

        <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
        <link rel="stylesheet" href="../css/viewOrder.css">

        <title>Who'sRound? - Bartender - Order</title>
    </head>
    <body>
        <!-- Modal -->
        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="vertical-alignment-helper">
                <div class="modal-dialog vertical-align-center">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span>

                            </button>
                            <h4 class="modal-title" id="myModalLabel">Confirm Order is Complete?</h4>

                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-xs-6">
                                    <button id="complBtn" data-orderid=" <?php echo htmlspecialchars($orderID, ENT_QUOTES, 'utf-8') ?> "  style="background-color: #33C4B3; color: white;" type="button" class="btn btn-lg">
                                        <span class="glyphicon glyphicon-ok"></span> Yes
                                    </button>
                                </div>
                                <div id="notComplBtn" class="col-xs-6">
                                    <button style="background-color: #FA8E4B; color: white;" type="button" data-dismiss="modal" class="btn btn-lg">
                                        <span class="glyphicon glyphicon-remove"></span> No
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <img id="confirmDone" data-toggle="modal" data-target="#myModal" src="../images/greentick.png">

        <nav class="navbar navbar-default  navbar-fixed-top" role="navigation">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">		                 
                    <div style="padding-top: 0.4em; font-size: 30px; " class="backbutton" class="navbar-brand navbar-brand-left">
                        <span class="glyphicon glyphicon glyphicon-user"></span> <?php echo htmlspecialchars($staffUsername, ENT_QUOTES, 'utf-8') ?>
                    </div>

                    <div style="font-size: 30px; top:0px; margin-left: -4em; margin-top: 1%" id="navbar-centre" class="navbar-brand bold navbar-brand-centered"><?php echo htmlspecialchars($establishName, ENT_QUOTES, 'utf-8') ?>
                    </div>

                </div>
            </div><!-- /.container-fluid -->
        </nav>

        <nav style=" top: 70px;" class="navbar navbar-default headersNav  navbar-fixed-top  " role="navigation">
            <div class="row">
                <div class="text-center orderDetails col-sm-4"><?php echo htmlspecialchars($details['username'], ENT_QUOTES, 'utf-8') ?></div>

<?php
if ($details['delivery'] == "Collect") {
    echo '<div class="text-center orderDetails col-sm-4"><b>' . htmlspecialchars($details['delivery'], ENT_QUOTES, 'utf-8') . '</b></div>';
} else {
    echo '<div class="text-center orderDetails col-sm-4"><b>Table No. ' . htmlspecialchars($details['delivery'], ENT_QUOTES, 'utf-8') . '</b></div>';
}
?>

                <div class="text-center orderDetails col-sm-4">Order No. <b><?php echo htmlspecialchars($orderID, ENT_QUOTES, 'utf-8') ?></b></div>
            </div>
        </nav>

        <nav style=" top: 121px;" class="navbar navbar-default headersNav  navbar-fixed-top  " role="navigation">
            <div style="border-bottom: 6px solid #33C4B3;" class="row">
                <div class="col-md-2 col-sm-2">

                </div>
                <div class="col-md-8 col-sm-4">
                    <div class="row">
                        <div class="listHeader text-left col-md-6">
                            Drink/Food <img style="height: 50px; width:auto;" src="../images/drinkiconlightblue.png"> <img style="height: 50px; width:auto;" src="../images/forkknifelightblue.png">
                        </div>
                        <div class="listHeader  text-center col-md-4">
                            Measurement
                        </div>
                        <div align class="listHeader  text-right col-md-2">
                            Quantity
                        </div>
                    </div>
                </div>
            </div>
        </nav>



        <div style="width:100%; margin-top: 20%; margin-bottom: 5%;" class="container">

<?php foreach ($order as $orderRow): ?>
    <?php
    echo '<div style="border-bottom: 6px solid #FA8E4B;" class="row">';
    echo '<div class="col-md-2 col-sm-2">';
    echo '<div style="background-color:transparent; border: none;"  class="well">';
    echo '<img  style="height: 70px; width: auto; float:right;" class="img-responsive" src="../images/' . htmlspecialchars($orderRow['img'], ENT_QUOTES, 'utf-8') . '">';
    echo '</div>';
    echo '</div>';
    echo '<div class="col-md-8 col-sm-4">';
    echo '<div class="row">';
    echo '<div class="list bold col-md-6">';
    echo htmlspecialchars($orderRow['name'], ENT_QUOTES, 'utf-8');
    echo '</div>';
    echo '<div class="list bold text-center col-md-4">';
    echo htmlspecialchars($orderRow['typeName'], ENT_QUOTES, 'utf-8');
    echo '</div>';
    echo '<div align class="list bold text-center col-md-2">';
    echo htmlspecialchars($orderRow['quantity'], ENT_QUOTES, 'utf-8');
    echo '</div>';
    echo '<div  style="padding: 0px;" class="col-md-10">';
    echo '<div placeholder="No special instructions"  style="padding: 5px; border-radius: 0px; background-color: #64686d; border-color: #AEB7C2; color: #FA8E4B; font-size: 20px;" class="bold text-center well">';
			if ($orderRow['specialRequests'] == null) {echo "NO SPECIAL REQUEST"; } else {echo htmlspecialchars($orderRow['specialRequests'], ENT_QUOTES, 'utf-8');}
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    ?>
            <?php endforeach; ?>

        </div>


        <div class="footer">
            <div style="font-size: 30px; " class=" backbutton navbar-brand navbar-brand-left">
                <span class="glyphicon glyphicon-chevron-left"></span>Back</div>
        </div>
        <script src="../js/viewOrder.js"></script>
        <script type="text/javascript">

            $('.backbutton').click(function () {
                window.location = document.referrer;
            });

        </script>

    </body>

</html>